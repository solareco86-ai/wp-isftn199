<?php
/**
 * Class DebugTrait
 *
 * @package  MANCA\CoolCA\Helper
 */

namespace MANCA\CoolCA\Helper;

/**
 * Database Trait
 */
trait DebugTrait {

	/**
	 * Log data if debug is enabled
	 *
	 * @param strin $log String to write in Log.
	 */
	public static function log( $log ) {
		if ( self::get_option( 'debug' ) !== 'no' ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				self::log_debug( wc_print_r( $log, true ) );
			} else {
				self::log_debug( $log );
			}
		}
	}
}
