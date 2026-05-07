<?php
/**
 * Helper Class
 *
 * @package  MANCA\CoolCA\Helper
 */

namespace MANCA\CoolCA\Helper;

/**
 * Helper Main Class
 */
class Helper {
	use WooTrait;
	use CASettingTrait;
	use CABranchTrait;
	use CACostTrait;
	use AssetTrait;
	use SettingsTrait;
	use DebugTrait;
	use LoggerTrait;
	use TemplateTrait;
	use ArrayTrait;

	/**
	 * Returns an url pointing to the main filder of the plugin assets
	 *
	 * @param string $string_value string input.
	 * @return string
	 */
	public static function remove_special_chars( $string_value = '' ) {

		$string_value = str_replace( 'á', 'a', $string_value );
		$string_value = str_replace( 'é', 'e', $string_value );
		$string_value = str_replace( 'í', 'i', $string_value );
		$string_value = str_replace( 'ó', 'o', $string_value );
		$string_value = str_replace( 'ú', 'u', $string_value );
		$string_value = str_replace( 'ñ', 'n', $string_value );
		$string_value = str_replace( 'Á', 'A', $string_value );
		$string_value = str_replace( 'É', 'E', $string_value );
		$string_value = str_replace( 'Í', 'I', $string_value );
		$string_value = str_replace( 'Ó', 'O', $string_value );
		$string_value = str_replace( 'Ú', 'U', $string_value );
		$string_value = str_replace( 'Ñ', 'N', $string_value );
		$string_value = preg_replace( '[^A-Za-z0-9 ;.@_]', ' ', $string_value );
		$string_value = iconv( 'utf-8', 'ascii//TRANSLIT', $string_value );
		$string_value = str_replace( '(', ' ', $string_value );
		$string_value = str_replace( ')', ' ', $string_value );
		$string_value = str_replace( "'", ' ', $string_value );
		$string_value = str_replace( '"', ' ', $string_value );
		$string_value = str_replace( '[', ' ', $string_value );
		$string_value = str_replace( ']', ' ', $string_value );
		$string_value = str_replace( '{', ' ', $string_value );
		$string_value = str_replace( '}', ' ', $string_value );
		$string_value = str_replace( '#', ' ', $string_value );
		$string_value = str_replace( '?', ' ', $string_value );
		$string_value = str_replace( ',', ' ', $string_value );
		$string_value = str_replace( '/', ' ', $string_value );
		$string_value = str_replace( '-', ' ', $string_value );
		$string_value = str_replace( ':', ' ', $string_value );
		$string_value = str_replace( '  ', ' ', $string_value );

		return $string_value;
		/**
		 * Commented, FIXIT.
		 * return utf8_encode($string_value);
		 */
	}
}
