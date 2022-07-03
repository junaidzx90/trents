<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Truck_Rents
 * @subpackage Truck_Rents/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Truck_Rents
 * @subpackage Truck_Rents/includes
 * @author     junaidzx90 <admin@easeare.com>
 */
class Truck_Rents {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Truck_Rents_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'tRENTS_VERSION' ) ) {
			$this->version = tRENTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'trents';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		add_filter( 'goodstype', [$this, 'typeofgoods'], 10, 1 );
	}
	
	function typeofgoods($selected){
		?>
		<label for="goodstype">ধরণ নির্বাচন করুন</label>
		<select required name="goodstype" id="goodstype">
			<option>মালের ধরণ</option>
			<option <?php echo (($selected == 'goods-1') ? 'selected' : '') ?> value="goods-1">বাসা পরিবর্তন</option>
			<option <?php echo (($selected == 'goods-2') ? 'selected' : '') ?> value="goods-2">বালু</option>
			<option <?php echo (($selected == 'goods-3') ? 'selected' : '') ?> value="goods-3">কাঁচামাল</option>
			<option <?php echo (($selected == 'goods-3') ? 'selected' : '') ?> value="goods-4">অন্যান্য</option>
		</select>
		<?php
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Truck_Rents_Loader. Orchestrates the hooks of the plugin.
	 * - Truck_Rents_i18n. Defines internationalization functionality.
	 * - Truck_Rents_Admin. Defines all hooks for the admin area.
	 * - Truck_Rents_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-trents-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-trents-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-trents-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-trents-public.php';

		$this->loader = new Truck_Rents_Loader();
		
		if( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}

		// In progress
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-job-progress.php';
		// Drivers
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rents-drivers.php';
		// Applied
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-job-applications.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Truck_Rents_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Truck_Rents_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Truck_Rents_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'trent_jobs_post_type' );
		$this->loader->add_action( 'wp_insert_post_data', $plugin_admin, 'trent_jobs_title_filter',10,3 );
		$this->loader->add_action( 'widgets_init', $plugin_admin, 'trent_job_sidebar' );
		$this->loader->add_action( 'init', $plugin_admin, 'trent_jobs_category' );

		$this->loader->add_action( 'gettext', $plugin_admin, 'overrides_predefined_txts', 10, 4 ); // Overrides predefined texts

		$this->loader->add_action( 'manage_trent_posts_columns', $plugin_admin, 'manage_trent_jobs_columns', 10, 4 );
		$this->loader->add_action( 'manage_trent_posts_custom_column', $plugin_admin, 'manage_trent_jobs_columns_views', 10, 2 ); 
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'trent_jobs_meta_boxes', 99 ); // truckrents meta boxes
		$this->loader->add_action( 'save_post_trent', $plugin_admin, 'save_trent_job_metadata', 99 ); // truckrents meta boxes
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'truck_rents_admin_menu', 99 ); // truckrents application table
		
		// Add roles
		$this->loader->add_action( 'init', $plugin_admin, 'truck_rents_roles', 99 ); // custom roles
		// Delete driver docs
		$this->loader->add_action( 'wp_ajax_reject_driver_profile_docs', $plugin_admin, 'reject_driver_profile_docs' );
		$this->loader->add_action( 'wp_ajax_nopriv_reject_driver_profile_docs', $plugin_admin, 'reject_driver_profile_docs' );
		// Approve payment
		$this->loader->add_action( 'wp_ajax_approve_payment', $plugin_admin, 'approve_payment' );
		$this->loader->add_action( 'wp_ajax_nopriv_approve_payment', $plugin_admin, 'approve_payment' );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Truck_Rents_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		// Page attribute
		$this->loader->add_filter('theme_page_templates', $plugin_public, 'truck_rents_attribute' );
		$this->loader->add_action( 'template_include', $plugin_public, 'author_template_include' );
		// Search locations
		$this->loader->add_action( 'wp_ajax_search_locations', $plugin_public, 'search_locations' );
		$this->loader->add_action( 'wp_ajax_nopriv_search_locations', $plugin_public, 'search_locations' );
		// Get single job details by ajax
		$this->loader->add_action( 'wp_ajax_get_job_details_for_apply', $plugin_public, 'get_job_details_for_apply' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_job_details_for_apply', $plugin_public, 'get_job_details_for_apply' );
		
		// Application submit
		$this->loader->add_action( 'init', $plugin_public, 'submit_application' );
		// Cancel Applied tript
		$this->loader->add_action( 'wp_ajax_cancel_applied_job', $plugin_public, 'cancel_applied_job' );
		$this->loader->add_action( 'wp_ajax_nopriv_cancel_applied_job', $plugin_public, 'cancel_applied_job' );
		// Accept Applied tript
		$this->loader->add_action( 'wp_ajax_accept_applied_job', $plugin_public, 'accept_applied_job' );
		$this->loader->add_action( 'wp_ajax_nopriv_accept_applied_job', $plugin_public, 'accept_applied_job' );
		// Send/Accept delivery tript
		$this->loader->add_action( 'wp_ajax_current_job_finished', $plugin_public, 'current_job_finished' );
		$this->loader->add_action( 'wp_ajax_nopriv_current_job_finished', $plugin_public, 'current_job_finished' );
		// Request running job cancel
		$this->loader->add_action( 'wp_ajax_cancel_requ_running_job', $plugin_public, 'cancel_requ_running_job' );
		$this->loader->add_action( 'wp_ajax_nopriv_cancel_requ_running_job', $plugin_public, 'cancel_requ_running_job' );
		// Cancel request accept
		$this->loader->add_action( 'wp_ajax_accept_cancellation_request', $plugin_public, 'accept_cancellation_request' );
		$this->loader->add_action( 'wp_ajax_nopriv_accept_cancellation_request', $plugin_public, 'accept_cancellation_request' );

		// Submit job
		$this->loader->add_action( 'init', $plugin_public, 'job_submitted_by_client' );

		// Profile Informations
		$this->loader->add_action( 'init', $plugin_public, 'save_profile_informations' );

		// Get due amount
		$this->loader->add_action( 'wp_ajax_get_trip_due_amount', $plugin_public, 'get_trip_due_amount' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_trip_due_amount', $plugin_public, 'get_trip_due_amount' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Truck_Rents_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
