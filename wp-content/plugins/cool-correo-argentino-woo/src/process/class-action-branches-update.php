<?php
/**
 * Cool CA Cron to Update Branches Class
 *
 * @package  MANCA\CoolCA\SDK
 */

namespace MANCA\CoolCA\Process;

use MANCA\CoolCA\Helper\Helper;
use MANCA\CoolCA\Process\CronBranches;
use MANCA\CoolCA\SDK\BranchesSdk;

defined( 'ABSPATH' ) || exit;

/**
 * Cron Processor's Main Class
 */
class ActionBranchesUpdate {

	/**
	 * Run Action
	 *
	 * @return bool
	 */
	public static function execute() {
		$branches = BranchesSdk::get_branches();
		if ( ! empty( $branches ) ) {
			update_option( 'wc-coolca-branches', $branches );

			// Split branches by state.
			$states = Helper::get_states_array();

			foreach ( $states as $state ) {

				$state_branches = array();
				foreach ( $branches as $key => $value ) {
					if ( $state === $value['s'] ) {
						$state_branches[ $key ] = $value;
					}
				}
				update_option( 'wc-coolca-branches-' . $state, $state_branches );
			}
		}

		$now = get_date_from_gmt( 'now', 'Y-m-d H:i:s' );
		update_option( 'coolca-branches-last-run', $now, 'yes' );
		return true;
	}

	/**
	 * Handle Ajax Request
	 *
	 * @return void
	 */
	public static function ajax_callback_wp() {
		Helper::log( 'ajax ' . __CLASS__ . '.' . __FUNCTION__ );
		if ( ! isset( $_POST['coolca_nonce'] ) ) {
			wp_send_json_error();
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['coolca_nonce'] ) ), 'coolca-branch-cron' ) ) {
			wp_send_json_error();
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$ret = static::execute();
		if ( $ret ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

		wp_send_json_success();
	}
}
