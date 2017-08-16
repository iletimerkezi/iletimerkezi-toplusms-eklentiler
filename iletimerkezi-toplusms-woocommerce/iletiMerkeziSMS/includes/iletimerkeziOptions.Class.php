<?php

/**
 * WordPress admin Options class
 *
 * @author iletimerkezi
 */

class iletimerkeziOptions {

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
        add_menu_page( __( 'İletimerkezi SMS', 'iletimerkezisms' ), __( 'İletimerkezi SMS', 'iletimerkezisms' ), 'manage_options', 'sat-order-sms-notification-settings', array( $this, 'plugin_page' ), plugins_url( 'iletiMerkeziSMS/admin/images/im-icon.png' ));
    }

    /**
     * Get All settings Field
     * @return array
     */
    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'iletimerkezisms_general',
                'title' => __( 'Mesaj Ayarları', 'iletimerkezisms' )
            ),
            array(
                'id' => 'iletimerkezisms_gateway',
                'title' => __( 'İletimerkezi Ayarları', 'iletimerkezisms' )
            ),

            array(
                'id' => 'iletimerkezisms_message',
                'title' => __( 'Mesaj İçeriği', 'iletimerkezisms' )
            )
        );
        return apply_filters( 'iletimerkezisms_settings_sections' , $sections );
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
        $order_statuses = $this->order_statuses();
        $order_array = array();
        foreach ($order_statuses as $key => $value) {
            $order_array[] = array(
                                'name' => 'order_'.$key ,
                                'label' => __('Ürün durumlarında SMS gönder', 'iletimerkezisms'),
                                'desc' => __('Ürün durumu <b>'. $value .'</b> olduğunda SMS gönderir.', 'iletimerkezisms'),
                                'type' => 'textarea',
                                'default' => __( $buyer_message, 'iletimerkezisms')
                        );
        }
        $settings_fields = array(

            'iletimerkezisms_general' => apply_filters( 'iletimerkezisms_general_settings', array(
                array(
                    'name' => 'enable_notification',
                    'label' => __( 'Yeni sipariş için SMS', 'iletimerkezisms' ),
                    'desc' => __( 'Aktif edilirse yeni siparişler için SMS göndermeyi aktif eder.', 'iletimerkezisms' ),
                    'type' => 'checkbox',
                ),

                array(
                    'name' => 'admin_notification',
                    'label' => __( 'Admin için SMS', 'iletimerkezisms' ),
                    'desc' => __( 'Aktif edilirse yeni siparişlerde admine mesaj göndermeyi aktif eder.', 'iletimerkezisms' ),
                    'type' => 'checkbox',
                    'default' => 'on'
                ),

                array(
                    'name' => 'force_sms',
                    'label' => __('SMS Gönderme isteğe bağlı değildir', 'iletimerkezisms'),
                    'desc' => __('Aktif edilirse seçeneğe bağlı olmadan SMS gönderimini aktif eder, SMS seçeneğini pasifleştirir.','iletimerkezisms'),
                    'type' => 'checkbox',
                    ),

                array(
                    'name' => 'buyer_notification',
                    'label' => __( 'Satın almada SMS seçeneği göster', 'iletimerkezisms' ),
                    'desc' => __( 'Aktif edilirse satın alma ekranında SMS alma seçeneği gösterir.', 'iletimerkezisms' ),
                    'type' => 'checkbox',
                ),

                array(
                    'name' => 'force_buyer_notification',
                    'label' => __( 'SMS seçeneğine zorla', 'iletimerkezisms' ),
                    'desc' => __( 'Aktif edilirse SMS ile bilgilendirme seçeneği zorunlı olur.', 'iletimerkezisms' ),
                    'type' => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Evet',
                        'no'   => 'Hayır'
                    )
                ),

                array(
                    'name' => 'buyer_notification_text',
                    'label' => __( 'SMS seçeneği yazısı', 'iletimerkezisms' ),
                    'desc' => __( 'Satın alma ekranında SMS seçeneğinin yanında çıkacak yazı ', 'iletimerkezisms' ),
                    'type' => 'textarea',
                    'default' => 'Ürün durumlarını bana mesaj ile bildir.'
                ),
                array(
                    'name' => 'order_status',
                    'label' => __( 'Ürün durumu değişimi ', 'iletimerkezisms' ),
                    'desc' => __( 'Ürün durumu değiştiğinde SMS gönderir.', 'iletimerkezisms' ),
                    'type' => 'multicheck',
                    'options' => $order_statuses
                )
            ) ),

            /*'iletimerkezisms_gateway' => apply_filters( 'iletimerkezisms_gateway_settings',  array(
                array(
                    'name' => 'sms_gateway',
                    'label' => __( 'Select your Gateway', 'iletimerkezisms' ),
                    'desc' => __( 'Select your sms gateway', 'iletimerkezisms' ),
                    'type' => 'select',
                    'default' => '-1',
                    'options' => $this->get_sms_gateway()
                ),
            ) ),*/

            'iletimerkezisms_message' => apply_filters( 'iletimerkezisms_message_settings',  array_merge(array(
                array(
                    'name' => 'sms_admin_phone',
                    'label' => __( 'Admin Gsm numarası', 'iletimerkezisms' ),
                    'desc' => __( '<br>Admine SMS ayarları aktifse mesaj gönderilecek numara.', 'iletimerkezisms' ),
                    'type' => 'text'
                ),
                array(
                    'name' => 'admin_sms_body',
                    'label' => __( 'Admin SMS içeriği', 'iletimerkezisms' ),
                    'desc' => __( ' Admine gidecek SMS\'i düzenleyebilirsiniz. Kullanabileceğiniz değişkenler; <code>[order_id]</code> , <code>[order_status]</code> , <code>[buyer_number]</code>', 'iletimerkezisms' ),
                    'type' => 'textarea',
                    'default' => __( $admin_message, 'iletimerkezisms' )
                ),

                array(
                    'name' => 'sms_hint',
                    'label' => __( 'Kullanılabilecek değişkenler ', 'iletimerkezisms' ),
                    'desc' => __( 'Ürün durumları değiştiğinde kullanabileceğiniz değişkenler; <code>[order_id]</code> , <code>[order_status]</code> <br> Kargo durumları için yith ile ortak çalışan değişkenler ; <code>[ywot_carrier_name]</code>,<code>[ywot_pick_up_date]</code>,<code>[ywot_tracking_code]</code>', 'iletimerkezisms' ),
                    'type' => 'html',
                ),
            ), $order_array )
            ),
        );

        return apply_filters( 'iletimerkezisms_settings_
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
        $default_statuses = array('pending' => __('Ödeme Bekliyor', 'iletimerkezisms' ),
                                  'processing' => __('İşleniyor', 'iletimerkezisms'),
                                  'on-hold' => __('Beklemede', 'iletimerkezisms'),
                                  'completed' => __('Tamamlandı','iletimerkezisms'),
                                  'cancelled' => __('İptal Edildi', 'iletimerkezisms'),
                                  'refunded' => __('İade Edildi', 'iletimerkezisms'),
                                  'failed' => __('Başarısız','iletimerkezisms'));
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
            'iletimerkezi' => __( 'İletimerkezi', 'iletimerkezisms' ),
        );

        return apply_filters( 'iletimerkezisms_sms_gateway', $gateway );
    }

}

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
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);
        preg_match_all('|\<sms\>.*\<\/sms\>|U', $result, $matches,PREG_PATTERN_ORDER);

        if(isset($matches[0])&&isset($matches[0][0])) {
            return $matches[0][0];
        }

        return '';
    }

    function getDomain($api_username,$api_password) {

        $domain = $_SERVER['HTTP_HOST'];
        $xml = "
        <request>
            <authentication>
                <username>{$api_username}</username>
                <password>{$api_password}</password>
            </authentication>
            <pluginUser>
                        <site><![CDATA[".$domain."]]></site>
                        <name>woocommerce</name>
                </pluginUser>
        </request>
        ";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'http://api.iletimerkezi.com/v1/add-plugin-user');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);
        return true;
    }



