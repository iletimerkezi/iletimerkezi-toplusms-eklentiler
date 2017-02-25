<?php
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

	if ( !is_multisite() ) {
		delete_option('mo_customer_validation_admin_email');
		delete_option('mo_customer_validation_company_name');
		delete_option('mo_customer_validation_first_name');
		delete_option('mo_customer_validation_last_name');
		delete_option('mo_customer_validation_wp_default_enable');
		delete_option('mo_customer_validation_wc_default_enable');
		delete_option('mo_customer_validation_wc_enable_type');
		delete_option('mo_customer_validation_pb_default_enable');
		delete_option('mo_customer_validation_um_default_enable');
		delete_option('mo_customer_validation_simplr_default_enable');
		delete_option('mo_customer_validation_simplr_enable_type');
		delete_option('mo_customer_validation_simplr_field_key');
		delete_option('mo_customer_validation_um_enable_type');
		delete_option('mo_customer_validation_event_default_enable');
		delete_option('mo_customer_validation_event_enable_type');
		delete_option('mo_customer_validation_bbp_default_enable');
		delete_option('mo_customer_validation_crf_default_enable');
		delete_option('mo_customer_validation_crf_enable_type');
		delete_option('mo_customer_validation_crf_phone_key');
		delete_option('mo_customer_validation_crf_email_key');
		delete_option('mo_customer_validation_uultra_default_enable');
		delete_option('mo_customer_validation_uultra_enable_type');
		delete_option('mo_customer_validation_uultra_phone_key');
		delete_option('mo_customer_validation_bbp_enable_type');
		delete_option('mo_customer_validation_bbp_phone_key');
		delete_option('mo_customer_validation_wc_checkout_enable');
		delete_option('mo_customer_validation_wc_checkout_type');	
		delete_option('mo_customer_validation_upme_default_enable');
		delete_option('mo_customer_validation_upme_enable_type');
		delete_option('mo_customer_validation_upme_phone_key');
		delete_option('mo_customer_validation_wc_redirect');
		delete_option('mo_customer_validation_wc_checkout_button');
		delete_option('mo_customer_validation_wc_checkout_guest');
		delete_option('mo_customer_validation_pie_default_enable');
		delete_option('mo_customer_validation_pie_enable_type');
		delete_option('mo_customer_validation_pie_phone_key');
		delete_option('mo_customer_check_ln');
		delete_option('mo_customer_email_transactions_remaining');
		delete_option('mo_customer_phone_transactions_remaining');
		delete_option('mo_otp_plugin_version');
		delete_option('mo_customer_validation_transaction_message');
		delete_option('mo_customer_validation_cf7_contact_enable');
		delete_option('mo_customer_validation_cf7_contact_type');
		delete_option('mo_customer_validation_cf7_email_key');
	}else {
		global $wpdb;
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		$original_blog_id = get_current_blog_id();

		foreach ( $blog_ids as $blog_id ){
			switch_to_blog( $blog_id );
			delete_option('mo_customer_validation_admin_email');
			delete_option('mo_customer_validation_company_name');
			delete_option('mo_customer_validation_first_name');
			delete_option('mo_customer_validation_last_name');
			delete_option('mo_customer_validation_wp_default_enable');
			delete_option('mo_customer_validation_wc_default_enable');
			delete_option('mo_customer_validation_wc_enable_type');
			delete_option('mo_customer_validation_pb_default_enable');
			delete_option('mo_customer_validation_um_default_enable');
			delete_option('mo_customer_validation_simplr_default_enable');
			delete_option('mo_customer_validation_simplr_enable_type');
			delete_option('mo_customer_validation_simplr_field_key');
			delete_option('mo_customer_validation_um_enable_type');
			delete_option('mo_customer_validation_event_default_enable');
			delete_option('mo_customer_validation_event_enable_type');
			delete_option('mo_customer_validation_bbp_default_enable');
			delete_option('mo_customer_validation_crf_default_enable');
			delete_option('mo_customer_validation_crf_enable_type');
			delete_option('mo_customer_validation_crf_phone_key');
			delete_option('mo_customer_validation_crf_email_key');
			delete_option('mo_customer_validation_uultra_default_enable');
			delete_option('mo_customer_validation_uultra_enable_type');
			delete_option('mo_customer_validation_uultra_phone_key');
			delete_option('mo_customer_validation_bbp_enable_type');
			delete_option('mo_customer_validation_bbp_phone_key');
			delete_option('mo_customer_validation_wc_checkout_enable');
			delete_option('mo_customer_validation_wc_checkout_type');
			delete_option('mo_customer_validation_upme_default_enable');
			delete_option('mo_customer_validation_upme_enable_type');
			delete_option('mo_customer_validation_upme_phone_key');
			delete_option('mo_customer_validation_wc_redirect');
			delete_option('mo_customer_validation_wc_checkout_button');
			delete_option('mo_customer_validation_wc_checkout_guest');
			delete_option('mo_customer_validation_pie_default_enable');
			delete_option('mo_customer_validation_pie_enable_type');
			delete_option('mo_customer_validation_pie_phone_key');
			delete_option('mo_customer_check_ln');
			delete_option('mo_customer_email_transactions_remaining');
			delete_option('mo_customer_phone_transactions_remaining');
			delete_option('mo_otp_plugin_version');
			delete_option('mo_customer_validation_transaction_message');
			delete_option('mo_customer_validation_cf7_contact_enable');
			delete_option('mo_customer_validation_cf7_contact_type');
			delete_option('mo_customer_validation_cf7_email_key');
		}
		switch_to_blog( $original_blog_id );
	}

?>