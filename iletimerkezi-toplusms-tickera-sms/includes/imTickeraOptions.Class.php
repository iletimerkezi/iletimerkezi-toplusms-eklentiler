<?php

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
 ?>
