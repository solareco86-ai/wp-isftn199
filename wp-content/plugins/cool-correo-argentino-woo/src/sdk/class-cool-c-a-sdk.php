<?php
/**
 * Cool CA SDK Class
 *
 * @package  MANCA\CoolCA\SDK
 */

namespace MANCA\CoolCA\SDK;

use MANCA\CoolCA\Helper\Helper;
defined( 'ABSPATH' ) || exit;

/**
 * A main class that holds all our settings logic
 */
class CoolCASdk {

	const CA_CI_OPTION         = 'coolca_ci';
	const CA_TKN_OPTION        = 'coolca_tkn';
	const CA_EXPIRES_AT_OPTION = 'coolca_tkn_expires_at';

	/**
	 * Api Key for Coolca
	 *
	 * @var api_key
	 */
	private string $api_key;

	/**
	 * Customer ID for CA
	 *
	 * @var ca_customer_id
	 */
	public $ca_customer_id;

	/**
	 * Token for CA
	 *
	 * @var ca_token
	 */
	public $ca_token;

	/**
	 * CA Token Expire At
	 *
	 * @var ca_token_expire_at
	 */
	public $ca_token_expire_at;

	/**
	 * Constructor
	 *
	 * @param string $api_key API Key for Coolca.
	 */
	public function __construct( string $api_key = '' ) {
		$this->api_key            = $api_key;
		$this->ca_customer_id     = '';
		$this->ca_token           = '';
		$this->ca_token_expire_at = 0;

		$this->fill_ca_attributes();
		if ( true === $this->should_refresh_token_ca() ) {
			$this->refresh_token_ca();
			$this->fill_ca_attributes();
		}
	}

	/**
	 * Fill CA Attributes
	 */
	public function fill_ca_attributes() {
		$cust_id = '';
		$hashci  = get_option( self::CA_CI_OPTION, '' );
		if ( ! empty( $hashci ) ) {
			// $hashci is obfuscated. That's why we need to decode it.
			$cust_id = gzuncompress( base64_decode( $hashci ) ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		}
		$this->ca_customer_id = $cust_id;
		$this->ca_token       = get_option( self::CA_TKN_OPTION, '' );
		$expires_at           = get_option( self::CA_EXPIRES_AT_OPTION, strtotime( 'now' ) );
		if ( empty( $expires_at ) ) {
			$expires_at = strtotime( 'now' );
		}
		$this->ca_token_expire_at = intval( $expires_at );
	}

	/**
	 * Get Request Header
	 *
	 * @return array
	 */
	private function get_header() {
		$url = wp_parse_url( get_bloginfo( 'url' ) );
		return array(
			'Content-Type'    => 'application/json',
			'x-coolcawebsite' => $url['host'],
			// phpcs:ignore Generic.Commenting.Todo.TaskFound
			// TODO FIX DEBUG Authorization?
			'Authorization'   => 'Bearer ' . $this->api_key,
		);
	}

	/**
	 * Get CA Request Header
	 *
	 * @return array
	 */
	private function get_ca_header() {
		return array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->ca_token,
		);
	}

	/**
	 * Set Request Args
	 *
	 * @return array
	 */
	private function set_args() {
		$args            = array();
		$args['timeout'] = Helper::get_option( 'timeout', 10 );
		$args['headers'] = $this->get_header();
		return $args;
	}

	/**
	 * Set CA Request Args
	 *
	 * @return array
	 */
	private function set_ca_args() {
		$args            = array();
		$args['timeout'] = Helper::get_option( 'timeout', 10 );
		$args['headers'] = $this->get_ca_header();
		return $args;
	}

	/**
	 * Get Base URL for API
	 *
	 * @return string
	 */
	private function get_base_url() {
		return 'https://coolca.manca.com.ar/api/ca';
	}

	/**
	 * Get Base URL for API
	 *
	 * @return string
	 */
	private function get_ca_base_url() {
		return 'https://api.correoargentino.com.ar/micorreo/v1';
	}

	/**
	 * Execute Request
	 *
	 * @param string $url Set URL for send the request to.
	 * @param array  $args Array of arguments for Request.
	 * @param bool   $log_response Determine if response should be logged or not.
	 *
	 * @return array
	 */
	private function exec_request( $url = '', $args = array(), $log_response = true ) {

		Helper::log( str_repeat( '-', 40 ) );
		Helper::log( $url );

		Helper::log( 'Request > ' );
		foreach ( $args as $key => $data ) {
			Helper::log( $key . ':' );
			Helper::log( $data );
		}

		$request = wp_safe_remote_request( $url, $args );

		if ( is_wp_error( $request ) ) {
			Helper::log( 'ERROR' );
			Helper::log( $request );
			return false;
		}

		$response = wp_remote_retrieve_body( $request );

		if ( $log_response ) {
			Helper::log( 'Response > ' );
			Helper::log( $response );
		}

		return $response;
	}

