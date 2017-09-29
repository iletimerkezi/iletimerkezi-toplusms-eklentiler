<?php
/*
Plugin Name: İleti Merkezi Tickera Sms
Plugin URI:
Description: Tickera eklentisi ile beraber çalışır, bilet satışında ve durumlar değiştiğinde İleti Merkezi üzerinden SMS gönderir.
Version: 1.0.0
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
require_once PLUGIN_CLASS_PATH.'/imTickeraOptions.Class.php';
require_once PLUGIN_CLASS_PATH.'/imSMSGateway.Class.php';


function im_tickera_get_option( $option, $section, $default = '' ) {

    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

class im_Tickera_SMS
{
    /**
     * Constructor for the im_Tickera_SMS class
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

      // Add telephone field to tickera
      add_action( 'tc_buyer_info_fields', array($this, 'im_gsm_field'));
      //apply_filters( 'tc_buyer_info_fields', array($this, 'im_gsm_field'));

    }

    /**
     * Instantiate necessary Class
     * @return void
     */
    function instantiate() {
      new imTickeraOptions();
      new imSMSGateway();
    }

    public static function init() {
      static $instance = false;

      if ( ! $instance ) {
          $instance = new im_Tickera_SMS();
      }
      return $instance;
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup(){
      load_plugin_textdomain( 'iletimekrezisms', false, dirname( plugin_basename( __FILE__ ) ) . 'admin/languages/' );
    }


    public function admin_enqueue_scripts(){

      wp_enqueue_style( 'admin-iletimerkezisms-styles', plugins_url( 'admin/css/admin.css', __FILE__ ), false, date( 'Ymd' ) );
      wp_enqueue_script( 'admin-iletimerkezisms-scripts', plugins_url( 'admin/js/admin.js', __FILE__ ), array( 'jquery' ), false, true );

      wp_localize_script( 'admin-iletimerkezisms-scripts', 'iletimerkezisms', array(
          'ajaxurl' => admin_url( 'admin-ajax.php' )
      ) );
    }

    public function im_gsm_field(){

      $default_fields = array(
        array(
          'field_name'     => 'im_gsm',
          'field_title'    => __( 'Telefon Numarası', 'tc' ),
          'field_type'     => 'text',
          'field_description'  => '',
          'post_field_type'  => 'post_meta',
          'required'       => true,
        )
      );

      return $default_fields;//apply_filters( 'tc_buyer_info_fields', $im_gsm_field);
    }
}

add_action( 'plugins_loaded', 'load_im_tickera_sms' );

function load_im_tickera_sms() {
  $iletimerkezisms = im_Tickera_SMS::init();
}