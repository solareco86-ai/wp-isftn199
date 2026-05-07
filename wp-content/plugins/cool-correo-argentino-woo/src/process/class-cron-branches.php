<?php
/**
 * Cool CA Cron to Update Branches Class
 *
 * @package  MANCA\CoolCA\Process
 */

namespace MANCA\CoolCA\Process;

use MANCA\CoolCA\Helper\Helper;
defined( 'ABSPATH' ) || exit;

/**
 * Cron Processor's Main Class
 */
class CronBranches {


	/**
	 * Run Cron Action
	 */
	public static function run_cron() {
		Helper::log( 'Init ' . __CLASS__ . '.' . __FUNCTION__ );
		$lastRun = get_option( 'coolca-branches-cron-last-run' );
		$now     = get_date_from_gmt( 'now', 'Y-m-d H:i:s' );

		update_option( 'coolca-branches-cron-last-run', $now, 'yes' );
		self::call_action();
		Helper::log( 'End ' . __CLASS__ . '.' . __FUNCTION__ );
	}

	/**
	 * Call Action
	 *
	 * @return void
	 */
	public static function call_action() {

		$result = wp_remote_post(
			admin_url( 'admin-ajax.php' ),
			array(
				'method'      => 'POST',
				'timeout'     => 10,
				'redirection' => 5,
				'blocking'    => false,
				'headers'     => array(),
				'body'        => array(
					'action'       => 'coolca_action_update_branches',
					'coolca_nonce' => wp_create_nonce( 'coolca-branch-cron' ),
				),

			)
		);
	}

	/**
	 * Add New Schedule Time
	 *
	 * @param array $schedules Array of Defined Schedules.
	 * @return array
	 */
	public static function add_schedule( $schedules ) {
		/*every minute*/
		$schedules['coolca_branches'] = array(
			'interval' => 1 * 60 * 60 * 24, // 1 day.
			'display'  => __( 'a diario', 'cool-ca' ),
		);
		return $schedules;
	}
}
