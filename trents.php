<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fiverr.com/junaidzx90
 * @since             1.0.0
 * @package           Truck_Rents
 *
 * @wordpress-plugin
 * Plugin Name:       Truck Rents
 * Plugin URI:        https://www.fiverr.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            junaidzx90
 * Author URI:        https://www.fiverr.com/junaidzx90
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       trents
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Status
 * 1. applied
 * 2. active
 * 3. pending_for_cancel
 * 3. pending_for_finish
 * 4. cancelled
 * 5. complete
 * 6. pending_payment
 * 7. paid
 * /

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'tRENTS_VERSION', '1.0.0' );
define( 'tRENTS_ROOT_URL', plugin_dir_url( __FILE__ ) );

add_image_size( 'truckimg', 80, 80, true );

date_default_timezone_set(get_option('timezone_string')?get_option('timezone_string'):'UTC');

add_action( "init", function(){
	if(current_user_can( 'client' ) || current_user_can( 'driver' )){
		add_filter( 'show_admin_bar', '__return_false' );
	}
} );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-trents-activator.php
 */
function activate_truck_rents() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-trents-activator.php';
	Truck_Rents_Activator::activate();
}

$globalError = null; // All errors comes here

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-trents-deactivator.php
 */
function deactivate_truck_rents() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-trents-deactivator.php';
	Truck_Rents_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_truck_rents' );
register_deactivation_hook( __FILE__, 'deactivate_truck_rents' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-trents.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_truck_rents() {

	$plugin = new Truck_Rents();
	$plugin->run();

}

add_image_size( 'truck_image', 80, 80, true );

function englishToBanglaNumber($words){
	$string = str_replace("0", "০", $words);
	$string = str_replace("1", "১", $string);
	$string = str_replace("2", "২", $string);
	$string = str_replace("3", "৩", $string);
	$string = str_replace("4", "৪", $string);
	$string = str_replace("5", "৫", $string);
	$string = str_replace("6", "৬", $string);
	$string = str_replace("7", "৭", $string);
	$string = str_replace("8", "৮", $string);
	$string = str_replace("9", "৯", $string);
	$string = str_replace("AM", "সকাল", $string);
	$string = str_replace("PM", "বিকাল", $string);
	return $string;
}

function get_payment_status($status){
	switch ($status) {
		case 'pending_payment':
			return "Pending";
			break;
		case 'paid':
			return "Paid";
			break;
		default:
			return "Unpaid";	
			break;
	}
}

function get_total_cost_with_vat($vat, $amount){
	$vats = intval($vat);
	$total = intval($amount);
	return $vats/100*$total+$total;
}

function get_goods_type($type){
	$types = '';
	switch ($type) {
		case 'goods-1':
			$types = 'বাসা পরিবর্তন';
			break;
		case 'goods-2':
			$types = 'বালু';
			break;
		case 'goods-3':
			$types = 'কাঁচামাল';
			break;
		case 'goods-4':
			$types = 'অন্যান্য';
			break;
	}
	return $types;
}

function time_elapsed_string($date) {
	$timestamp = strtotime($date);	
	   
	$strTime = array("second", "minute", "hour", "day", "month", "year");
	$length = array("60","60","24","30","12","10");

	$currentTime = strtotime(date("Y/m/d h:i:s"));
	if($currentTime >= $timestamp) {
		$diff     = $currentTime - $timestamp;
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
			$diff = $diff / $length[$i];
		}

		$diff = round($diff);
		$returns = $diff . " " . $strTime[$i];
		if($diff > 1){
			return $returns.'\'s ago';
		}else{
			return $returns.' ago';
		}
	}
}
run_truck_rents();
