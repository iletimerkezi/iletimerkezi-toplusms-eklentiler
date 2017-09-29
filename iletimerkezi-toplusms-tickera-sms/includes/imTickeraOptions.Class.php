<?php
require_once PLUGIN_CLASS_PATH.'/imSMSGateway.Class.php';
function instantiate() {
      new imSMSGateway();
    }
Class imTickeraOptions
{
	private $settings_api;
	function __construct()
	{
        $this->settings_api = new WeDevs_Settings_API;
        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    /**
     * Admin init hook
     * @return void
     */
    function admin_init()
    {

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
    function admin_menu()
    {
        add_menu_page( 'İletimerkezi Tickera Eklentisi', 'İleti Merkezi SMS', 'manage_options', 'im-tickera-sms-settings', array( $this, 'plugin_page' ), plugins_url( 'iletimerkezi-tickera-sms/admin/images/im-icon.png' ));
    }

    /**
     * Get All settings Field
     * @return array
     */
    function get_settings_sections()
    {
        $sections = array(
            array(
                'id' => 'iletimerkezisms_general',
                'title' => __( 'Mesaj Ayarları', 'iletimerkezisms' ),
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
    function get_settings_fields()
    {
        $settings_fields = array(

            'iletimerkezisms_general' => apply_filters( 'iletimerkezisms_general_settings', array(
                array(
                    'name' => 'buyer_notification_text',
                    'label' => __( 'SMS seçeneği yazısı', 'iletimerkezisms' ),
                    'desc' => __( 'Satın alma ekranında SMS seçeneğinin yanında çıkacak yazı ', 'iletimerkezisms' ),
                    'type' => 'textarea',
                    'default' => 'Ürün durumlarını bana mesaj ile bildir.',
                    'class' => 'im_registration_table_layout'
                )
            ) ),

            'iletimerkezisms_message' => apply_filters( 'iletimerkezisms_message_settings', array(
                array(
                    'name' => 'sms_admin_phone',
                    'label' => __( 'Admin Gsm numarası', 'iletimerkezisms' ),
                    'desc' => __( '<br>Admine SMS ayarları aktifse mesaj gönderilecek numara.', 'iletimerkezisms' ),
                    'type' => 'text'
                )
            ) )
        );

        return apply_filters( 'iletimerkezisms_settings_section_content', $settings_fields );
    }

     /**
     * Loaded Plugin page
     * @return void
     */
    function plugin_page()
    {
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
    function get_pages()
    {
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
    function get_sms_gateway()
    {
        $gateway = array(
            'iletimerkezi' => __( 'İletimerkezi', 'iletimerkezisms' ),
        );

        return apply_filters( 'iletimerkezisms_sms_gateway', $gateway );
    }
}


function iletimerkezisms_settings_field_gateway()
{

    $iletimerkezi_username   = im_tickera_get_option( 'iletimerkezi_username', 'iletimerkezisms_gateway', '' );
    $iletimerkezi_password   = im_tickera_get_option( 'iletimerkezi_password', 'iletimerkezisms_gateway', '' );
    $iletimerkezi_originator = im_tickera_get_option( 'iletimerkezi_originator', 'iletimerkezisms_gateway', '' );

    $balance = imSMSGateway::imGetBalance();
    $getdomain = imSMSGateway::imGetDomain();
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
        <div style="padding-left: 10px">
            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Değişiklikleri Kaydet"></p>
        </div>
    </div>

    <?php do_action( 'iletimerkezisms_gateway_settings_options_after' ) ?>
    <?php
}

add_action( 'wsa_form_bottom_iletimerkezisms_gateway', 'iletimerkezisms_settings_field_gateway' );
?>