	/**
	 * Refresh Token CA
	 */
	public function refresh_token_ca() {
		$url            = $this->get_base_url() . '/token';
		$args           = $this->set_args();
		$args['method'] = 'GET';

		$response = $this->exec_request( $url, $args );
		if ( $response ) {
			$json = json_decode( $response, true );
			if ( isset( $json['token'] ) ) {
				update_option( self::CA_TKN_OPTION, $json['token'] );
			}
			if ( isset( $json['customer_id'] ) ) {
				update_option( self::CA_CI_OPTION, $json['customer_id'] );
			}
			if ( isset( $json['expires_in'] ) ) {
				update_option( self::CA_EXPIRES_AT_OPTION, strtotime( 'now + ' . $json['expires_in'] . ' minutes' ) );
			}
		}
	}

	/**
	 * Shoud Refres CA Token?
	 *
	 * @return bool
	 */
	public function should_refresh_token_ca() {
		return ( empty( $this->ca_token ) || empty( $this->ca_customer_id ) || empty( $this->ca_token_expire_at ) || ( intval( strtotime( 'now' ) ) >= intval( $this->ca_token_expire_at ) ) );
	}

	/**
	 * Get prices
	 *
	 * @param array $data Array of data to ask rates for.
	 *
	 * @return bool|int
	 */
	public function get_prices( $data = array() ) {
		$url            = $this->get_ca_base_url() . '/rates';
		$args           = $this->set_ca_args();
		$args['method'] = 'POST';
		// phpcs:ignore Generic.Commenting.Todo.TaskFound
		// TODO: fix $data is empty array.
		$body         = array(
			'customerId'            => $this->ca_customer_id,
			'postalCodeOrigin'      => isset( $data['postalCodeOrigin'] ) ? $data['postalCodeOrigin'] : '',
			'postalCodeDestination' => isset( $data['postalCodeDestination'] ) ? $data['postalCodeDestination'] : '',
			'deliveredType'         => ( 'classic' === $data['deliveredType'] || 'express' === $data['deliveredType'] ) ? 'D' : 'S',
			'dimensions'            => array(
				'weight' => isset( $data['weight'] ) ? $data['weight'] : '',
				'height' => isset( $data['height'] ) ? $data['height'] : '',
				'width'  => isset( $data['width'] ) ? $data['width'] : '',
				'length' => isset( $data['length'] ) ? $data['length'] : '',
			),
		);
		$args['body'] = wp_json_encode( $body, JSON_UNESCAPED_UNICODE );

		$transient_key   = 'coolca_rates_get_prices_23' . md5( wp_json_encode( $body ) );
		$cached_response = get_transient( $transient_key );
		$json            = '';

		if ( false !== $cached_response ) {
			Helper::log( 'Request > ' );
			foreach ( $args as $key => $arg_data ) {
				Helper::log( $key . ':' );
				Helper::log( $arg_data );
			}
			Helper::log( 'Cached Response' );
			Helper::log( $cached_response );
			$json = json_decode( $cached_response, true );
		} else {
			$response = $this->exec_request( $url, $args );
			if ( $response ) {
				set_transient( $transient_key, $response, 10 * MINUTE_IN_SECONDS );
				$json = json_decode( $response, true );
			}
		}

		if ( ! empty( $json ) ) {
			if ( isset( $json['rates'] ) ) {
				foreach ( $json['rates'] as $quote ) {
					Helper::log( 'Quote' );
					Helper::log( $quote );
					Helper::log( $data['deliveredType'] );
					Helper::log( $quote['productType'] );
					Helper::log( 'end' );
					if ( 'CP' === $quote['productType'] && ( 'classic' === $data['deliveredType'] || 'branch' === $data['deliveredType'] ) ) {
						return $quote['price'];
					}
					if ( 'EP' === $quote['productType'] && ( 'express' === $data['deliveredType'] || 'branch-express' === $data['deliveredType'] ) ) {
						return $quote['price'];
					}
				}
			}
		}

		return false;
	}
}
