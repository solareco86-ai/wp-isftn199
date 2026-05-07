<?php
/**
 * Settings Trait
 *
 * @package  MANCA\CoolCA\Helper
 */

namespace MANCA\CoolCA\Helper;

trait ArrayTrait {

	/**
	 * Encode Array
	 *
	 * @param array $input Array to be encode.
	 * @return string
	 */
	public static function encode_array( $input = array() ) {
		return base64_encode( gzcompress( wp_json_encode( $input, true ), 9 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * Decode Array
	 *
	 * @param string $input String to be decoded.
	 * @return array
	 */
	public static function decode_array( $input = '' ) {
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		return json_decode( gzuncompress( base64_decode( $input ) ), true );
	}
}
