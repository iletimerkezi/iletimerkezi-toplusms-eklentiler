<?php
/*
Plugin Name: Contact Form 7 - İletimerkezi SMS
Description: Sms bildirimleri gönderin
Version: 1.0
Author: Emarka
Author URI: https://www.iletimerkezi.com
*/
$GLOBALS['iletimerkezi_plugins'][ basename( dirname( __FILE__ ) ) ] = '1.0';

if( !function_exists( 'iletimerkezi_loader' ) ) {

  function iletimerkezi_loader() {
    $versions = array_flip( $GLOBALS['iletimerkezi_plugins'] );
    
    if( !class_exists( 'Iletimerkezi_Plugin' ) ) {
      require_once( dirname( dirname( __FILE__ ) ) . '/contact-form-7-iletimerkezi-sms/class-iletimerkezi-plugin.php' );
    }   
    
    require_once( dirname( dirname( __FILE__ ) ) . '/contact-form-7-iletimerkezi-sms/main.php' );
  }
  
}

add_action( 'plugins_loaded', 'iletimerkezi_loader' );
