<?php

/**
 * WordPress settings API class
 *
 * @author Tareq Hasan
 */

class SatSMS_Setting_Options {

    private $settings_api;

    function __construct() {

        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') ); 
    }

    /**
     * Admin init hook
     * @return void 
     */
    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    /**
     * Admin Menu CB
     * @return void 
     */
    function admin_menu() {
        add_menu_page( __( 'İletimerkezi SMS', 'satosms' ), __( 'İletimerkezi SMS', 'satosms' ), 'manage_options', 'sat-order-sms-notification-settings', array( $this, 'plugin_page' ), plugins_url( 'iletiMerkeziSMS/images/im-icon.png' ));
    }

    /**
     * Get All settings Field
     * @return array 
     */
    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'satosms_general',
                'title' => __( 'Mesaj Ayarları', 'satosms' )
            ),
            array(
                'id' => 'satosms_gateway',
                'title' => __( 'İletimerkezi Ayarları', 'satosms' )
            ),

            array(
                'id' => 'satosms_message',
                'title' => __( 'Mesaj İçeriği', 'satosms' )
            )
        );
        return apply_filters( 'satosms_settings_sections' , $sections );
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {

        $order_statuses = $this->order_statuses();        
        $buyer_message = "[order_id] nolu siparişiniz, [order_status] durumundadır. \nBizi seçtiğiniz için teşekkürler"; 
        $admin_message = "Yeni bir sipariş var.\n[order_id] nolu siparişin durumu : [order_status]\n";    
        $settings_fields = array(

            'satosms_general' => apply_filters( 'satosms_general_settings', array(
                array(
                    'name' => 'enable_notification',
                    'label' => __( 'Yeni sipariş için SMS', 'satosms' ),
                    'desc' => __( 'Aktif edilirse yeni siparişler için SMS göndermeyi aktif eder.', 'satosms' ),
                    'type' => 'checkbox',
                ),

                array(
                    'name' => 'admin_notification',
                    'label' => __( 'Admin için SMS', 'satosms' ),
                    'desc' => __( 'Aktif edilirse yeni siparişlerde admine mesaj göndermeyi aktif eder.', 'satosms' ),
                    'type' => 'checkbox',
                    'default' => 'on'
                ),

                array(
                    'name' => 'buyer_notification',
                    'label' => __( 'Satın almada SMS seçeneği göster', 'satosms' ),
                    'desc' => __( 'Aktif edilirse satın alma ekranında SMS alma seçeneği gösterir.', 'satosms' ),
                    'type' => 'checkbox',
                ),

                array(
                    'name' => 'force_buyer_notification',
                    'label' => __( 'SMS seçeneğine zorla', 'satosms' ),
                    'desc' => __( 'Aktif edilirse SMS ile bilgilendirme seçeneği zorunlı olur.', 'satosms' ),
                    'type' => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'   => 'No'
                    )
                ),

                array(
                    'name' => 'buyer_notification_text',
                    'label' => __( 'SMS seçeneği yazısı', 'satosms' ),
                    'desc' => __( 'Satın alma ekranında SMS seçeneğinin yanında çıkacak yazı ', 'satosms' ),
                    'type' => 'textarea',
                    'default' => 'Ürün durumlarını bana mesaj ile bildir.'
                ),
                array(
                    'name' => 'order_status',
                    'label' => __( 'Ürün durumu değişimi ', 'satosms' ),
                    'desc' => __( 'Ürün durumu değiştiğinde SMS gönderir.', 'satosms' ),
                    'type' => 'multicheck',
                    'options' => $order_statuses
                )
            ) ),

            /*'satosms_gateway' => apply_filters( 'satosms_gateway_settings',  array(
                array(
                    'name' => 'sms_gateway',
                    'label' => __( 'Select your Gateway', 'satosms' ),
                    'desc' => __( 'Select your sms gateway', 'satosms' ),
                    'type' => 'select',
                    'default' => '-1',
                    'options' => $this->get_sms_gateway()
                ),
            ) ),*/

            'satosms_message' => apply_filters( 'satosms_message_settings',  array(
                array(
                    'name' => 'sms_admin_phone',
                    'label' => __( 'Admin Gsm numarası', 'satosms' ),
                    'desc' => __( '<br>Admine SMS ayarları aktifse mesaj gönderilecek numara.', 'satosms' ),
                    'type' => 'text'
                ),
                array(
                    'name' => 'admin_sms_body',
                    'label' => __( 'Admin SMS içeriği', 'satosms' ),
                    'desc' => __( ' Admine gidecek SMS\'i düzenleyebilirsiniz. Kullanabileceğiniz değişkenler; <code>[order_id]</code> , <code>[order_status]</code>', 'satosms' ),
                    'type' => 'textarea',
                    'default' => __( $admin_message, 'satosms' )
                ),

                array(
                    'name' => 'sms_body',
                    'label' => __( 'Satın alana gidecek SMS ', 'satosms' ),
                    'desc' => __( ' Satın alana gidecek SMS\'i düzenleyebilirsiniz. Kullanabileceğiniz değişkenler; <code>[order_id]</code> , <code>[order_status]</code> <br> Kargo durumları için yith ile ortak çalışan değişkenler ; <code>[ywot_carrier_name]</code>,<code>[ywot_pick_up_date]</code>,<code>[ywot_tracking_code]</code>', 'satosms' ),
                    'type' => 'textarea',
                    'default' => __( $buyer_message, 'satosms' )
                ),
            ) ),
        );

        return apply_filters( 'satosms_settings_
            section_content', $settings_fields );
    }

    public function order_statuses(){

       $status_posts = get_posts( array(
                'posts_per_page' => -1,
                'post_type'      => 'yith-wccos-ostatus',
                'post_status'    => 'publish'
            ) );
        foreach ( $status_posts as $sp ) {
            $statuses[ get_post_meta( $sp->ID, 'slug', true ) ] = $sp->post_title;
        }
        $default_statuses = array('pending' => __('Ödeme Bekliyor', 'satosms' ),
                                  'processing' => __('İşleniyor', 'satosms'),
                                  'on-hold' => __('Beklemede', 'satosms'),
                                  'completed' => __('Tamamlandı','satosms'),
                                  'cancelled' => __('İptal Edildi', 'satosms'),
                                  'refunded' => __('İade Edildi', 'satosms'),
                                  'failed' => __('Başarısız','satosms'));
        if (!empty($statuses)) {
           $order_statuses = array_merge($statuses,$default_statuses);
            return $order_statuses;
        }
        return $default_statuses;
    }

    /**
     * Loaded Plugin page
     * @return void
     */
    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

    /**
     * Get sms Gateway settings
     * @return array 
     */
    function get_sms_gateway() {
        $gateway = array( 
            'iletimerkezi' => __( 'İletimerkezi', 'satosms' ),
        );

        return apply_filters( 'satosms_sms_gateway', $gateway );
    }

} // End of SatSMS_Setting_Options Class

