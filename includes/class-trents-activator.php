<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Truck_Rents
 * @subpackage Truck_Rents/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Truck_Rents
 * @subpackage Truck_Rents/includes
 * @author     junaidzx90 <admin@easeare.com>
 */
class Truck_Rents_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
		$job_progression = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}job_progression` (
			`ID` INT NOT NULL AUTO_INCREMENT,
			`job_id` INT NOT NULL,
			`driver_id` INT NOT NULL,
			`client_id` INT NOT NULL,
			`rent_cost` FLOAT NOT NULL,
			`vat` INT NOT NULL,
			`application` VARCHAR(555) NOT NULL,
			`status` VARCHAR(55) NOT NULL,
			`deal_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`ID`)) ENGINE = InnoDB";
		dbDelta($job_progression);

		$applications_canceled = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}applications_canceled` (
			`ID` INT NOT NULL AUTO_INCREMENT,
			`application_id` INT NOT NULL,
			`cancelled_by` INT NOT NULL,
			`reason` VARCHAR(555) NOT NULL,
			`cancel_status` VARCHAR(55) NOT NULL,
			`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`ID`)) ENGINE = InnoDB";
		dbDelta($applications_canceled);

		$payment_history = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}payment_history` (
			`ID` INT NOT NULL AUTO_INCREMENT,
			`progression_id` INT NOT NULL,
			`payment_status` VARCHAR(55) NOT NULL,
			`transiction` VARCHAR(155) NOT NULL,
			`amount` FLOAT NOT NULL,
			`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`ID`)) ENGINE = InnoDB";
		dbDelta($payment_history);
	}

}
