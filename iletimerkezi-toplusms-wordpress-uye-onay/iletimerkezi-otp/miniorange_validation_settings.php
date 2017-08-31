<?php
/**
* Plugin Name: İleti Merkezi SMS ile üyelik onayı
* Plugin URI: http://www.iletimerkezi.com
* Description: SMS ile Woocommerce, Contact 7 vb. formlar için üyelik onay ekletisi.
* Version: 1.0.0
* Author: ileti merkezi
* Author URI: http://www.iletimerkezi.com
* License: GPL2
*/

require('class-utility.php');
require('miniorange_validation_settings_page.php');
include_once 'miniorange_register_form.php';
global $jal_db_version;
$jal_db_version = '1.0';

class Miniorange_Customer_Validation {

	function __construct() {
		add_action(	'init', array($this, 'miniorange_customer_validation_handle_form' ));
		add_action( 'admin_menu', array( $this, 'miniorange_customer_validation_menu' ) );
		add_action( 'admin_init',  array( $this, 'miniorange_registration_save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'mo_registration_plugin_settings_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'mo_registration_plugin_settings_script' ) );
		add_action( 'enqueue_scripts', array( $this, 'mo_registration_plugin_settings_style' ) );
		add_action( 'enqueue_scripts', array( $this, 'mo_registration_plugin_settings_script' ) );
		add_shortcode('mo_verify_email', array($this, '_cf7_email_shortcode') );
		add_shortcode('mo_verify_phone', array($this, '_cf7_phone_shortcode') );
		//add_filter( 'dbdelta_create_queries', array($this,'jal_install') );
		register_deactivation_hook(__FILE__, array( $this, 'mo_registration_deactivate'));
		register_activation_hook( __FILE__, array($this, 'mo_registration_activate') );



		//registration form actions and filters
			add_action('register_form', 'miniorange_site_register_form');
			add_filter('registration_errors', 'miniorange_site_registration_errors', 10, 3 );
			add_action('admin_post_nopriv_miniorange-validate-otp-form', '_handle_validation_form_action');
			add_action('admin_post_nopriv_validation_goBack', '_handle_validation_goBack_action');
			add_action( 'user_register', 'miniorange_registration_save', 10, 1 );
			//filter to hook into woocommerce account creation
			add_filter('woocommerce_process_registration_errors','woocommerce_site_registration_errors',99,4);
			if(get_option('mo_customer_validation_wc_enable_type')==='mo_wc_phone_enable' || get_option('mo_customer_validation_wc_enable_type')==='mo_wc_both_enable')
				add_action( 'woocommerce_register_form', 'mo_add_phone_field' );
			//filter and action to hook into woocommerce checkout
			if(get_option('mo_customer_validation_wc_checkout_enable')){
				add_action( 'woocommerce_checkout_process', 'my_custom_checkout_field_process');
				add_action( 'woocommerce_after_checkout_billing_form','my_custom_checkout_field',99);
			}
			//filters to hook into fombuilder account creation
			add_filter( 'wppb_build_userdata','formbuilder_site_registration_errors',99,2);
			add_filter('wppb_register_pre_form_message','miniorange_message_formbuilder_override',1,1);
			//filters to hook into Simplr Registration account creation
			add_filter('simplr_validate_form','simplr_site_registration_errors',10,1);
			//action to hook into ultimate member account creation
			add_action('um_submit_form_errors_hook_', 'miniorange_um_phone_validation', 99,1);
			add_action('um_before_new_user_register', 'miniorange_um_user_registration', 99,1);
			//action to hook into event registration account creation
			add_action('evr_process_confirmation','miniorange_evr_user_registration',1,1);
			//filter to hook into buddypress registration account creation
			add_filter('bp_signup_usermeta','miniorange_bp_user_registration',99,1);
			add_action('bp_core_screen_signup','miniorange_check_registration_status',99,0);
			//filter to hook into pie registration form
			add_action('pie_register_after_register_validate', 'miniorange_pie_user_registration',10,0);
			//filter to hook into CF7 contact form
			add_filter( 'wpcf7_validate_text*', 'miniorange_cf7_text_validation', 10 , 2 );
			add_filter( 'wpcf7_validate_email*', 'miniorange_cf7_text_validation', 10 , 2 );
			add_filter( 'wpcf7_validate_email', 'miniorange_cf7_text_validation', 10 , 2 );
			add_filter( 'wpcf7_validate_tel*', 'miniorange_cf7_text_validation', 10 , 2 );
	}

