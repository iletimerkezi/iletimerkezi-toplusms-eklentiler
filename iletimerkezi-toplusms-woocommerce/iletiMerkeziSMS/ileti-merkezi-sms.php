<?php
/*
Plugin Name: İleti Merkezi Sms
Plugin URI: http://www.woocommercesms.com
Description: Woocommers ile beraber çalışır, ürün satışında ve ürün durumları değiştiğinde İleti Merkezi üzerinden SMS gönderir.
Version: 1.0.1
Author: İleti Merkezi
Author URI: http://www.iletimerkezi.com/
License: GPL2
*/


/**
 * Copyright (c) 2015 İleti Merkezi (email: bilgi@emarka.com.tr). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

// Lib Directory Path Constant
define( 'PLUGIN_CLASS_PATH', dirname(__FILE__). '/includes' );

// Requere settings api
require_once PLUGIN_CLASS_PATH. '/class.settings-api.php';
require_once PLUGIN_CLASS_PATH.'/iletimerkeziOptions.Class.php';
require_once PLUGIN_CLASS_PATH.'/iletimerkeziSMSGateway.Class.php';

/**
 * Get SMS Settings Settings options value
 * @param  string $option
 * @param  string $section
 * @param  string $default
 * @return mixed
 */
function iletimerkezisms_get_option( $option, $section, $default = '' ) {

    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

/**
 * iletimerkezi_Order_SMS class
 *
 * @class iletimerkezi_Order_SMS The class that holds the entire iletimerkezi_Order_SMS plugin
 */
class iletimerkezi_Order_SMS {

    /**
     * Constructor for the iletimerkezi_Order_SMS class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {

      // Instantiate necessary class
      $this->instantiate();

      // Localize our plugin
      add_action( 'init', array( $this, 'localization_setup' ) );

      // Loads frontend scripts and styles
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

      if(iletimerkezisms_get_option( 'force_sms', 'iletimerkezisms_general', 'off' ) == 'off' ){
        add_action( 'woocommerce_checkout_after_customer_details', array( $this, 'add_buyer_notification_field' ) );
        add_action( 'woocommerce_checkout_process', array( $this, 'add_buyer_notification_field_process' ) );
      }
      add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'buyer_notification_update_order_meta' ) );
      add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'buyer_sms_notify_display_admin_order_meta' ) , 10, 1 );
      add_action( 'woocommerce_order_status_changed', array( $this, 'trigger_after_order_place' ), 10, 3 );
     //add_filter( 'wc_order_statuses', array($this, 'filter_order_status'));


    }

    /**
     * Instantiate necessary Class
     * @return void
     */
    function instantiate() {
      new iletimerkeziOptions();
      new iletimerkeziSMSGateway();
    }

    public static function init() {
      static $instance = false;

      if ( ! $instance ) {
          $instance = new iletimerkezi_Order_SMS();
      }
      return $instance;
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
      load_plugin_textdomain( 'iletimekrezisms', false, dirname( plugin_basename( __FILE__ ) ) . 'admin/languages/' );
    }


    public function admin_enqueue_scripts() {

      wp_enqueue_style( 'admin-iletimerkezisms-styles', plugins_url( 'admin/css/admin.css', __FILE__ ), false, date( 'Ymd' ) );
      wp_enqueue_script( 'admin-iletimerkezisms-scripts', plugins_url( 'admin/js/admin.js', __FILE__ ), array( 'jquery' ), false, true );

      wp_localize_script( 'admin-iletimerkezisms-scripts', 'iletimerkezisms', array(
          'ajaxurl' => admin_url( 'admin-ajax.php' )
      ) );
    }

    /**
     * Add Buyer Notification field in checkout page
     */
    function add_buyer_notification_field() {

      if( iletimerkezisms_get_option( 'buyer_notification', 'iletimerkezisms_general', 'off' ) == 'off' ) {
          return;
      }

      $required = ( iletimerkezisms_get_option( 'force_buyer_notification', 'iletimerkezisms_general', 'no' ) == 'yes' ) ? true : false;
      $checkbox_text = iletimerkezisms_get_option( 'buyer_notification_text', 'iletimerkezisms_general', 'Send me order status notifications via sms' );
      woocommerce_form_field( 'buyer_sms_notify', array(
          'type'          => 'checkbox',
          'class'         => array('buyer-sms-notify form-row-wide'),
          'label'         => __( $checkbox_text, 'iletimerkezisms' ),
          'required'      => $required,
      ), 0);
    }

    /**
     * Add Buyer Notification field validation
     */
    function add_buyer_notification_field_process() {

      if( iletimerkezisms_get_option( 'force_buyer_notification', 'iletimerkezisms_general', 'no' ) == 'no' ) {
          return;
      }

      // Check if the field is set, if not then show an error message.
      if ( ! $_POST['buyer_sms_notify'] ) {
              wc_add_notice( __( '<strong>Send Notification Via SMS</strong> must be required' ), 'error' );
      }
    }

    /**
     * Display Buyer notification in Order admin page
     * @param  object $order
     * @return void
     */
    function buyer_sms_notify_display_admin_order_meta( $order ) {
      $want_notification =  get_post_meta( $order->id, '_buyer_sms_notify', true );
      $display_info = (  isset( $want_notification ) && !empty( $want_notification ) ) ? 'Evet' : 'Hayır';
      echo '<p><strong>'.__('Kullanıcı SMS ile bildirim istiyor.').':</strong> ' . $display_info . '</p>';
    }

    /**
     * Update Order buyer notify meta in checkout page
     * @param  integer $order_id
     * @return void
     */
    function buyer_notification_update_order_meta( $order_id ) {
      if ( ! empty( $_POST['buyer_sms_notify'] ) ) {
          update_post_meta( $order_id, '_buyer_sms_notify', sanitize_text_field( $_POST['buyer_sms_notify'] ) );
      }
    }

    /**
     * Trigger when and order is placed
     * @param  integer $order_id
     * @param  string $old_status
     * @param  string $new_status
     * @return void
     */
    public  function trigger_after_order_place( $order_id, $old_status, $new_status )
    {
      $order = new WC_Order( $order_id );
      $ywot = get_post_custom ( $order_id );
      if( !$order_id ) {
          return;
      }
      $admin_sms_data = $buyer_sms_data = array();

      $default_admin_sms_body = __( 'You have a new Order. The [order_id] is now [order_status]', 'iletimerkezisms' );
      $default_buyer_sms_body = __( 'Thanks for purchasing. Your [order_id] is now [order_status]. Thank you', 'iletimerkezisms' );
      $order_status_settings  = iletimerkezisms_get_option( 'order_status', 'iletimerkezisms_general', array() );
      $admin_phone_number     = iletimerkezisms_get_option( 'sms_admin_phone', 'iletimerkezisms_message', '' );
      $admin_sms_body         = iletimerkezisms_get_option( 'admin_sms_body', 'iletimerkezisms_message', $default_admin_sms_body );
      $buyer_sms_body         = iletimerkezisms_get_option( 'order_'.$new_status, 'iletimerkezisms_message', $default_buyer_sms_body );
      $active_gateway         = iletimerkezisms_get_option( 'sms_gateway', 'iletimerkezisms_gateway', 'iletimerkezi' );
      $want_to_notify_buyer   = get_post_meta( $order_id, '_buyer_sms_notify', true );

      if( count( $order_status_settings ) < 0 || empty( $active_gateway ) ) {
          return true;
      }

      if( in_array( $new_status, $order_status_settings ) || $this->_isNewOrderActive() ) {
        //send SMS admin
        if ($this->_isAdminSMSActive() && $new_status == 'on-hold') {
          $buyer_number   = get_post_meta( $order_id, '_billing_phone', true );
          $admin_numbers = explode(',',$admin_phone_number);
          foreach ($admin_numbers as $admin_number) {
            $admin_sms_data['number']   = $admin_number;
            $admin_sms_data['sms_body'] = $this->pharse_sms_body( $admin_sms_body, $new_status, $order_id, $ywot, $buyer_number );
            $admin_response             = iletimerkeziSMSGateway::iletimerkeziSendSMS( $admin_sms_data );
            if( $admin_response ) {
                $order->add_order_note( __( 'Admin numarasına SMS gönderilmiştir.', 'iletimerkezisms' ) );
            } else {
                $order->add_order_note( __( 'Admin numarasına SMS gönderimi başarısız oldu.', 'iletimerkezisms' ) );
            }
          }
        }
        //send SMS buyer
        if( !$this->_isNewOrderActive() && $new_status == 'on-hold' ) {
          return true;
        }
        if(!$this->_isForceSMSActive() && !$want_to_notify_buyer ){
          return true;
        }

        $buyer_number = get_post_meta( $order_id, '_billing_phone', true );
        $buyer_sms_data['sms_body'] = $this->pharse_sms_body( $buyer_sms_body, $new_status, $order_id, $ywot );

        if (!empty($buyer_sms_data['sms_body'])) {
          $buyer_sms_data['number']   = $buyer_number;
          $buyer_response             = iletimerkeziSMSGateway::iletimerkeziSendSMS( $buyer_sms_data );
          if( $buyer_response ) {
              $order->add_order_note( __( 'Müşteriye SMS gönderilmiştir.', 'iletimerkezisms' ) );
          } else {
              $order->add_order_note( __( 'Müşteriye SMS gönderimi başarısız oldu.', 'iletimerkezisms' ) );
          }
        }
      }
    }

    private function _isNewOrderActive()
    {
      return iletimerkezisms_get_option( 'enable_notification', 'iletimerkezisms_general', 'on' ) == 'on' ? true : false;
    }

    private function _isAdminSMSActive()
    {
      return iletimerkezisms_get_option( 'admin_notification', 'iletimerkezisms_general', 'on' ) == 'on' ? true : false;
    }

    private function _isForceSMSActive()
    {
      return iletimerkezisms_get_option('force_sms', 'iletimerkezisms_general', 'on') == 'on' ? true : false;
    }

    public function pharse_sms_body( $content, $order_status, $order_id, $ywot, $number )
    {
      $order = $order_id;
      $ywot_carrier_name = $ywot['ywot_carrier_name'][0];
      $ywot_pick_up_date = $ywot['ywot_pick_up_date'][0];
      $ywot_tracking_code = $ywot['ywot_tracking_code'][0];
      $order_total = $order_amount. ' '. get_post_meta( $order_id, '_order_currency', true );

      $filter_status = $this->filter_order_status($order_status);
      $find = array(
          '[order_id]',
          '[order_status]',
          '[ywot_carrier_name]',
          '[ywot_pick_up_date]',
          '[ywot_tracking_code]',
          '[buyer_number]',
      );
      $replace = array(
          $order,
          $filter_status,
          $ywot_carrier_name,
          $ywot_pick_up_date,
          $ywot_tracking_code,
          $number
      );

      $body = str_replace( $find, $replace, $content );

      return $body;
    }

    public function filter_order_status($new_status){

     $status_posts = get_posts( array(
              'posts_per_page' => -1,
              'post_type'      => 'yith-wccos-ostatus',
              'post_status'    => 'publish'
          ) );
      foreach ( $status_posts as $sp ) {
          $statuses[ get_post_meta( $sp->ID, 'slug', true ) ] = $sp->post_title;
      }
      $default_statuses = array('pending' => 'Ödeme Bekliyor',
                                'processing' => 'İşleniyor',
                                'on-hold' => 'Beklemede',
                                'completed' => 'Tamamlandı',
                                'cancelled' => 'İptal Edildi',
                                'refunded' => 'İade Edildi',
                                'failed' => 'Başarısız');
      if (!empty($statuses)) {
        $order_statuses = array_merge($statuses,$default_statuses);
      }else{
        $order_statuses = $default_statuses;
      }
      $order_status = $order_statuses[$new_status];
      if (!$order_status) {
          return $new_status;
      }
      return $order_status ;
    }


}
add_action( 'plugins_loaded', 'load_iletimerkezi_order_sms' );

function load_iletimerkezi_order_sms() {
  $iletimerkezisms = iletimerkezi_Order_SMS::init();
}