/**
 * SMS Gateway Settings Extra panel options
 * @return void 
 */

 function getBalance($api_username,$api_password) {


        $xml = <<<EOS
        <request>
            <authentication>
                <username>{$api_username}</username>
                <password>{$api_password}</password>
            </authentication>
        </request>
EOS;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'http://api.iletimerkezi.com/v1/get-balance');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);
        preg_match_all('|\<sms\>.*\<\/sms\>|U', $result, $matches,PREG_PATTERN_ORDER);
        
        if(isset($matches[0])&&isset($matches[0][0])) {
            return $matches[0][0];  
        }
        
        return '';
    }



function satosms_settings_field_gateway() {

    $talkwithtext_username   = satosms_get_option( 'talkwithtext_username', 'satosms_gateway', '' ); 
    $talkwithtext_password   = satosms_get_option( 'talkwithtext_password', 'satosms_gateway', '' ); 
    $talkwithtext_originator = satosms_get_option( 'talkwithtext_originator', 'satosms_gateway', '' ); 
    
    $iletimerkezi_username   = satosms_get_option( 'iletimerkezi_username', 'satosms_gateway', '' ); 
    $iletimerkezi_password   = satosms_get_option( 'iletimerkezi_password', 'satosms_gateway', '' ); 
    $iletimerkezi_originator = satosms_get_option( 'iletimerkezi_originator', 'satosms_gateway', '' ); 

    $clickatell_name         = satosms_get_option( 'clickatell_name', 'satosms_gateway', '' ); 
    $clickatell_password     = satosms_get_option( 'clickatell_password', 'satosms_gateway', '' ); 
    $clickatell_api          = satosms_get_option( 'clickatell_api', 'satosms_gateway', '' ); 

    $twt_helper        = sprintf( 'Please fill talk with text username and password. If not then visit <a href="%s" target="_blank">%s</a>', 'http://my.talkwithtext.com/', 'Talk With Text' );
    $clickatell_helper = sprintf( 'Please fill Clickatell informations. If not then go to <a href="%s" target="_blank">%s</a> and get your informations', 'https://www.clickatell.com/login/', 'Clickatell');
    
    $balance = getBalance($iletimerkezi_username,$iletimerkezi_password);
    if (!$balance) {
        $iletimerkezi_helper2 = sprintf( 'Mesaj gönderebilmek için giriş bilgileriniz doldurun. Eğer bilmiyorsanız <a href="%s" target="_blank">%s</a> bilgi alabilirsiniz.', 'https://www.iletimerkezi.com', 'İleti Merkezi\'nden');
    }else{
    $iletimerkezi_helper = sprintf( 'Mevcut bakiyeniz :'.$balance.' <a target="_blank" href="https://www.iletimerkezi.com/index.php?function=default&obj1=signinViaGet&gsm='.$iletimerkezi_username.'&password='.$iletimerkezi_password.'">SMS Satın Al!</a>');
    }
    ?>
    
    <?php do_action( 'satosms_gateway_settings_options_before' ); ?>

    <div class="talkwithtext_wrapper hide_class">
        <hr>
        <p style="margin-top:15px; margin-bottom:0px; padding-left: 20px; font-style: italic; font-size: 14px;">
            <strong><?php _e( $twt_helper, 'satosms' ); ?></strong>
        </p>
        <table class="form-table">
            <tr valign="top">
                <th scrope="row"><?php _e( 'Talk with text Username', 'satosms' ); ?></th>
                <td>
                    <input type="text" name="satosms_gateway[talkwithtext_username]" id="satosms_gateway[talkwithtext_username]" value="<?php echo $talkwithtext_username; ?>">
                    <span><?php _e( 'The HTTP API username that is supplied to your account (most of the times it is your email)', 'satosms' ); ?></span>
                </td>
            </tr>

            <tr valign="top">
                <th scrope="row"><?php _e( 'Talk with text Password', 'satosms' ); ?></th>
                <td>
                    <input type="text" name="satosms_gateway[talkwithtext_password]" id="satosms_gateway[talkwithtext_password]" value="<?php echo $talkwithtext_password; ?>">
                    <span><?php _e( 'The HTTP API password of your account', 'satosms' ); ?></span>
                </td>
            </tr>

            <tr valign="top">
                <th scrope="row"><?php _e( 'Talk with text Originator', 'satosms' ); ?></th>
                <td>
                    <input type="text" name="satosms_gateway[talkwithtext_originator]" id="satosms_gateway[talkwithtext_originator]" value="<?php echo $talkwithtext_originator; ?>">
                    <span><?php _e( 'The originator of your message (11 alphanumeric or 14 numeric values)', 'satosms' ); ?></span>
                </td>
            </tr>
        </table>
    </div>

    <div class="clickatell_wrapper hide_class">
        <hr>
        <p style="margin-top:15px; margin-bottom:0px; padding-left: 20px; font-style: italic; font-size: 14px;">
            <strong><?php _e( $clickatell_helper, 'satosms' ); ?></strong>
       </p>
        <table class="form-table">
            <tr valign="top">
                <th scrope="row"><?php _e( 'Clickatell name', 'satosms' ) ?></th>
                <td>
                    <input type="text" name="satosms_gateway[clickatell_name]" id="satosms_gateway[clickatell_name]" value="<?php echo $clickatell_name; ?>">
                    <span><?php _e( 'Clickatell Username', 'satosms' ); ?></span> 
                </td>
            </tr>

            <tr valign="top">
                <th scrope="row"><?php _e( 'Clickatell Password', 'satosms' ) ?></th>
                <td>
                    <input type="text" name="satosms_gateway[clickatell_password]" id="satosms_gateway[clickatell_password]" value="<?php echo $clickatell_password; ?>">
                    <span><?php _e( 'Clickatell password', 'satosms' ); ?></span> 
                </td>
            </tr>

            <tr valign="top">
                <th scrope="row"><?php _e( 'Clickatell api', 'satosms' ) ?></th>
                <td>
                    <input type="text" name="satosms_gateway[clickatell_api]" id="satosms_gateway[clickatell_api]" value="<?php echo $clickatell_api; ?>">
                    <span><?php _e( 'Clickatell API id', 'satosms' ); ?></span> 
                </td>
            </tr>
        </table>
    </div>

    <div class="iletimerkezi_wrapper">
        <hr>
        <p style="margin-top:15px; margin-bottom:0px; padding-left: 20px; font-style: italic; font-size: 14px; color:red;">
            <strong><?php _e( $iletimerkezi_helper, 'satosms' ); ?></strong>
       </p>
       <p style="margin-top:15px; margin-bottom:0px; padding-left: 20px; font-style: italic; font-size: 14px; ">
            <strong><?php _e( $iletimerkezi_helper2, 'satosms' ); ?></strong>
       </p>
        <table class="form-table">
            <tr valign="top">
                <th scrope="row"><?php _e( 'Kullanıcı Adı', 'satosms' ) ?></th>
                <td>
                    <input type="text" name="satosms_gateway[iletimerkezi_username]" id="satosms_gateway[iletimerkezi_username]" value="<?php echo $iletimerkezi_username; ?>">
                    <span><?php _e( '', 'satosms' ); ?></span> 
                </td>
            </tr>

            <tr valign="top">
                <th scrope="row"><?php _e( 'Şifre', 'satosms' ) ?></th>
                <td>
                    <input type="password" name="satosms_gateway[iletimerkezi_password]" id="satosms_gateway[iletimerkezi_password]" value="<?php echo $iletimerkezi_password; ?>">
                    <span><?php _e( '', 'satosms' ); ?></span> 
                </td>
            </tr>

            <tr valign="top">
                <th scrope="row"><?php _e( 'Başlık', 'satosms' ) ?></th>
                <td>
                    <input type="text" name="satosms_gateway[iletimerkezi_originator]" id="satosms_gateway[iletimerkezi_originator]" value="<?php echo $iletimerkezi_originator; ?>">
                    <span><?php _e( '', 'satosms' ); ?></span> 
                </td>
            </tr>
        </table>
    </div>

    <?php do_action( 'satosms_gateway_settings_options_after' ) ?>
    <?php
}

// hook for Settings API for adding extra sections
add_action( 'wsa_form_bottom_satosms_gateway', 'satosms_settings_field_gateway' );