function iletimerkezisms_settings_field_gateway() {

    $iletimerkezi_username   = iletimerkezisms_get_option( 'iletimerkezi_username', 'iletimerkezisms_gateway', '' );
    $iletimerkezi_password   = iletimerkezisms_get_option( 'iletimerkezi_password', 'iletimerkezisms_gateway', '' );
    $iletimerkezi_originator = iletimerkezisms_get_option( 'iletimerkezi_originator', 'iletimerkezisms_gateway', '' );

    $balance = getBalance($iletimerkezi_username,$iletimerkezi_password);
    $getdomain = getDomain($iletimerkezi_username,$iletimerkezi_password);
    if (!$balance) {
        $iletimerkezi_helper2 = sprintf( 'Mesaj gönderebilmek için giriş bilgileriniz doldurun. Eğer bilmiyorsanız <a href="%s" target="_blank">%s</a> bilgi alabilirsiniz.', 'https://www.iletimerkezi.com', 'İleti Merkezi\'nden');
    }else{
    $iletimerkezi_helper = sprintf( 'Mevcut bakiyeniz :'.$balance.' <a target="_blank" href="https://www.iletimerkezi.com/index.php?function=default&obj1=signinViaGet&gsm='.$iletimerkezi_username.'&password='.$iletimerkezi_password.'">SMS Satın Al!</a>');
    }
    ?>

    <?php do_action( 'iletimerkezisms_gateway_settings_options_before' ); ?>


    <div class="iletimerkezi_wrapper">
        <hr>
        <p style="margin-top:15px; margin-bottom:0px; padding-left: 20px; font-style: italic; font-size: 14px; color:red;">
            <strong><?php _e( $iletimerkezi_helper, 'iletimerkezisms' ); ?></strong>
       </p>
       <p style="margin-top:15px; margin-bottom:0px; padding-left: 20px; font-style: italic; font-size: 14px; ">
            <strong><?php _e( $iletimerkezi_helper2, 'iletimerkezisms' ); ?></strong>
       </p>
        <table class="form-table">
            <tr valign="top">
                <th scrope="row"><?php _e( 'Kullanıcı Adı', 'iletimerkezisms' ) ?></th>
                <td>
                    <input type="text" name="iletimerkezisms_gateway[iletimerkezi_username]" id="iletimerkezisms_gateway[iletimerkezi_username]" value="<?php echo $iletimerkezi_username; ?>">
                    <span><?php _e( '', 'iletimerkezisms' ); ?></span>
                </td>
            </tr>

            <tr valign="top">
                <th scrope="row"><?php _e( 'Şifre', 'iletimerkezisms' ) ?></th>
                <td>
                    <input type="password" name="iletimerkezisms_gateway[iletimerkezi_password]" id="iletimerkezisms_gateway[iletimerkezi_password]" value="<?php echo $iletimerkezi_password; ?>">
                    <span><?php _e( '', 'iletimerkezisms' ); ?></span>
                </td>
            </tr>

            <tr valign="top">
                <th scrope="row"><?php _e( 'Başlık', 'iletimerkezisms' ) ?></th>
                <td>
                    <input type="text" name="iletimerkezisms_gateway[iletimerkezi_originator]" id="iletimerkezisms_gateway[iletimerkezi_originator]" value="<?php echo $iletimerkezi_originator; ?>">
                    <span><?php _e( '', 'iletimerkezisms' ); ?></span>
                </td>
            </tr>
        </table>
    </div>

    <?php do_action( 'iletimerkezisms_gateway_settings_options_after' ) ?>
    <?php
}

// hook for Settings API for adding extra sections
add_action( 'wsa_form_bottom_iletimerkezisms_gateway', 'iletimerkezisms_settings_field_gateway' );