	function jal_install() {
		//die('adasda');
		global $wpdb;
		global $jal_db_version;

		$table_name = $wpdb->prefix . 'iletimerkeziotp';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS `iletimerkeziotp` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `telephone` varchar(55) CHARACTER SET utf8 NOT NULL,
		  `reportid` bigint(20) NOT NULL,
		  `reportstatus` varchar(50) CHARACTER SET utf8 NOT NULL,
		  `otp` int(7) NOT NULL,
		  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`);
		) $charset_collate;
		";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'jal_db_version', $jal_db_version );
	}


	function miniorange_customer_validation_menu() {
		//Add miniOrange plugin to the menu
		//die('sadas!23123123');
		$page = add_menu_page( 'İletimerkezi Üyelik Bilgisi Ayarları ' . __( 'Üyelik Onayı Ayarları', 'mo_customer_validation_settings' ), 'İletiMerkezi OTP', 'administrator',
		'mo_customer_validation_settings', array( $this, 'mo_customer_validation_options' ),plugin_dir_url(__FILE__) . 'includes/images/im-icon.png');
	}

	function  mo_customer_validation_options() {
		global $wpdb;
		update_option( 'mo_customer_validation_host_name', 'https://auth.miniorange.com' );
		mo_register_plugin();
	}

	function mo_registration_plugin_settings_style() {
		wp_enqueue_style( 'mo_customer_validation_admin_settings_style', plugins_url('includes/css/mo_customer_validation_style.css?version=1.1.1', __FILE__));
		wp_enqueue_style( 'mo_customer_validation_admin_settings_phone_style', plugins_url('includes/css/phone.css', __FILE__));
	}

	function mo_registration_plugin_settings_script() {
		wp_enqueue_script( 'mo_customer_validation_admin_settings_phone_script', plugins_url('includes/js/phone.js', __FILE__ ));
		wp_enqueue_script( 'mo_customer_validation_admin_settings_phone_script', plugins_url('includes/js/bootstrap.min.js', __FILE__ ));
		wp_enqueue_script( 'mo_customer_validation_admin_settings_script', plugins_url('includes/js/settings.js?version=1.1.1', __FILE__ ), array('jquery'));
	}

	function mo_registration_activate() {
		//die('adasda');
		global $wpdb;
		global $jal_db_version;

		$table_name = $wpdb->prefix . 'iletimerkeziotp';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."iletimerkeziotp` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `telephone` varchar(55) CHARACTER SET utf8 NOT NULL,
		  `reportid` bigint(20) NOT NULL,
		  `reportstatus` varchar(50) CHARACTER SET utf8 NOT NULL,
		  `otp` int(7) NOT NULL,
		  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`)
		) $charset_collate
		";


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'jal_db_version', $jal_db_version );

		$sql = "SHOW TABLES LIKE '".$table_name."'" ;
		//die(var_export($sql));
		$result = $wpdb->query($sql);
		if (!$result) die('İlgili tablo kurulamamıştır. Destek için destek@emarka.com.tr adresine talep yapabilirsiniz.');
	}

	function mo_registration_deactivate() {
		global $wpdb;
		delete_option('mo_customer_validation_host_name');
		delete_option('mo_customer_validation_transactionId');
		delete_option('mo_customer_validation_admin_password');
		delete_option('mo_customer_validation_registration_status');
		delete_option('mo_customer_validation_admin_phone');
		delete_option('mo_customer_validation_new_registration');
		delete_option('mo_customer_validation_admin_customer_key');
		delete_option('mo_customer_validation_admin_api_key');
		delete_option('mo_customer_validation_customer_token');
		delete_option('mo_customer_validation_verify_customer');
		delete_option('mo_customer_validation_message');
		delete_option('mo_customer_check_ln');
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iletimerkeziotp" );
	}

	function miniorange_registration_save_settings(){
		if ( current_user_can( 'manage_options' )){
			if( isset( $_POST['option'] ) and $_POST['option'] == "mo_registration_register_customer" ) {	//register the admin to miniOrange
				//validation and sanitization
				$company = '';
				$first_name = '';
				$last_name = '';
				$email = '';
				$phone = '';
				$password = '';
				$confirmPassword = '';
				$illegal = "#$%^*()+=[]';,/{}|:<>?~";
				$illegal = $illegal . '"';
				if(MO_Validation_Utility::mo_check_empty_or_null( $_POST['company'] ) || MO_Validation_Utility::mo_check_empty_or_null( $_POST['email'] ) ||MO_Validation_Utility::mo_check_empty_or_null( $_POST['password'] ) ||MO_Validation_Utility::mo_check_empty_or_null( $_POST['confirmPassword'] ) ) {
					update_option( 'mo_customer_validation_message', 'All the fields are required. Please enter valid entries.');
					$this->mo_registration_show_error_message();
					return;
				} else if( strlen( $_POST['password'] ) < 6 || strlen( $_POST['confirmPassword'] ) < 6){	//check password is of minimum length 6
							update_option( 'mo_customer_validation_message', 'Choose a password with minimum length 6.');
							$this->mo_registration_show_error_message();
							return;
				} else if(strpbrk($_POST['email'],$illegal)) {
					update_option( 'mo_customer_validation_message', 'Please match the format of Email. No special characters are allowed.');
					$this->mo_registration_show_error_message();
					return;
				} else {
					$email = sanitize_email( $_POST['email'] );
					$company = sanitize_text_field($_POST['company']);
					$first_name = sanitize_text_field($_POST['fname']);
					$last_name = sanitize_text_field($_POST['lname']);
					$phone = sanitize_text_field( $_POST['phone'] );
					$password = sanitize_text_field( $_POST['password'] );
					$confirmPassword = sanitize_text_field( $_POST['confirmPassword'] );
				}
				update_option( 'mo_customer_validation_company_name', $company);
				update_option( 'mo_customer_validation_first_name', $first_name);
				update_option( 'mo_customer_validation_last_name', $last_name);
				update_option( 'mo_customer_validation_admin_email', $email );
				update_option( 'mo_customer_validation_admin_phone', $phone );
				if( strcmp( $password, $confirmPassword) == 0 ) {
					update_option( 'mo_customer_validation_admin_password', $password );
					$customer = new MO_Validation_Utility();
					$content = json_decode($customer->check_customer(), true);
					if( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND') == 0 ){
						$content = json_decode($customer->send_otp_token('EMAIL',get_option('mo_customer_validation_admin_email')), true);
						if(strcasecmp($content['status'], 'SUCCESS') == 0) {
							if(get_option('mo_customer_validation_email_otp_count')){
							update_option('mo_customer_validation_email_otp_count',get_option('mo_customer_validation_email_otp_count') + 1);
							update_option('mo_customer_validation_message', 'Another One Time Passcode has been sent <b>( ' . get_option('mo_customer_validation_email_otp_count') . ' )</b> for verification to ' . get_option('mo_customer_validation_admin_email'));
						}else{
							update_option( 'mo_customer_validation_message', ' A passcode is sent to ' . get_option('mo_customer_validation_admin_email') . '. Please enter the otp here to verify your email.');
							update_option('mo_customer_validation_email_otp_count',1);
						}
						update_option('mo_customer_validation_transactionId',$content['txId']);
						update_option('mo_customer_validation_registration_status','MO_OTP_DELIVERED_SUCCESS');

						$this->mo_registration_show_success_message();
						}else{
							update_option('mo_customer_validation_message','There was an error in sending email. Please click on Resend OTP to try again.');
							update_option('mo_customer_validation_registration_status','MO_OTP_DELIVERED_FAILURE');
							$this->mo_registration_show_error_message();
						}
					}else{
							$this->get_current_customer();
					}

				} else {
					update_option( 'mo_customer_validation_message', 'Passwords do not match.');
					delete_option('mo_customer_validation_verify_customer');
					$this->mo_registration_show_error_message();
				}
			}else if(isset($_POST['option']) and $_POST['option'] == "mo_registration_validate_otp"){
				//validation and sanitization
				$otp_token = '';
				if(MO_Validation_Utility::mo_check_empty_or_null( $_POST['otp_token'] ) ) {
					update_option( 'mo_customer_validation_message', 'Please enter a value in OTP field.');
					update_option('mo_customer_validation_registration_status','MO_OTP_VALIDATION_FAILURE');
					$this->mo_registration_show_error_message();
					return;
				} else if(!preg_match('/^[0-9]*$/', $_POST['otp_token'])) {
					update_option( 'mo_customer_validation_message', 'Please enter a valid value in OTP field.');
					update_option('mo_customer_validation_registration_status','MO_OTP_VALIDATION_FAILURE');
					$this->mo_registration_show_error_message();
					return;
				} else{
					$otp_token = sanitize_text_field( $_POST['otp_token'] );
				}

				$customer = new MO_Validation_Utility();
				$content = json_decode($customer->validate_otp_token(get_option('mo_customer_validation_transactionId'), $otp_token ),true);

				if(strcasecmp($content['status'], 'SUCCESS') == 0) {
						$this->create_customer();
				}else{
					update_option( 'mo_customer_validation_message','Invalid one time passcode. Please enter a valid passcode.');
					update_option('mo_customer_validation_registration_status','MO_OTP_VALIDATION_FAILURE');
					$this->mo_registration_show_error_message();
				}
			}
			else if( isset($_POST['option']) and $_POST['option'] == 'mo_registration_phone_verification'){ //at registration time
				$phone = sanitize_text_field($_POST['phone_number']);
				$phone = str_replace(' ', '', $phone);
				update_option('mo_customer_validation_admin_phone',$phone);
				$auth_type = 'SMS';
				$customer = new MO_Validation_Utility();
				$send_otp_response = json_decode($customer->send_otp_token($auth_type,'',$phone),true);
				if(strcasecmp($send_otp_response['status'], 'SUCCESS') == 0){
					//Save txId

					update_option('mo_customer_validation_transactionId',$send_otp_response['txId']);
					update_option( 'mo_customer_validation_registration_status','MO_OTP_DELIVERED_SUCCESS');
					if(get_option('mo_customer_validation_sms_otp_count')){
						update_option('mo_customer_validation_sms_otp_count',get_option('mo_customer_validation_sms_otp_count') + 1);
						update_option('mo_customer_validation_message', 'Another One Time Passcode has been sent <b>( ' . get_option('mo_customer_validation_sms_otp_count') . ' )</b> for verification to ' . $phone);
					}else{

							update_option('mo_customer_validation_message', 'One Time Passcode has been sent for verification to ' . $phone);
							update_option('mo_customer_validation_sms_otp_count',1);
					}

					$this->mo_registration_show_success_message();

				}else{
					update_option('mo_customer_validation_message','There was an error in sending sms. Please click on Resend OTP link next to phone number textbox.');
					update_option('mo_customer_validation_registration_status','MO_OTP_DELIVERED_FAILURE');
					$this->mo_registration_show_error_message();
				}
			}
	        else if( isset( $_POST['option'] ) and $_POST['option'] == "mo_registration_connect_verify_customer" ) {	//register the admin to miniOrange
				//validation and sanitization
				$email = '';
				$password = '';
				$illegal = "#$%^*()+=[]';,/{}|:<>?~";
				$illegal = $illegal . '"';
				if(MO_Validation_Utility::mo_check_empty_or_null( $_POST['email'] ) ||MO_Validation_Utility::mo_check_empty_or_null( $_POST['password'] ) ) {
					update_option( 'mo_customer_validation_message', 'All the fields are required. Please enter valid entries.');
					$this->mo_registration_show_error_message();
					return;
				} else if(strpbrk($_POST['email'],$illegal)) {
					update_option( 'mo_customer_validation_message', 'Please match the format of Email. No special characters are allowed.');
					$this->mo_registration_show_error_message();
					return;
				} else{
					$email = sanitize_email( $_POST['email'] );
					$password = sanitize_text_field( $_POST['password'] );
				}

				update_option( 'mo_customer_validation_admin_email', $email );
				update_option( 'mo_customer_validation_admin_password', $password );
				$customer = new MO_Validation_Utility();
				$content = $customer->get_customer_key();
				$customerKey = json_decode( $content, true );
				if( json_last_error() == JSON_ERROR_NONE ) {
					update_option( 'mo_customer_validation_admin_customer_key', $customerKey['id'] );
					update_option( 'mo_customer_validation_admin_api_key', $customerKey['apiKey'] );
					update_option( 'mo_customer_validation_customer_token', $customerKey['token'] );
					update_option( 'mo_customer_validation_admin_phone', $customerKey['phone'] );
					update_option( 'mo_customer_validation_admin_password', '');
					update_option( 'mo_customer_validation_message', 'Your account has been retrieved successfully.');
					delete_option( 'mo_customer_validation_verify_customer');
					$this->_handle_mo_check_ln(false);
					$this->mo_registration_show_success_message();
				} else {
					update_option( 'mo_customer_validation_message', 'Invalid username or password. Please try again.');
					$this->mo_registration_show_error_message();
				}
				update_option('mo_customer_validation_admin_password', '');
			}
			else if(isset($_POST['option']) and $_POST['option'] == 'mo_registration_forgot_password'){
				$email = get_option('mo_customer_validation_admin_email');
				$customer = new MO_Validation_Utility();
				$content = json_decode($customer->forgot_password($email),true);
				if(strcasecmp($content['status'], 'SUCCESS') == 0){
					update_option( 'mo_customer_validation_message','You password has been reset successfully. Please enter the new password sent to your registered mail here.');
					$this->mo_registration_show_success_message();
				}else{
					update_option( 'mo_customer_validation_message','An error occured while processing your request. Please try again.');
					$this->mo_registration_show_error_message();
				}
			}
			else if(isset($_POST['option']) and $_POST['option'] == 'mo_registration_go_back'){
				update_option('mo_customer_validation_registration_status','');
				delete_option('mo_customer_validation_new_registration');
				delete_option('mo_customer_validation_verify_customer' ) ;
				delete_option('mo_customer_validation_admin_email');
				delete_option('mo_customer_validation_sms_otp_count');
				delete_option('mo_customer_validation_email_otp_count');
			}
			else if(isset($_POST['option']) and $_POST['option'] == 'mo_customer_validation_settings'){
				if(MO_Validation_Utility::mo_customer_validation_is_customer_registered()){
					//die(var_dump($_POST));
					update_option('mo_customer_validation_wp_default_enable',isset( $_POST['mo_customer_validation_wp_default_enable']) ? $_POST['mo_customer_validation_wp_default_enable'] : 0);
					update_option('mo_customer_validation_wc_default_enable',isset( $_POST['mo_customer_validation_wc_default_enable']) ? $_POST['mo_customer_validation_wc_default_enable'] : 0);
					update_option('mo_customer_validation_wc_enable_type',isset( $_POST['mo_customer_validation_wc_enable_type']) ? $_POST['mo_customer_validation_wc_enable_type'] : '');
					update_option('mo_customer_validation_pb_default_enable',isset( $_POST['mo_customer_validation_pb_default_enable']) ? $_POST['mo_customer_validation_pb_default_enable'] : 0);
					update_option('mo_customer_validation_um_default_enable',isset( $_POST['mo_customer_validation_um_default_enable']) ? $_POST['mo_customer_validation_um_default_enable'] : 0);
					update_option('mo_customer_validation_simplr_default_enable',isset( $_POST['mo_customer_validation_simplr_default_enable']) ? $_POST['mo_customer_validation_simplr_default_enable'] : 0);
					update_option('mo_customer_validation_simplr_enable_type',isset( $_POST['mo_customer_validation_simplr_enable_type']) ? $_POST['mo_customer_validation_simplr_enable_type'] : '');
					update_option('mo_customer_validation_simplr_field_key',isset( $_POST['simplr_phone_field_key']) ? $_POST['simplr_phone_field_key'] : '');
					update_option('mo_customer_validation_um_enable_type',isset( $_POST['mo_customer_validation_um_enable_type']) ? $_POST['mo_customer_validation_um_enable_type'] : '');
					update_option('mo_customer_validation_event_default_enable',isset( $_POST['mo_customer_validation_event_default_enable']) ? $_POST['mo_customer_validation_event_default_enable'] : '');
					update_option('mo_customer_validation_event_enable_type',isset( $_POST['mo_customer_validation_event_enable_type']) ? $_POST['mo_customer_validation_event_enable_type'] : '');
					update_option('mo_customer_validation_bbp_default_enable',isset( $_POST['mo_customer_validation_bbp_default_enable']) ? $_POST['mo_customer_validation_bbp_default_enable'] : 0);
					update_option('mo_customer_validation_crf_default_enable',isset( $_POST['mo_customer_validation_crf_default_enable']) ? $_POST['mo_customer_validation_crf_default_enable'] : 0);
					update_option('mo_customer_validation_crf_enable_type',isset( $_POST['mo_customer_validation_crf_enable_type']) ? $_POST['mo_customer_validation_crf_enable_type'] : 0);
					update_option('mo_customer_validation_crf_phone_key',isset( $_POST['crf_phone_field_key']) ? $_POST['crf_phone_field_key'] : '');
					update_option('mo_customer_validation_crf_email_key',isset( $_POST['crf_email_field_key']) ? $_POST['crf_email_field_key'] : '');
					update_option('mo_customer_validation_uultra_default_enable',isset( $_POST['mo_customer_validation_uultra_default_enable']) ? $_POST['mo_customer_validation_uultra_default_enable'] : 0);
					update_option('mo_customer_validation_uultra_enable_type',isset( $_POST['mo_customer_validation_uultra_enable_type']) ? $_POST['mo_customer_validation_uultra_enable_type'] : '');
					update_option('mo_customer_validation_uultra_phone_key',isset( $_POST['uultra_phone_field_key']) ? $_POST['uultra_phone_field_key'] : '');

					update_option('mo_customer_validation_bbp_enable_type',isset( $_POST['mo_customer_validation_bbp_enable_type']) ? $_POST['mo_customer_validation_bbp_enable_type'] : '');
					update_option('mo_customer_validation_bbp_phone_key',isset( $_POST['bbp_phone_field_key']) ? $_POST['bbp_phone_field_key'] : '');
					update_option('mo_customer_validation_wc_checkout_enable',isset( $_POST['mo_customer_validation_wc_checkout_enable']) ? $_POST['mo_customer_validation_wc_checkout_enable'] : 0);
					update_option('mo_customer_validation_wc_checkout_type',isset( $_POST['mo_customer_validation_wc_checkout_type']) ? $_POST['mo_customer_validation_wc_checkout_type'] : '');
					update_option('mo_customer_validation_wc_checkout_guest',isset( $_POST['mo_customer_validation_wc_checkout_guest']) ? $_POST['mo_customer_validation_wc_checkout_guest'] : '');
					update_option('mo_customer_validation_wc_checkout_button',isset( $_POST['mo_customer_validation_wc_checkout_button']) ? $_POST['mo_customer_validation_wc_checkout_button'] : '');
					update_option('mo_customer_validation_upme_default_enable',isset( $_POST['mo_customer_validation_upme_default_enable']) ? $_POST['mo_customer_validation_upme_default_enable'] : 0);
					update_option('mo_customer_validation_upme_enable_type',isset( $_POST['mo_customer_validation_upme_enable_type']) ? $_POST['mo_customer_validation_upme_enable_type'] : '');
					update_option('mo_customer_validation_upme_phone_key',isset( $_POST['upme_phone_field_key']) ? $_POST['upme_phone_field_key'] : '');
					update_option('mo_customer_validation_pie_default_enable',isset( $_POST['mo_customer_validation_pie_default_enable']) ? $_POST['mo_customer_validation_pie_default_enable'] : 0);
					update_option('mo_customer_validation_pie_enable_type',isset( $_POST['mo_customer_validation_pie_enable_type']) ? $_POST['mo_customer_validation_pie_enable_type'] : '');
					update_option('mo_customer_validation_pie_phone_key',isset( $_POST['pie_phone_field_key']) ? $_POST['pie_phone_field_key'] : '');
					update_option('mo_customer_validation_wc_redirect',isset($_POST['page_id']) ? get_the_title($_POST['page_id']) : 'My Account');
					update_option('mo_customer_validation_cf7_contact_enable',isset( $_POST['mo_customer_validation_cf7_contact_enable']) ? $_POST['mo_customer_validation_cf7_contact_enable'] : 0);
					update_option('mo_customer_validation_cf7_contact_type',isset( $_POST['mo_customer_validation_cf7_contact_type']) ? $_POST['mo_customer_validation_cf7_contact_type'] : '');
					update_option('mo_customer_validation_cf7_email_key',isset( $_POST['cf7_email_field_key']) ? $_POST['cf7_email_field_key'] : '');

					if(!$_POST['error_message']){
						update_option( 'mo_customer_validation_message', 'Settings saved successfully. You can go to your registration form page to test the plugin. <a href=\"'.wp_logout_url().'\">Click here<\/a> to logout.' );
							$this->mo_registration_show_success_message();
					}else{
						update_option( 'mo_customer_validation_message', $_POST['error_message']);
						$this->mo_registration_show_error_message();
					}
				}else{
					update_option('mo_customer_validation_message','Please register an account before trying to enable OTP verification for any form.');
					$this->mo_registration_show_error_message();
				}
			}
			else if(isset($_POST['option']) and trim($_POST['option']) == "mo_registration_resend_otp"){ //resend OTP over email for admin
				$customer = new MO_Validation_Utility();
				$content = json_decode($customer->send_otp_token('EMAIL',get_option('mo_customer_validation_admin_email')), true);
				if(strcasecmp($content['status'], 'SUCCESS') == 0) {
					if(get_option('mo_customer_validation_email_otp_count')){
						update_option('mo_customer_validation_email_otp_count',get_option('mo_customer_validation_email_otp_count') + 1);
						update_option('mo_customer_validation_message', 'Another One Time Passcode has been sent <b>( ' . get_option('mo_customer_validation_email_otp_count') . ' )</b> for verification to ' . get_option('mo_customer_validation_admin_email'));
					}else{

						update_option( 'mo_customer_validation_message', ' A passcode is sent to ' . get_option('mo_customer_validation_admin_email') . '. Please enter the otp here to verify your email.');
						update_option('mo_customer_validation_email_otp_count',1);
					}
					update_option('mo_customer_validation_transactionId',$content['txId']);
					update_option('mo_customer_validation_registration_status','MO_OTP_DELIVERED_SUCCESS');

					$this->mo_registration_show_success_message();
				}else{
					update_option('mo_customer_validation_message','There was an error in sending email. Please click on Resend OTP to try again.');
					update_option('mo_customer_validation_registration_status','MO_OTP_DELIVERED_FAILURE');
					$this->mo_registration_show_error_message();
				}
			}else if(isset($_POST['option']) and trim($_POST['option']) == "mo_otp_verification_test"){
				miniorange_otp_verification_testmodal();
	 		}else if(isset($_POST['option']) and trim($_POST['option']) == "mo_validation_contact_us_query_option" ){
	 			$this->_mo_validation_support_query($_POST);
	 		}else if (isset($_POST['option']) and trim($_POST['option']) == "iletimerkezi_options") {
	 			$this->save_iletimerkezi_options($_POST);
	 		}
 		}
	}

	function miniorange_customer_validation_handle_form(){
		//die('asdsadsadad');
		if(get_option('mo_otp_plugin_version')>1.4){
			$email = get_option('mo_customer_email_transactions_remaining');
			$phone = get_option('mo_customer_email_transactions_remaining');
			//$t = "'quick_recharge'";
			update_option('mo_customer_validation_transaction_message','You have '.$email.' Email Transactions and '.$phone.' Phone Transactions remaining.');
			//For quick recharge <a href="#" onclick="document.getElementById('.$t.').submit()">click here</a>
		}
		if(isset($_GET['option']) and trim($_GET['option']) == "miniorange-woocommerce-checkout"){
			_handle_woocommere_checkout_form($_GET);
		}else if(isset($_GET['option']) and trim($_GET['option']) == "miniorange-cf7-contact"){
			_handle_cf7_contact_form($_GET);
		}else if(isset($_POST['option']) and trim($_POST['option']) == "validation_goBack"){
			_handle_validation_goBack_action();
		}else if(isset($_POST['option']) and trim($_POST['option']) == "miniorange-validate-otp-form"){
			$from_both = $_POST['from_both']=='true' ? true : false;
			_handle_validation_form_action($_POST['otp_type'],$from_both);
		}else if(isset($_POST['option']) and trim($_POST['option']) == "verification_resend_otp_phone"){
			_handle_verification_resend_otp_action("phone");
		}else if(isset($_POST['option']) and trim($_POST['option']) == "verification_resend_otp_email"){
			_handle_verification_resend_otp_action("email");
		}else if(isset($_POST['option']) and trim($_POST['option']) == "verification_resend_otp_both"){
			_handle_verification_resend_otp_action("both");
		}else if(isset($_POST['option']) and trim($_POST['option']) == "miniorange-validate-otp-choice-form"){
			_handle_validate_otp_choice_form($_POST);
		}else if(isset($_REQUEST['crf_key']) && get_option('mo_customer_validation_crf_default_enable')){
			_handle_crf_form_submit($_REQUEST);
		}else if(isset($_POST['xoouserultra-register-form']) &&  get_option('mo_customer_validation_uultra_default_enable')){
			_handle_uultra_form_submit($_POST['user_login'],$_POST['user_email'],$_POST['phone']);
		}else if(isset($_POST['upme-register-form']) &&  get_option('mo_customer_validation_upme_default_enable')){
			_handle_upme_form_submit($_POST);
 		}else if(isset($_POST['option']) and trim($_POST['option']) == "check_mo_ln"){
 			$this->_handle_mo_check_ln(true);
 		}
	}

	function mo_registration_success_message() {
		$message = get_option('mo_customer_validation_message'); ?>
		<script>
		jQuery(document).ready(function() {
			var message = '<?php echo $message; ?>';
			jQuery('#mo_registration_msgs').append("<div class='error notice is-dismissible mo_registration_error_container'> <p class='mo_registration_msgs'>" + message + "</p></div>");
		});
		</script>
	<?php }

	function mo_registration_error_message() {
			$message = get_option('mo_customer_validation_message'); ?>
		<script>
		jQuery(document).ready(function() {
			var message = '<?php echo $message; ?>';
			jQuery('#mo_registration_msgs').append("<div class='updated notice is-dismissible mo_registration_success_container'> <p class='mo_registration_msgs'>" + message + "</p></div>");
		});
		</script>
	<?php }

	function get_current_customer(){
		$customer = new MO_Validation_Utility();
		$content = $customer->get_customer_key();
		$customerKey = json_decode( $content, true );
		if( json_last_error() == JSON_ERROR_NONE ) {
			update_option('mo_customer_validation_admin_customer_key', $customerKey['id'] );
			update_option('mo_customer_validation_admin_api_key', $customerKey['apiKey'] );
			update_option('mo_customer_validation_customer_token', $customerKey['token'] );
			update_option('mo_customer_validation_admin_password', '' );
			update_option('mo_customer_validation_message', 'Your account has been retrieved successfully.' );
			delete_option('mo_customer_validation_verify_customer');
			delete_option('mo_customer_validation_new_registration');

			$this->_handle_mo_check_ln(false);
			$this->mo_registration_show_success_message();
		} else {
			update_option('mo_customer_validation_message', 'You already have an account with miniOrange. Please enter a valid password.');
			update_option('mo_customer_validation_verify_customer', 'true');
			delete_option('mo_customer_validation_new_registration');
			$this->mo_registration_show_error_message();
		}
	}

	function create_customer(){
		delete_option('mo_customer_validation_sms_otp_count');
		delete_option('mo_customer_validation_email_otp_count');
		$customer = new MO_Validation_Utility();
		$customerKey = json_decode( $customer->create_customer(), true );
		if( strcasecmp( $customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0 ) {
			$this->get_current_customer();
		} else if( strcasecmp( $customerKey['status'], 'SUCCESS' ) == 0 ) {
			update_option('mo_customer_validation_admin_customer_key', $customerKey['id'] );
			update_option('mo_customer_validation_admin_api_key', $customerKey['apiKey'] );
			update_option('mo_customer_validation_customer_token', $customerKey['token'] );
			update_option('mo_customer_validation_admin_password', '');
			update_option('mo_customer_validation_message', 'Registration complete!');
			update_option('mo_customer_validation_registration_status','MO_CUSTOMER_VALIDATION_REGISTRATION_COMPLETE');
			update_option('mo_customer_email_transactions_remaining',10);
			update_option('mo_customer_phone_transactions_remaining',10);
			update_option('mo_otp_plugin_version',1.8);
			delete_option('mo_customer_validation_verify_customer');
			delete_option('mo_customer_validation_new_registration');
			$this->mo_registration_show_success_message();
			header('Location: admin.php?page=mo_customer_validation_settings&tab=pricing');
		}
		update_option('mo_customer_validation_admin_password', '');
	}

	private function mo_registration_show_success_message() {
		remove_action( 'admin_notices', array( $this, 'mo_registration_success_message') );
		add_action( 'admin_notices', array( $this, 'mo_registration_error_message') );
	}

	private function mo_registration_show_error_message() {
		remove_action( 'admin_notices', array( $this, 'mo_registration_error_message') );
		add_action( 'admin_notices', array( $this, 'mo_registration_success_message') );
	}

	private function _handle_mo_check_ln($showMessage){
		$challenge_otp = new MO_Validation_Utility();
		$content = json_decode($challenge_otp->check_customer_ln(), true);
		if(strcasecmp($content['status'], 'SUCCESS') == 0){
			array_key_exists('licensePlan', $content) && !MO_Validation_Utility::mo_check_empty_or_null($content['licensePlan']) ? update_option('mo_customer_check_ln',base64_encode($content['licensePlan'])) : update_option('mo_customer_check_ln','');
			if($showMessage)
				array_key_exists('licensePlan', $content) && !MO_Validation_Utility::mo_check_empty_or_null($content['licensePlan']) ? update_option('mo_customer_validation_message', 'Thank you. You have upgraded to '.$content['licensePlan']) : update_option('mo_customer_validation_message', 'You are on our FREE plan. Check Licensing Tab to learn how to upgrade.');
			if(array_key_exists('licensePlan', $content) && !MO_Validation_Utility::mo_check_empty_or_null($content['licensePlan'])){
				delete_option('mo_customer_email_transactions_remaining');
				delete_option('mo_customer_phone_transactions_remaining');
				delete_option('mo_otp_plugin_version');
				delete_option('mo_customer_validation_transaction_message');
			}

		} else if(strcasecmp($content['status'], 'FAILED') == 0){
            if($showMessage)
            update_option('mo_customer_validation_message', 'You are on our FREE plan. Check Licensing Tab to learn how to upgrade.');
        }
		$this->mo_registration_show_success_message();
	}

	private function _mo_validation_support_query($POSTED) {
		// Contact Us query
		$email = sanitize_text_field($POSTED['mo_registration_contact_us_email']);
		$phone = sanitize_text_field($POSTED['mo_registration_contact_us_phone']);
		$query = sanitize_text_field($POSTED['mo_registration_contact_us_query']);
		$customer = new MO_Validation_Utility();
		if ( MO_Validation_Utility::mo_check_empty_or_null( $email ) || MO_Validation_Utility::mo_check_empty_or_null( $query ) ) {
			update_option('mo_customer_validation_message', 'Please fill up Email and Query fields to submit your query.');
			$this->mo_idp_show_error_message();
		} else {
			$submited = $customer->submit_contact_us( $email, $phone, $query );
			if ( $submited == false ) {
				update_option('mo_customer_validation_message', 'Your query could not be submitted. Please try again.');
				$this->mo_registration_show_error_message();
			} else {
				update_option('mo_customer_validation_message', 'Thanks for getting in touch! We shall get back to you shortly.');
				$this->mo_registration_show_success_message();
			}
		}
	}

	function _cf7_email_shortcode(){
		$img = "<div style='display:table;text-align:center;'><img src='".plugin_dir_url(__FILE__) . "includes/images/loader.gif'></div>";
		$html = '<script>jQuery(document).ready(function(){$=jQuery;$("#miniorange_otp_token_submit").click(function(o){ var e=$("input[name='.get_option('mo_customer_validation_cf7_email_key').']").val(); $("#mo_message").empty(),$("#mo_message").append("'.$img.'"),$("#mo_message").show(),$.ajax({url:"'.site_url().'",type:"GET",data:"option=miniorange-cf7-contact&user_email="+e,crossDomain:!0,dataType:"json",contentType:"application/json; charset=utf-8",success:function(o){ if(o.result=="success"){$("#mo_message").empty(),$("#mo_message").append(o.message),$("#mo_message").css("border-top","3px solid green"),$("input[name=email_verify]").focus()}else{$("#mo_message").empty(),$("#mo_message").append(o.message),$("#mo_message").css("border-top","3px solid red"),$("input[name=email_verify]").focus()} ;},error:function(o,e,n){}})});});</script>';
		return $html;
	}

	function _cf7_phone_shortcode(){
		$img = "<div style='display:table;text-align:center;'><img src='".plugin_dir_url(__FILE__) . "includes/images/loader.gif'></div>";
		$html = '<script>jQuery(document).ready(function(){$=jQuery;$("#miniorange_otp_token_submit").click(function(o){ var e=$("input[name=mo_phone]").val(); $("#mo_message").empty(),$("#mo_message").append("'.$img.'"),$("#mo_message").show(),$.ajax({url:"'.site_url().'",type:"GET",data:"option=miniorange-cf7-contact&user_phone="+e,crossDomain:!0,dataType:"json",contentType:"application/json; charset=utf-8",success:function(o){ if(o.result=="success"){$("#mo_message").empty(),$("#mo_message").append(o.message),$("#mo_message").css("border-top","3px solid green"),$("input[name=email_verify]").focus()}else{$("#mo_message").empty(),$("#mo_message").append(o.message),$("#mo_message").css("border-top","3px solid red"),$("input[name=phone_verify]").focus()} ;},error:function(o,e,n){}})});});</script>';
		return $html;
	}

	function save_iletimerkezi_options($POSTED){

		if (MO_Validation_Utility::mo_check_empty_or_null( $POSTED['iletimerkezi_username'] ) || MO_Validation_Utility::mo_check_empty_or_null( $POSTED['iletimerkezi_password'] ) || MO_Validation_Utility::mo_check_empty_or_null( $POSTED['iletimerkezi_sender'] ) ) {
			//update_option('mo_customer_validation_message', 'Lütfen tüm alanları eksiksiz olarak doldurun.');
			return false;
		}
			$phone = preg_replace('/\D/','',$POSTED['iletimerkezi_username']);
			$phone = substr($phone, -10);
			update_option('iletimerkezi_username', $phone);
			update_option('iletimerkezi_password', $POSTED['iletimerkezi_password']);
			update_option('iletimerkezi_sender', $POSTED['iletimerkezi_sender']);
			if (MO_Validation_Utility::get_balance()[1] ) {
				update_option('get_balance', '1');
			}
			return true;
	}

}

new Miniorange_Customer_Validation;
?>