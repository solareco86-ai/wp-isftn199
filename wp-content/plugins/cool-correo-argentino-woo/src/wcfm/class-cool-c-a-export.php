<?php
/**
 * WCFM CoolCA Export Class
 *
 * @package  MANCA\CoolCA\WCFM
 */

namespace MANCA\CoolCA\WCFM;

use MANCA\CoolCA\Export\Main;

/**
 * WCFM CoolCA Export Class
 */
class CoolCAExport {

	/**
	 * WCFM CoolCA Export Endpoint
	 *
	 * @var string
	 */
	public $wcfm_coolca_export_endpoint = 'coolca-export';

	/**
	 * Constructor
	 */
	public function __construct() {
		global $WCFM, $WCFMu;

		$wcfm_myac_modified_endpoints      = wcfm_get_option( 'wcfm_myac_endpoints', array() );
		$this->wcfm_coolca_export_endpoint = ! empty( $wcfm_myac_modified_endpoints['coolca-export'] ) ? $wcfm_myac_modified_endpoints['coolca-export'] : 'coolca-export';

		add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_coolca_export_query_vars' ), 20 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_coolca_export_endpoint_title' ), 20, 2 );
		add_action( 'init', array( &$this, 'wcfm_coolca_export_init' ), 20 );
		add_filter( 'wcfm_endpoints_slug', array( $this, 'coolca_export_wcfm_endpoints_slug' ) );
		add_filter( 'wcfm_menus', array( &$this, 'wcfm_coolca_export_menus' ), 30 );
		add_action( 'wcfm_load_views', array( &$this, 'load_views' ), 30 );
	}

	/**
	 * Support Query Var
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	public function wcfm_coolca_export_query_vars( $query_vars ) {
		$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );

		$query_coolca_export_vars = array(
			'wcfm-coolca-export' => ! empty( $wcfm_modified_endpoints['wcfm-coolca-export'] ) ? $wcfm_modified_endpoints['wcfm-coolca-export'] : 'coolca-export',
		);

		$query_vars = array_merge( $query_vars, $query_coolca_export_vars );

		return $query_vars;
	}

	/**
	 * Support End Point Title
	 *
	 * @param string $title Title.
	 * @param string $endpoint Endpoint.
	 * @return string
	 */
	public function wcfm_coolca_export_endpoint_title( $title, $endpoint ) {
		global $wp;
		switch ( $endpoint ) {
			case 'wcfm-coolca-export':
				$title = __( 'Correo Argentino', 'coolca' );
				break;
		}

		return $title;
	}

	/**
	 * Support Endpoint Intialize
	 *
	 * @return void
	 */
	public function wcfm_coolca_export_init() {
		global $WCFM_Query;

		// Intialize WCFM End points.
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();

		add_rewrite_endpoint( $this->wcfm_coolca_export_endpoint, EP_ROOT | EP_PAGES );

		if ( ! get_option( 'coolca_wcfm_updated_end_point_coolca_export' ) ) {
			// Flush rules after endpoint update.
			flush_rewrite_rules();
			update_option( 'coolca_wcfm_updated_end_point_coolca_export', 1 );
		}
	}

	/**
	 * Support Endpoiint Edit
	 *
	 * @param array $endpoints Endpoints.
	 * @return array
	 */
	public function coolca_export_wcfm_endpoints_slug( $endpoints ) {

		$support_endpoints = array(
			'wcfm-coolca-export' => 'coolca-export',
		);

		$endpoints = array_merge( $endpoints, $support_endpoints );

		return $endpoints;
	}

	/**
	 * WCFM Support Menu
	 *
	 * @param array $menus Menus.
	 * @return array
	 */
	public function wcfm_coolca_export_menus( $menus ) {
		global $WCFM;

		$menus = array_slice( $menus, 0, 3, true ) +
												array(
													'wcfm-coolca-export' => array(
														'label' => __( 'Correo Argentino', 'coolca' ),
														'url'      => $this->wcfm_coolca_export_url(),
														'icon'     => 'truck',
														'priority' => 3.2,
													),
												) +
													array_slice( $menus, 3, count( $menus ) - 3, true );
		return $menus;
	}

	/**
	 * Support Views
	 *
	 * @param string $end_point End point.
	 * @return void
	 */
	public function load_views( $end_point ) {
		global $WCFM, $WCFMu;

		switch ( $end_point ) {
			case 'wcfm-coolca-export':
				?>			
				<div class="collapse wcfm-collapse" id="wcfm_support_listing">
					<div class="wcfm-page-headig">
						<span class="wcfmfa fa-life-ring"></span>
						<span class="wcfm-page-heading-text"><?php esc_html_e( 'Correo Argentino Exportación', 'coolca' ); ?></span>												
						<?php
						/**
						 * Hook: wcfm_page_heading
						 *
						 * @since 1.4.0
						 * @hooked wcfm_page_heading_wrap_start - 10
						 */
						do_action( 'wcfm_page_heading' );
						?>
					</div>
					<div class="" style="padding: 10px;">
						<?php Main::page_content(); ?>
					</div>
				</div>
				<?php
				break;
		}
	}

	/**
	 * WCFM CoolCA Export URL
	 *
	 * @return string
	 */
	public function wcfm_coolca_export_url() {
		global $WCFM;
		$wcfm_page                  = get_wcfm_page();
		$get_wcfm_coolca_export_url = wcfm_get_endpoint_url( 'wcfm-coolca-export', '', $wcfm_page );

		/**
		 * Filter WCFM CoolCA Export URL
		 *
		 * @since 1.4.0
		 * @param string $get_wcfm_coolca_export_url WCFM CoolCA Export URL.
		 * @return string
		 */
		return apply_filters( 'wcfm_coolca_export_url', $get_wcfm_coolca_export_url );
	}
}
