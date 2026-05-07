<?php
/**
 * Settings Trait
 *
 * @package  MANCA\CoolCA\Helper
 */

namespace MANCA\CoolCA\Helper;

trait AssetTrait {

	/**
	 * Gets Assets Folder URL
	 *
	 * @return string
	 */
	public static function get_assets_folder_url() {
		return plugin_dir_url( \CoolCA::MAIN_FILE ) . 'assets';
	}
}
