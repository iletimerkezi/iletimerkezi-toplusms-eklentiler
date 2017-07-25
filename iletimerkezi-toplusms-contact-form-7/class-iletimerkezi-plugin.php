<?php
abstract class Iletimerkezi_Plugin {


  public $plugin_callback = null;
  public $plugin_dir = null;

  public function __construct() {

    register_activation_hook( __FILE__, array( $this, 'install' ) );

    add_action( 'admin_head', array( $this, 'setup_admin_head' ) );
    add_action( 'admin_menu', array( $this, 'setup_admin_navigation' ) );
    add_action( 'admin_init', array( $this, 'setup_admin_init' ) );

    $this->plugin_callback = array( $this, 'main' );
  }


  public function install() {
  }

  public function setup_admin_navigation() {
    global $menu;

    $menu_exists = false;
    foreach( $menu as $k => $item ) {
      if( $item[0] == "Iletimerkezi SMS" ) {
        $menu_exists = true;
        break;
      }
    }

    if( !$menu_exists ) {
      add_menu_page( __( 'iletimerkezi SMS', $this->language_string ), __( 'iletimerkezi SMS', $this->language_string ), 'manage_options', 'iletimerkezi_options', array( $this, 'iletimerkezi_options' ), plugins_url( 'contact-form-7-iletimerkezi-sms/images/im-icon.png', dirname( __FILE__ ) ) );
      add_submenu_page( 'iletimerkezi_options', __( 'iletimerkezi Ayarlar', $this->language_string ), __( 'iletimerkezi Ayarlar', $this->language_string ), 'manage_options', 'iletimerkezi_options', array( $this, 'iletimerkezi_options' ) );
    }

    add_submenu_page( 'iletimerkezi_options', __( $this->plugin_name, $this->language_string ), __( $this->plugin_name, $this->language_string ), 'manage_options', $this->plugin_callback[1], $this->plugin_callback );
  }

  public function setup_admin_init() {
    register_setting( 'iletimerkezi_options', 'iletimerkezi_options', '');

    add_settings_section( 'iletimerkezi_balance', 'Bakiyeniz', array( $this, 'getIletimerkeziBalance' ), 'iletimerkezi' );

    add_settings_section( 'iletimerkezi_api_username', 'Gsm numaraniz', array( $this, 'getIletimerkeziUserName' ), 'iletimerkezi' );
    add_settings_field( 'iletimerkezi_api_username', 'Gsm numaraniz', array( $this, 'getIletimerkeziUserNameInput' ), 'iletimerkezi','iletimerkezi_api_username');

    add_settings_section( 'iletimerkezi_api_pass', 'Şifreniz', array( $this, 'getIletimerkeziPass' ), 'iletimerkezi' );
    add_settings_field( 'iletimerkezi_api_pass', 'Şifreniz', array( $this, 'getIletimerkeziPassInput' ), 'iletimerkezi','iletimerkezi_api_pass');

    add_settings_section( 'iletimerkezi_defaults', 'Başlık Ayarları','', 'iletimerkezi' );
    add_settings_field( 'iletimerkezi_from', 'Onaylanmış başlık bilgisi', array( $this, 'settings_from_input' ), 'iletimerkezi', 'iletimerkezi_defaults' );
  }


  function getIletimerkeziUserName() {
      echo 'iletimerkezi.com kullanici adiniz.';
  }

  function getIletimerkeziPass() {
      echo 'iletimerkezi.com sifreniz.';
  }

  function getIletimerkeziUserNameInput() {
      $options = get_option( 'iletimerkezi_options' );
      // echo var_export($options,1);
      echo "<input id='iletimerkezi_api_username' name='iletimerkezi_options[api_username]' size='40' type='text' value='{$options['api_username']}' />";
  }

  function getIletimerkeziPassInput() {
      $options = get_option( 'iletimerkezi_options' );
      // echo var_export($options,1);
      echo "<input id='iletimerkezi_api_pass' name='iletimerkezi_options[api_pass]' size='40' type='text' value='{$options['api_pass']}' />";
  }

  public function settings_from_input() {

    $options = get_option( 'iletimerkezi_options' );
    if( isset( $options['sender'] ) ) {
      echo "<input id='iletimerkezi_sender' name='iletimerkezi_options[sender]' size='40' maxlength='14' type='text' value='{$options['sender']}' />";
    } else {
      echo "<input id='iletimerkezi_sender' name='iletimerkezi_options[sender]' size='40' maxlength='14' type='text' value='' />";
    }

  }

  function getIletimerkeziBalance() {

    $options = get_option( 'iletimerkezi_options' );

    if ($options[api_username] && $options[api_pass]) {
      $xml = '<request>

                      <authentication>

                              <username>'.$options[api_username].'</username>

                              <password>'.$options[api_pass].'</password>

                      </authentication>

              </request>' ;

      $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'http://api.iletimerkezi.com/v1/get-balance');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $result = curl_exec($ch);
        preg_match_all('|\<sms\>.*\<\/sms\>|U', $result, $matches,PREG_PATTERN_ORDER);

        if(isset($matches[0])&&isset($matches[0][0])) {
          echo $matches[0][0].' <font color = "red"><a href = "https://www.iletimerkezi.com/index.php?function=default&obj1=signinViaGet&gsm='.$options[api_username].'&password='.$options[api_pass].'" target = _blank> Satın Al!</a></font>';
        }else{
          echo '<font color = "red"><b>Giriş bilgileriniz hatalı!</b></font>';
        }
    }else{
    echo 'Bakiyenizi görmek için giriş yapmalısınız!';
    }
  }

  function getDomain() {

        $domain = $_SERVER['HTTP_HOST'];
        $options = get_option( 'iletimerkezi_options' );
        $username = $options[api_username];
        $password = $options[api_pass];
        $xml = "
        <request>
            <authentication>
                <username>{$username}</username>
                <password>{$password}</password>
            </authentication>
            <pluginUser>
                        <site><![CDATA[".$domain."]]></site>
                        <name>wordpressotp</name>
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
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);
        return true;
    }

  public function iletimerkezi_options() {
    $this->render_template( 'iletimerkezi-options' );
  }

  protected function show_admin_message( $message, $errormsg = false ) {
    if( $errormsg ) {
      echo '<div id="message" class="error">';
    } else {
      echo '<div id="message" class="updated fade">';
    }

    echo "<p><strong>$message</strong></p></div>";
  }

  protected function render_template( $name, $data = array() ) {
    include( WP_PLUGIN_DIR . '/' . $this->plugin_dir . '/templates/' . $name . '.php');
  }

}
