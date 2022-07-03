<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Truck_Rents
 * @subpackage Truck_Rents/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Truck_Rents
 * @subpackage Truck_Rents/admin
 * @author     junaidzx90 <admin@easeare.com>
 */
class Truck_Rents_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Truck_Rents_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Truck_Rents_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'leafletjs', 'http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'esri-leaflet-geocoder', 'https://cdn-geoweb.s3.amazonaws.com/esri-leaflet-geocoder/0.0.1-beta.5/esri-leaflet-geocoder.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/trents-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Truck_Rents_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Truck_Rents_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 * 
		 */
		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/trents-admin.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'trents', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'trent_nonce' )
		) );

	}

	function trent_jobs_post_type(){
		$labels = array(
			'name'                  => _x( 'tRents', 'Post type general name', 'trents' ),
			'singular_name'         => _x( 'Jobs', 'Post type singular name', 'trents' ),
			'menu_name'             => _x( 'tRents', 'Admin Menu text', 'trents' ),
			'name_admin_bar'        => _x( 'Jobs', 'Add job', 'trents' ),
			'add_new'               => __( 'Add job', 'trents' ),
			'add_new_item'          => __( 'Add job', 'trents' ),
			'new_item'              => __( 'New job', 'trents' ),
			'edit_item'             => __( 'Edit job', 'trents' ),
			'view_item'             => __( 'View job', 'trents' ),
			'all_items'             => __( 'All jobs', 'trents' ),
			'search_items'          => __( 'Search jobs', 'trents' ),
			'parent_item_colon'     => __( 'Parent jobs:', 'trents' ),
			'not_found'             => __( 'No jobs found.', 'trents' ),
			'not_found_in_trash'    => __( 'No jobs found in Trash.', 'trents' )
		);     
		$args = array(
			'labels'             => $labels,
			'description'        => 'tRents post type.',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'trent' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_icon'       	 => 'dashicons-money',
			'menu_position'      => 20,
			'supports'           => array(  ),
			'taxonomies'         => array( 'trucks' ),
			'show_in_rest'       => false
		);
		  
		register_post_type( 'trent', $args );
	}

	// Job sidebar
	function trent_job_sidebar() {
		register_sidebar(
			array (
				'name' => __( 'Job Sidebar', 'trents' ),
				'id' => 'trent-job-sidebar',
				'description' => __( 'Job Sidebar', 'trents' ),
				'before_widget' => '<div class="job-sidebar"><div class="job-widget">',
				'after_widget' => "</div></div>",
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			)
		);
	}
	

	function trent_jobs_category(){
		$labels = array(
			'name'              => _x( 'Trucks', 'taxonomy general name', 'trents' ),
			'singular_name'     => _x( 'Truck', 'taxonomy singular name', 'trents' ),
			'search_items'      => __( 'Search Trucks', 'trents' ),
			'all_items'         => __( 'All Trucks', 'trents' ),
			'parent_item'       => __( 'Parent Truck', 'trents' ),
			'parent_item_colon' => __( 'Parent Truck:', 'trents' ),
			'edit_item'         => __( 'Edit Truck', 'trents' ),
			'update_item'       => __( 'Update Truck', 'trents' ),
			'add_new_item'      => __( 'Add New Truck', 'trents' ),
			'new_item_name'     => __( 'New Truck Name', 'trents' ),
			'menu_name'         => __( 'Trucks', 'trents' ),
		);
	 
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'trucks' ),
		);
	 
		register_taxonomy( 'trucks', array( 'trent' ), $args );
	}

	function trent_terms($post_id, $taxonomy){
		$term_list = get_the_term_list( $post_id, $taxonomy, '', ', ' );
		return apply_filters( 'the_terms', $term_list, $taxonomy, '', ', ' );
	}
	
	function overrides_predefined_txts($translation, $text, $domain){
		global $post;
		if (get_post_type( $post ) == 'trent') {
			if ( $text == 'Publish' )
				return 'Create Job';
			if ( $text == 'Update' )
				return 'Update Job';
			if ( $text == 'Post updated.' )
				return 'Job Updated.';
			if ( $text == 'Post published.' )
				return 'Job Created.';
			if ( $text == 'Add title' )
				return 'Job Title';
			return $translation;
		}
		
		return $translation;
	}

	// Manage table columns
	function manage_trent_jobs_columns($columns) {
		unset(
			$columns['subscribe-reloaded'],
			$columns['title'],
			$columns['taxonomy-trucks'],
			$columns['date']
		);
	
		$new_columns = array(
			'title' => __('Job ID', 'trents'),
			'job_author' => __('Owner', 'trents'),
			'loadloc' => __('Load Location', 'trents'),
			'unloadloc' => __('Unload Location', 'trents'),
			'loadtime' => __('Load Time', 'trents'),
			'date' => __('Added', 'trents'),
		);
	
		return array_merge($columns, $new_columns);
	}

	// View custom column data
	function manage_trent_jobs_columns_views($column_id, $post_id){
		switch ($column_id) {
			case 'loadloc':
				echo get_post_meta($post_id, 'tr_load_location', true );
				break;
			case 'job_author':
				$post_author_id = get_post_field( 'post_author', $post_id );
				echo get_user_by( "ID", $post_author_id )->display_name;
				echo '<p><a href="tel:'.get_user_meta($post_author_id, 'user_phone', true ).'">'.get_user_meta($post_author_id, 'user_phone', true ).'</a></p>';
				break;
			case 'unloadloc':
				echo get_post_meta($post_id, 'tr_unload_location', true );
				break;
			case 'loadtime':
				echo date("Y/m/d - h:i:s a", strtotime(get_post_meta($post_id, 'tr_load_datetime', true)));
				break;
			
			default:
				# code...
				break;
		}
	}
	
	// Admin menu
	function truck_rents_admin_menu(){
		add_submenu_page( 'edit.php?post_type=trent', 'Applications', 'Applications', 'manage_options', 'applications', [$this, 'applications_menupage']);
		add_submenu_page( 'edit.php?post_type=trent', 'Progression', 'Progression', 'manage_options', 'progression', [$this, 'inprogress_menupage']);
		add_submenu_page( 'edit.php?post_type=trent', 'Drivers', 'Drivers', 'manage_options', 'drivers', [$this, 'truckrents_drivers_menupage']);
		add_submenu_page( 'edit.php?post_type=trent', 'Settings', 'Settings', 'manage_options', 'job-settings', [$this, 'truckrents_settings_menupage']);

		add_settings_section( 'trent_job_setting_section', '', '', 'trent_job_setting_page' );
		// Job Page Sidebar
		add_settings_field( 'jobpagesidebar', 'Job Page Sidebar', [$this, 'jobpagesidebar_cb'], 'trent_job_setting_page','trent_job_setting_section' );
		register_setting( 'trent_job_setting_section', 'jobpagesidebar' );
		// Vat
		add_settings_field( 'company_vat', 'Company Vat', [$this, 'company_vat_cb'], 'trent_job_setting_page','trent_job_setting_section' );
		register_setting( 'trent_job_setting_section', 'company_vat' );
		// Logo
		add_settings_field( 'profile_logo', 'Profile Logo', [$this, 'profile_logo_cb'], 'trent_job_setting_page','trent_job_setting_section' );
		register_setting( 'trent_job_setting_section', 'profile_logo' );
		// Welcome heading
		add_settings_field( 'welcome_heading', 'Welcome Heading', [$this, 'welcome_heading_cb'], 'trent_job_setting_page','trent_job_setting_section' );
		register_setting( 'trent_job_setting_section', 'welcome_heading' );
		// Welcome text
		add_settings_field( 'welcome_text', 'Welcome Text', [$this, 'welcome_text_cb'], 'trent_job_setting_page','trent_job_setting_section' );
		register_setting( 'trent_job_setting_section', 'welcome_text' );
		// Eiconbd API
		add_settings_field( 'eiconbd_api_key', 'Eiconbd API', [$this, 'eiconbd_api_key_cb'], 'trent_job_setting_page','trent_job_setting_section' );
		register_setting( 'trent_job_setting_section', 'eiconbd_api_key' );
		// Comapany bkash number
		add_settings_field( 'company_bkash', 'Comapany bkash number', [$this, 'company_bkash_cb'], 'trent_job_setting_page','trent_job_setting_section' );
		register_setting( 'trent_job_setting_section', 'company_bkash' );
		// Comapany bkash QR Image
		add_settings_field( 'company_bkash_qr_code', 'Comapany bkash QR Image', [$this, 'company_bkash_qr_code_cb'], 'trent_job_setting_page','trent_job_setting_section' );
		register_setting( 'trent_job_setting_section', 'company_bkash_qr_code' );
	}

	// Jobpage sidebar
	function jobpagesidebar_cb(){
		echo '<select name="jobpagesidebar" id="jobpagesidebar">
			<option '.((get_option('jobpagesidebar') === 'disable') ? 'selected': '').' value="disable">Disable Sidebar</option>
			<option '.((get_option('jobpagesidebar') === 'enable') ? 'selected': '').' value="enable">Enable Sidebar</option>
		</select>';
	}

	// Vat
	function company_vat_cb(){
		echo '<input type="number" name="company_vat" placeholder="5" value="'.get_option('company_vat_cb').'"> %';
	}

	function profile_logo_cb(){
		echo '<div class="logo-wrapp"><img width="100px" id="profile_logo_show" src="'.((get_option('profile_logo')) ? esc_url(get_option( 'profile_logo' )) : '').'" alt="profile_logo"></div>';
		echo '<button id="emaillogo" class="button-secondary">Profile Logo</button>';
		if(get_option('profile_logo')){
			echo '<button id="removeLogo" class="button-secondary">Remove Logo</button>';
		}
		echo '<input type="hidden" name="profile_logo" id="profile_logo" value="'.( get_option('profile_logo') ? get_option('profile_logo') : '').'">';
	}

	function welcome_heading_cb(){
		echo '<input type="text" name="welcome_heading" value="'.get_option('welcome_heading').'" placeholder="শুভ কামনা">';
	}
	function welcome_text_cb(){
		echo '<textarea class="widefat" name="welcome_text" rows="5" id="welcome_text">'.get_option('welcome_text').'</textarea>';
	}
	function eiconbd_api_key_cb(){
		echo '<input type="password" class="widefat" name="eiconbd_api_key" id="eiconbd_api_key" placeholder="API Key" value="'.get_option('eiconbd_api_key').'">';
	}
	function company_bkash_cb(){
		echo '<input type="number" class="widefat" name="company_bkash" id="company_bkash" value="'.get_option('company_bkash').'">';
	}
	function company_bkash_qr_code_cb(){
		echo '<input type="url" class="widefat" name="company_bkash_qr_code" placeholder="Image URL" id="company_bkash_qr_code" value="'.get_option('company_bkash_qr_code').'">';
	}

	// menupage
	function applications_menupage(){
		$applications = new Job_applications();
		?>
		<div class="wrap" id="applications-table">
			<h3 class="heading3">Applications</h3>
			<hr>
			<?php $applications->prepare_items(); ?>
			<?php $applications->display(); ?>
		</div>
		<?php
	}
	// menupage
	function inprogress_menupage(){
		if(isset($_GET['page']) && $_GET['page'] === "progression" && isset($_GET['id']) && !empty($_GET['id'])){
			require_once plugin_dir_path( __FILE__ )."partials/job-view.php";
		}else{
			$jobprogress = new Job_Progress();
			?>
			<div class="wrap" id="jobprogress-table">
				<h3 class="heading3">Progression</h3>
				<hr>
				<?php $jobprogress->prepare_items(); ?>
				<?php $jobprogress->display(); ?>
			</div>
			<?php
		}
	}
	// Drivers
	function truckrents_drivers_menupage(){
		if(isset($_GET['post_type']) 
		&& $_GET['post_type'] === 'trent' 
		&& isset($_GET['page']) 
		&& $_GET['page'] === 'drivers' 
		&& isset($_GET['action']) 
		&& $_GET['action'] === 'manage' && isset($_GET['id'])){
			require_once plugin_dir_path( __FILE__ )."partials/trents-driver-manage.php";
		}else{
			$drivers = new TR_Drivers();
			?>
			<div class="wrap" id="drivers-table">
				<h3 class="heading3">Drivers</h3>
				<hr>
				<?php $drivers->prepare_items(); ?>
				<?php $drivers->display(); ?>
			</div>
			<?php
		}
	}

	// Settings
	function truckrents_settings_menupage(){
		echo '<div class="trent-job-settings">';
		echo '<h3>Settings</h3>';
		echo '<hr>';

		echo '<form style="width: 50%" method="post" action="options.php">';
		echo '<table class="widefat">';
		settings_fields( 'trent_job_setting_section' );
		do_settings_fields('trent_job_setting_page', 'trent_job_setting_section' );
		echo '</table>';
		submit_button(  );
		echo '</form>';		
		echo '</div>';
	}


	// Add meta boxespostcustom
	function trent_jobs_meta_boxes(){
		global $wp_meta_boxes;
		unset($wp_meta_boxes['trent']);
		add_meta_box( 'submitdiv', "Create Job", 'post_submit_meta_box', 'trent', 'side' );
		add_meta_box( 'trucksdiv', "Trucks Type", 'post_categories_meta_box', 'trent', 'side', '', array(
			'taxonomy' => 'trucks'
		) );
		add_meta_box( 'job_author', "Owner", [$this, 'job_author'], 'trent', 'side' );
		add_meta_box( 'loadlocation_box', "Locations", [$this, 'loadlocation_box_location'], 'trent', 'advanced' );
		add_meta_box( 'jobdata', "Job Data", [$this, 'jobdata_location'], 'trent', 'advanced' );
	}
	
	function job_author($post){
		$selected = $post->post_author;
		$authors = get_users( 'role=client' );
		echo '<select class="widefat" name="job_author">';
		echo '<option value="">Select a customer</option>';
		if($authors){
			foreach($authors as $author){
				echo '<option '.(($selected && intval($selected) === $author->ID) ? 'selected': '').' value="'.$author->ID.'">'.$author->display_name.'</option>';
			}
		}
		echo '</select>';
	}
	// Location view
	function loadlocation_box_location($post){
		echo '<div class="location">
			<label for="load_location">লোডের জায়গা</label>
			<input type="text" required name="load_location" id="load_location" class="widefat" value="'.get_post_meta($post->ID, 'tr_load_location', true ).'" placeholder="Address">
		</div>';
		echo '<div class="location">
			<label for="unload_location">আনলোডের জায়গা</label>
			<input type="text" required name="unload_location" id="unload_location" class="widefat" value="'.get_post_meta($post->ID, 'tr_unload_location', true ).'" placeholder="Address">
		</div>';
	}

	function jobdata_location($post){
		echo '<div class="jobdata-inp">';
		apply_filters( 'goodstype', get_post_meta($post->ID, 'tr_goodstype', true));
		echo '</div>';

		echo '<div class="jobdata-inp">';
		echo '<label for="load-datetime">লোডের সময়</label>
		<input required type="datetime-local" value="'.((get_post_meta($post->ID, 'tr_load_datetime', true)) ? date("Y-m-d\TH:i:s", strtotime(get_post_meta($post->ID, 'tr_load_datetime', true))) : '').'" name="load-datetime" id="load-datetime">';
		echo '</div>';

		echo '<div class="jobdata-inp">';
		echo '<label for="jobdescription">মালের বিবরণ</label>';
		echo '<textarea name="jobdescription" id="jobdescription" cols="30" rows="5">'.stripcslashes(get_post_meta($post->ID, 'tr_jobdescription', true)).'</textarea>';
		echo '</div>';
	}

	// Post data filter
	function trent_jobs_title_filter($data, $postarr, $unsanitized_postarr){
		if ( get_post_type( $postarr['ID'] ) == 'trent' ) {
			$data['post_title'] = "Job-".$postarr['ID'];
			if(isset($postarr['job_author'])){
				$data['post_author'] = $postarr['job_author'];
			}
		}
    	return $data;
	}

	// Save metadata
	function save_trent_job_metadata($post_id){
		if(isset($_POST['load_location'])){
			update_post_meta( $post_id, 'tr_load_location', sanitize_text_field( $_POST['load_location'] ) );
		}
		if(isset($_POST['unload_location'])){
			update_post_meta( $post_id, 'tr_unload_location', sanitize_text_field( $_POST['unload_location'] ) );
		}
		if(isset($_POST['goodstype'])){
			update_post_meta( $post_id, 'tr_goodstype', sanitize_text_field( $_POST['goodstype'] ) );
		}
		if(isset($_POST['load-datetime'])){
			update_post_meta( $post_id, 'tr_load_datetime', sanitize_text_field( $_POST['load-datetime'] ) );
		}
		if(isset($_POST['jobdescription'])){
			update_post_meta( $post_id, 'tr_jobdescription', sanitize_text_field( $_POST['jobdescription'] ) );
		}
	}

	// Custom roles
	function truck_rents_roles(){
		if(get_option( 'truck_rents_roles_version' ) < 1){
			add_role( 'driver', 'Driver' );
			add_role( 'client', 'Client' );

			update_option( 'truck_rents_roles_version', 1 );
		}
	}

	// Delete user profile files
	function reject_driver_profile_docs(){
		if(!wp_verify_nonce( $_POST['nonce'], "trent_nonce" )){
			die("Invalid request!");
		}

		if(isset($_POST['driver_id']) && !empty($_POST['driver_id']) && isset($_POST['data_type']) && !empty($_POST['data_type'])){
			$driver_id = intval($_POST['driver_id']);
			$data_type = intval($_POST['data_type']);
			
			$folderPath = get_user_meta( $driver_id, "_profile_docs_upload_dir", true );

			if($folderPath){
				$folderPath = base64_decode($folderPath);
				$files = glob($folderPath . '/*');
				//Loop through the file list.
				$delted = false;
				foreach($files as $file){
					//Make sure that this is a file and not a directory.
					if(is_file($file)){
						//Use the unlink function to delete the file.
						unlink($file);
						$delted = true;
					}
				}

				if($delted){
					rmdir($folderPath);
					
					delete_user_meta($driver_id, "_profile_docs_upload_dir");
					delete_user_meta($driver_id, "driver_docs_type");

					switch ($data_type) {
						case 'nid-card':
							delete_user_meta($driver_id, "nid_card_front_file");
							delete_user_meta($driver_id, "nid_card_back_file");
							break;
						case 'passport':
							delete_user_meta($driver_id, "passport_document_file");
							break;
						case 'driving_license':
							delete_user_meta($driver_id, "driving_document_front_file");
							delete_user_meta($driver_id, "driving_document_back_file");
							break;
					}
				}
			}

			echo json_encode(array("success" => 'Deleted'));
			die;
		}

		echo json_encode(array("success" => 'Deleted'));
		die;
	}

	// Approve payment
	function approve_payment(){
		if(!wp_verify_nonce( $_POST['nonce'], "trent_nonce" )){
			die("Invalid request!");
		}

		if(isset($_POST['payment_id'])){
			$payment_id = intval($_POST['payment_id']);
			global $wpdb;

			$wpdb->update($wpdb->prefix.'payment_history', array(
				'payment_status' => 'paid'
			), array('ID' => $payment_id), array('%s'), array('%d'));

			echo json_encode(array("success" => 'Success'));
			die;
		}
	}
}

