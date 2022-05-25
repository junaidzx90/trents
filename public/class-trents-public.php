<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Truck_Rents
 * @subpackage Truck_Rents/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Truck_Rents
 * @subpackage Truck_Rents/public
 * @author     junaidzx90 <admin@easeare.com>
 */
class Truck_Rents_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
		wp_enqueue_style( "jquery-ui", '//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'fontawesome.min', plugin_dir_url( __FILE__ ) . 'css/fontawesome.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/trents-public.css', array(), microtime(), 'all' );

		wp_enqueue_style( 'datatables', 'https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css', array(), $this->version, 'all' );
		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		 */
		
		wp_enqueue_script( "jquery-ui", 'https://code.jquery.com/ui/1.13.0/jquery-ui.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'vuejs', 'https://cdn.jsdelivr.net/npm/vue@2.6.14', array(  ), $this->version, false );
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/trents-public.js', array( 'jquery', 'vuejs' ), $this->version, false );
		wp_register_script( 'trent-jobs', plugin_dir_url( __FILE__ ) . 'js/trent.js', array( 'jquery' ), microtime(), false );

		wp_enqueue_script( 'datatables', 'https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'payment-table', plugin_dir_url( __FILE__ ) . 'js/payment-table.js', array( 'jquery', 'datatables' ), $this->version, false );

		wp_localize_script( 'trent-jobs', 'jobajax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'job_nonce' ),
			'vats' => ((get_option('company_vat_cb')) ? get_option('company_vat_cb') : 5)
		) );

		if( is_author( ) ){
			$author_id = get_queried_object(  )->ID;
			$user_login = get_queried_object(  )->user_login;
			$page = null;
			if(isset($_GET['page'])){
				$page = sanitize_text_field( $_GET['page'] );
			}
			wp_localize_script( $this->plugin_name, 'ajaxrequ', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'job_nonce' ),
				'site_url' => home_url(  ),
				'plugin_url' => tRENTS_ROOT_URL,
				'author_id' => $author_id,
				'user_login' => $user_login,
				'page' => $page
			) );
		}
	}

	public function truck_rents_attribute($templates){ 
        $templates['profile_page'] = 'Profile';
        return $templates;
    }

	function author_template_include($template){
		if( is_author( ) ){
			$author_id = get_queried_object(  )->ID;
			if(is_user_logged_in(  ) && (current_user_can( 'client' ) || current_user_can( 'driver' ) || current_user_can( 'partner' )) && get_current_user_id(  ) === $author_id ){
				require_once 'partials/trents-dashboard.php';
			}else{
				wp_safe_redirect( get_post_type_archive_link( 'trent' ) );
			}
		}else{
			if(get_page_template_slug() === 'profile_page'){
				$theme_files = array('dashboard-page-redirect.php', plugin_dir_path( __FILE__ ).'partials/dashboard-page-redirect.php');
				$exists_in_theme = locate_template($theme_files, false);
				if ( $exists_in_theme != '' ) {
					return $exists_in_theme;
				} else {
					return  plugin_dir_path( __FILE__ ). 'partials/dashboard-page-redirect.php';
				}
			}

			if ( is_post_type_archive('trent') ) {
				$theme_files = array('trent-jobs.php', plugin_dir_path( __FILE__ ).'partials/trent-jobs.php');
				$exists_in_theme = locate_template($theme_files, false);
				if ( $exists_in_theme != '' ) {
					return $exists_in_theme;
				} else {
					return  plugin_dir_path( __FILE__ ). 'partials/trent-jobs.php';
				}
			}

			if(is_singular( 'trent' )){
				global $post;
				$post_id = $post->ID;
				wp_safe_redirect( get_post_type_archive_link( 'trent' )."#job-$post_id" );
				exit;
			}

			if ($template == '') {
				throw new \Exception('No template found');
			}
			return $template;
		}
	}

	// Search location by ajax
	function search_locations(){
		if(!wp_verify_nonce( $_GET['nonce'], 'job_nonce' )){
			echo 'Invalid Request';
			die;
		}

		global $wpdb;
		if(isset($_GET['address'])){
			$address = ucfirst(sanitize_text_field( $_GET['address'] ));
			$locationsObj = $wpdb->get_results("SELECT * FROM bd_locations WHERE `location` LIKE '%$address%'");

			$locations = array();
			if($locationsObj){
				foreach($locationsObj as $location){
					$locations[] = $location->location;
				}
			}
			echo json_encode(array('address' => $locations));
			die;
		}

		die;
	}

	// get job details by ajax
	function get_job_details_for_apply(){
		if(!wp_verify_nonce( $_GET['nonce'], 'job_nonce' )){
			echo 'Invalid Request';
			die;
		}

		if(isset($_GET['job_id']) && is_user_logged_in(  )){
			if(current_user_can( 'driver' )){
				$job_id = intval($_GET['job_id']);
				global $wpdb;
				$job_title = get_the_title( $job_id );

				if(get_user_meta( get_current_user_id(  ), 'verified_account', true ) === 'on'){
					echo json_encode(array('success' => $job_title));
					die;
				}else{
					echo json_encode(array('unverified' => 'Unverified'));
					die;
				}
			}else{
				echo json_encode(array('needtologin' => wp_login_url(  )));
				die;
			}
		}

		echo json_encode(array('needtologin' => wp_login_url(  )));
		die;
	}

	// Submit application
	function submit_application(){
		if(isset($_POST['application__form_btn'])){
			if(isset($_POST['job_id']) && isset($_POST['rent_cost'])){
				$job_id = intval($_POST['job_id']);
				$total_cost = floatval($_POST['rent_cost']);
				$current_vat = ((get_option('company_vat_cb')) ? get_option('company_vat_cb') : 5);
				$driver_id = get_current_user_id(  );
				$application = '';
				if(isset($_POST['application'])){
					$application = sanitize_text_field( $_POST['application'] );
					$application = stripcslashes( $application );
				}
				$job_author_id = get_post($job_id)->post_author;
				
				global $wpdb;
				$mysubmission = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}job_progression WHERE driver_id = $driver_id AND job_id = $job_id");
				
				if(!$mysubmission){
					$wpdb->insert($wpdb->prefix.'job_progression', array(
						'job_id' => $job_id,
						'driver_id' => $driver_id,
						'client_id' => $job_author_id,
						'rent_cost' => $total_cost,
						'vat' => $current_vat,
						'application' => $application,
						'status' => 'applied',
						'created' => date("Y-m-d h:i:s A")
					));

					if($wpdb->insert_id){
						wp_safe_redirect( get_post_type_archive_link( 'trent' )."#job-$job_id" );
						exit;
					}
				}
			}
		}
	}

	// Canscel Applied job
	function cancel_applied_job(){
		if(!wp_verify_nonce( $_POST['nonce'], 'job_nonce' )){
			echo 'Invalid Request';
			die;
		}

		if(isset($_POST['job_id']) && isset($_POST['application_id'])){
			$job_id = intval($_POST['job_id']);
			$application_id = intval($_POST['application_id']);
			if(!$job_id){
				die;
			}
			if(!$application_id){
				die;
			}
			global $wpdb;
			if($wpdb->query("DELETE FROM {$wpdb->prefix}job_progression WHERE job_id = $job_id AND status = 'applied' AND ID = $application_id")){
				echo json_encode(array('deleted' => 'Deleted'));
				die;
			}

			die;
		}
		die;
	}

	// Cancel request running job
	function cancel_requ_running_job(){
		if(!wp_verify_nonce( $_POST['nonce'], 'job_nonce' )){
			echo 'Invalid Request';
			die;
		}
		
		if(isset($_POST['application_id']) && isset($_POST['reason'])){
			$application_id = intval($_POST['application_id']);
			$reason = sanitize_text_field( $_POST['reason'] );

			if(!$application_id){
				die;
			}
			global $wpdb;

			$wpdb->insert($wpdb->prefix.'applications_canceled',array(
				'application_id' => $application_id,
				'reason' => $reason,
				'cancelled_by' => get_current_user_id(  ),
				'cancel_status' => 'pending_for_cancel',
				'created' => date("Y-m-d h:i:s A")
			),array('%d','%s','%d','%s','%s'));

			if(!is_wp_error( $wpdb )){

				// Msg

				echo json_encode(array("success" => "Requested"));
			}
			die;
		}

		die;
	}

	function accept_cancellation_request(){
		if(!wp_verify_nonce( $_POST['nonce'], 'job_nonce' )){
			echo 'Invalid Request';
			die;
		}

		if(isset($_POST['application_id'])){
			$application_id = intval($_POST['application_id']);

			if(!$application_id){
				die;
			}

			global $wpdb;
			$wpdb->update($wpdb->prefix.'job_progression',array(
				'status' => 'cancelled'
			), array('ID' => $application_id), array('%s'), array('%d'));

			$wpdb->update($wpdb->prefix.'applications_canceled',array(
				'cancel_status' => 'cancelled'
			), array('application_id' => $application_id), array('%s'), array('%d'));

			if(!is_wp_error( $wpdb )){

				$cancelled_by = $wpdb->get_var("SELECT cancelled_by FROM {$wpdb->prefix}applications_canceled WHERE application_id = $application_id");

				if($cancelled_by){
					$user_ranks = get_user_meta($cancelled_by, 'user_profile_ranks', true);
					if($user_ranks){
						$user_ranks = intval($user_ranks);
					}else{
						$user_ranks = 100;
					}
	
					if($user_ranks > 5){
						$user_ranks -= 5;
						update_user_meta( $cancelled_by, 'user_profile_ranks', $user_ranks );
					}
				}

				// Msg

				echo json_encode(array("success" => "Cancelled"));
			}
			die;
		}

		die;
	}

	// Accept Applied job
	function accept_applied_job(){
		if(!wp_verify_nonce( $_POST['nonce'], 'job_nonce' )){
			echo 'Invalid Request';
			die;
		}
		
		if(isset($_POST['job_id']) && isset($_POST['application_id'])){
			$job_id = intval($_POST['job_id']);
			$application_id = intval($_POST['application_id']);
			if(!$job_id){
				die;
			}
			if(!$application_id){
				die;
			}
			global $wpdb;
			
			$wpdb->update($wpdb->prefix.'job_progression',array(
				'status' => 'active',
				'deal_date' => date("Y-m-d h:i:s A")
			), array('ID' => $application_id), array('%s', '%s'), array('%d'));

			// Update other request
			$wpdb->update($wpdb->prefix.'job_progression',array(
				'status' => 'expired'
			), array('job_id' => $job_id, 'status' => 'applied'), array('%s'), array('%d','%s'));

			if(!is_wp_error( $wpdb )){
				// SMS Will be sent from here
				$driverId = $wpdb->get_var("SELECT driver_id FROM {$wpdb->prefix}job_progression WHERE ID = $application_id");

				$apiKey = get_option('eiconbd_api_key');
				$driverName = ucfirst(get_user_by( 'ID', $driverId )->display_name);
                $driverPhone = get_user_meta($driverId, 'user_phone', true);
				$jobUniqueId = strtolower(base64_encode($application_id));
				$mesage = "Hi $driverName,\nYour offer ($jobUniqueId) is accepted.";

				if($driverPhone && $apiKey){
					file_get_contents("https://sms.eiconbd.com/services/send.php?key=$apiKey&number=$driverPhone&message=$mesage&option=1&type=sms&prioritize=0");
				}

				echo json_encode(array('updated' => 'Updated'));
				die;
			}

			die;
		}
		die;
	}

	// Accept driver delivery
	function current_job_finished(){
		if(!wp_verify_nonce( $_POST['nonce'], 'job_nonce' )){
			echo 'Invalid Request';
			die;
		}
		$job_id = intval($_POST['job_id']);
		$application_id = intval($_POST['application_id']);
		if(!$job_id){
			die;
		}
		if(!$application_id){
			die;
		}
		global $wpdb;

		$user = get_user_by( 'ID', get_current_user_id(  ) );

        if($user->roles[0] === 'driver' || $user->roles[0] === 'partner'){
			$wpdb->update($wpdb->prefix.'job_progression',array(
				'status' => 'pending_for_finish',
				'deal_date' => date("Y-m-d h:i:s A")
			), array('ID' => $application_id, 'job_id' => $job_id), array('%s', '%s'), array('%d', '%d'));
		}
		if($user->roles[0] === 'client'){
			$wpdb->update($wpdb->prefix.'job_progression',array(
				'status' => 'complete',
				'deal_date' => date("Y-m-d h:i:s A")
			), array('ID' => $application_id, 'job_id' => $job_id), array('%s', '%s'), array('%d', '%d'));
		}

		$repeat = true;
		if(!is_wp_error( $wpdb ) && $repeat){
			$repeat = false;
			// SMS Will be sent from here
			$job_info = $wpdb->get_row("SELECT driver_id, client_id, rent_cost, vat FROM {$wpdb->prefix}job_progression WHERE ID = $application_id");

			$driverId = $job_info->driver_id;
			$clientId = $job_info->client_id;

			$client_ranks = get_user_meta($clientId, 'user_profile_ranks', true);
			if($client_ranks){
				$client_ranks = intval($client_ranks);
			}else{
				$client_ranks = 100;
			}

			$driver_ranks = get_user_meta($driverId, 'user_profile_ranks', true);
			if($driver_ranks){
				$driver_ranks = intval($driver_ranks);
			}else{
				$driver_ranks = 100;
			}

			if($client_ranks < 100){
				$client_ranks += 3;
				if($client_ranks > 100){
					$client_ranks = 100;
				}
				update_user_meta( $clientId, 'user_profile_ranks', $client_ranks );
			}

			if($driver_ranks < 100){
				$driver_ranks += 3;
				if($driver_ranks > 100){
					$driver_ranks = 100;
				}
				update_user_meta( $driverId, 'user_profile_ranks', $driver_ranks );
			}

			$subtotal = get_total_cost_with_vat($job_info->vat, $job_info->rent_cost);
			$due = (floatval($subtotal)-floatval($job_info->rent_cost));
			$vat = $job_info->vat;

			$apiKey = get_option('eiconbd_api_key');
			$driverName = ucfirst(get_user_by( 'ID', $driverId )->display_name);
			$driverPhone = get_user_meta($driverId, 'user_phone', true);
			$jobUniqueId = strtolower(base64_encode($application_id));
			$mesage = "হ্যালো $driverName,\nআপনার ট্রিপ ($jobUniqueId) এইমাত্র শেষ হয়েছে,\nএই ট্রিপের $vat% ভ্যাট ($due টাকা) পরিশোধের অনুরোধ রইলো।";

			if($driverPhone && $apiKey){
				file_get_contents("https://sms.eiconbd.com/services/send.php?key=$apiKey&number=$driverPhone&message=$mesage&option=1&type=sms&prioritize=0");
			}

			echo json_encode(array('updated' => 'Updated'));
			die;
		}
		die;
	}
	
	function trent_terms($post_id, $taxonomy){
		$term_list = get_the_term_list( $post_id, $taxonomy, '', ', ' );
		return apply_filters( 'the_terms', $term_list, $taxonomy, '', ', ' );
	}

	function job_submitted_by_client(){
		if(isset($_POST['new_job_submission'])){
			$job_title = '';
			$load_location = '';
			$unload_locatiion = '';
			$load_time = '';
			$goodstype = '';
			$truck_types = '';
			$job_description = '';

			if(isset($_POST['job_title'])){
				$job_title = sanitize_text_field( $_POST['job_title'] );
			}
			if(isset($_POST['load_location'])){
				$load_location = sanitize_text_field( $_POST['load_location'] );
			}
			if(isset($_POST['unload_locatiion'])){
				$unload_locatiion = sanitize_text_field( $_POST['unload_locatiion'] );
			}
			if(isset($_POST['load_time'])){
				$load_time = $_POST['load_time'];
			}
			if(isset($_POST['goodstype'])){
				$goodstype = $_POST['goodstype'];
			}
			if(isset($_POST['truck_types'])){
				$truck_types = intval($_POST['truck_types']);
			}
			if(isset($_POST['job_description'])){
				$job_description = sanitize_text_field( $_POST['job_description'] );
			}


			if(!empty($job_title) && !empty($load_location) && !empty($unload_locatiion) && !empty($load_time) && !empty($goodstype) && !empty($truck_types) && !empty($job_description)){
				$args = array(
					'post_title'    => $job_title,
					'post_content'  => '',
					'post_status'   => 'publish',
					'post_type'   => 'trent',
					'post_author'   => get_current_user_id(  ),
					'meta_input'   => array(
						'tr_load_location' => $load_location,
						'tr_unload_location' => $unload_locatiion,
						'tr_goodstype' => $goodstype,
						'tr_load_datetime' => $load_time,
						'tr_jobdescription' => $job_description
					)
				);

				$post_id = wp_insert_post( $args );
				$taxonomy = 'trucks';
				wp_set_object_terms( $post_id, intval( $truck_types ), $taxonomy );

				if($post_id){
					wp_safe_redirect( get_post_type_archive_link( 'trent' )."#job-$post_id" );
					exit;
				}
			}else{
				global $globalError;
				$globalError = "Please fill out all fields";
			}

		}
	}

	function upload_documents($file, $filename, $foldername, $filemeata = '', $is_truck = false){
		global $globalError;
		$globalError = '';

		$wpdir = wp_upload_dir(  );
		$max_upload_size = wp_max_upload_size();
		$fileSize = $file['size'];
		$imageFileType = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

		$username = get_user_by( 'ID', get_current_user_id(  ) )->user_login;
		$folderPath = $wpdir['basedir']."/driver-documents/$username/$foldername";
		$uploadPath = "$folderPath/$filename.$imageFileType";
		$uploadedUrl = $wpdir['baseurl']."/driver-documents/$username/$foldername/".$filename.'.'.$imageFileType;

		// Allow certain file formats
		$allowedExt = array("jpg", "png", "jpeg", "gif", "PNG");

		if(!in_array($imageFileType, $allowedExt)) {
			$globalError = "Unsupported file format!";
		}

		if ($fileSize > $max_upload_size) {
			$globalError = "Maximum upload size $max_upload_size";
		}

		if(empty($globalError)){
			if (!file_exists($folderPath)) {
				mkdir($folderPath, 0777, true);
			}
			
			if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
				if($is_truck){
					return $uploadedUrl;
				}else{
					update_user_meta(get_current_user_id(  ), $filemeata, $uploadedUrl);
					update_user_meta(get_current_user_id(  ), "_profile_docs_upload_dir", base64_encode($folderPath));
				}
			}
		}
	}

	function save_profile_informations(){
		// Profile info
		if(isset($_POST['user_profile_submit']) && isset($_POST['profile_nonce'])){
			if(wp_verify_nonce( $_POST['profile_nonce'], '_nonce' )){
				if(isset($_POST['userfullname'])){
					$username = sanitize_text_field( $_POST['userfullname'] );
					$username = stripcslashes($username);
					wp_update_user( array( 'ID' => get_current_user_id(  ), 'display_name' => $username ) );
				}
				if(isset($_POST['userphone'])){
					$userphone = sanitize_text_field( $_POST['userphone'] );
					$userphone = stripcslashes($userphone);
					update_user_meta( get_current_user_id(  ), 'user_phone', $userphone );
				}
				if(isset($_POST['useraddress'])){
					$useraddress = sanitize_text_field( $_POST['useraddress'] );
					$useraddress = stripcslashes($useraddress);
					update_user_meta( get_current_user_id(  ), 'user_locations', $useraddress );
				}
	
				if(current_user_can( 'driver' )){
					if(isset($_POST['tr_docs_type'])){
						$docs_type = sanitize_text_field( $_POST['tr_docs_type'] );
						update_user_meta( get_current_user_id(  ), 'driver_docs_type', $docs_type);
	
						switch ($docs_type) {
							case 'nid-card':
								if(isset($_FILES['nid_card_front_file']) && !empty($_FILES['nid_card_front_file']['tmp_name'])){
									$filename1 = 'front-part';
									$this->upload_documents($_FILES['nid_card_front_file'], $filename1, $docs_type, 'nid_card_front_file');
								}
								if(isset($_FILES['nid_card_back_file']) && !empty($_FILES['nid_card_back_file']['tmp_name'])){
									$filename2 = 'back-part';
									$this->upload_documents($_FILES['nid_card_back_file'], $filename2, $docs_type, 'nid_card_back_file');
								}
								break;
							case 'passport':
								if(isset($_FILES['passport_document_file']) && !empty($_FILES['passport_document_file']['tmp_name'])){
									$filename3 = 'passport-copy';
									$this->upload_documents($_FILES['passport_document_file'], $filename3, $docs_type, 'passport_document_file');
								}
								break;
							case 'driving_license':
								if(isset($_FILES['driving_document_front_file']) && !empty($_FILES['driving_document_front_file']['tmp_name'])){
									$filename4 = 'front-part';
									$this->upload_documents($_FILES['driving_document_front_file'], $filename4, $docs_type, 'driving_document_front_file');
								}
								if(isset($_FILES['driving_document_back_file']) && !empty($_FILES['driving_document_back_file']['tmp_name'])){
									$filename5 = 'back-part';
									$this->upload_documents($_FILES['driving_document_back_file'], $filename5, $docs_type, 'driving_document_back_file');
								}
								break;
						}
					}
				}
			}
		}

		// New trucks
		if(isset($_POST['new_truck_submitted']) && isset($_POST['newtruck_nonce'])){
			if(wp_verify_nonce( $_POST['newtruck_nonce'], '_nonce' )){
				$truck_number = null;
				$registration_date = null;
				$truck_owner = null;
				$truck_types = null;
				$truck_self_photo = null;
				$truck_valid_docs = null;

				if(isset($_POST['truck_number'])){
					$truck_number = sanitize_text_field( $_POST['truck_number'] );
					$truck_number = stripcslashes($truck_number);
				}
				if(isset($_POST['registration_date'])){
					$registration_date = sanitize_text_field( $_POST['registration_date'] );
				}
				if(isset($_POST['truck_owner'])){
					$truck_owner = sanitize_text_field( $_POST['truck_owner'] );
					$truck_owner = stripcslashes($truck_owner);
				}
				if(isset($_POST['truck_types'])){
					$truck_types = intval( $_POST['truck_types'] );
				}
				if(isset($_FILES['truck_self_photo'])){
					$truck_self_photo = $_FILES['truck_self_photo'];
					$truck_self_photo = $this->upload_documents($truck_self_photo, 'truck', "trucks", "", true);
				}
				if(isset($_FILES['truck_valid_docs'])){
					$truck_valid_docs = $_FILES['truck_valid_docs'];
					$truck_valid_docs = $this->upload_documents($truck_valid_docs, 'document', "trucks", "", true);
				}

				if($truck_number !== null && $registration_date !== null && $truck_owner !== null && $truck_types !== null && $truck_self_photo !== null && $truck_valid_docs !== null){
					$data = array(
						"truck_number" => $truck_number,
						"registration_date" => $registration_date,
						"truck_owner" => $truck_owner,
						"truck_types" => $truck_types,
						"truck_self_photo" => $truck_self_photo,
						"truck_valid_docs" => $truck_valid_docs
					);
					
					$prev = get_user_meta( get_current_user_id(  ), '_driver_trucks', true );
					if($prev){
						$prev = unserialize($prev);
						if(!is_array($prev)){
							$prev = [];
						}
					}else{
						$prev = [];
					}

					$prev[] = $data;

					$prev = serialize($prev);
					update_user_meta( get_current_user_id(  ), '_driver_trucks', $prev );

					$user_login = get_user_by( "ID", get_current_user_id(  ) )->user_login;
					wp_safe_redirect( home_url( "author/$user_login?page=profile&tab=2" ) );
					exit;
				}
			}
		}
	}

	// Ajax get payment due
	function get_trip_due_amount(){
		if(!wp_verify_nonce( $_GET['nonce'], 'job_nonce' )){
			echo 'Invalid Request';
			die;
		}

		if(isset($_GET['tripID'])){
			$tripid = intval($_GET['tripID']);
			global $wpdb;
			$trip = $wpdb->get_row("SELECT rent_cost, vat FROM {$wpdb->prefix}job_progression WHERE ID = $tripid");

			if($trip){
				$vat = $trip->vat;
				$rent_cost = floatval($trip->rent_cost);

				$total_cost = floatval(get_total_cost_with_vat($vat, $rent_cost));
				$due = $total_cost - $rent_cost;

				if($due > 0){
					echo json_encode(array("success" => $due));
					die;
				}
			}

			echo json_encode(array("error" => "no data"));
			die;
		}

		echo json_encode(array("error" => "no data"));
		die;
	}
}