<?php

/**
 * SMS Gateway handler class
 *
 * @author iletimerkezi
 */
class imSMSGateway {

    private static $_instance;

    public static function init()
    {
        if ( !self::$_instance ) {
            self::$_instance = new imSMSGateway();
        }

        return self::$_instance;
    }

    private function _sendRequest($data)
    {
        $url = $data['url'];
        $xml = $data['xml'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

     private function _iletimerkeziAuth()
     {

        $username = im_tickera_get_option( 'iletimerkezi_username', 'iletimerkezisms_gateway', '' );
        $password = im_tickera_get_option( 'iletimerkezi_password', 'iletimerkezisms_gateway', '' );

        if( empty( $username ) || empty( $password ) ) {
            return;
        }

        $msg = $sms_data['sms_body'];

        $xml = "
                            <authentication>
                                    <username>{$username}</username>
                                    <password>{$password}</password>
                            </authentication>
                ";
        return $xml;

    }

    public function imSendSMS( $smsdata )
    {

        $originator = im_tickera_get_option( 'iletimerkezi_originator', 'iletimerkezisms_gateway', '' );
        $auth = self::_iletimerkeziAuth();
        $receipent = $smsdata['number'];
        $receipent = preg_replace('/\D/','',$receipent);
        $receipent = substr($receipent, -10);
        $msg = $smsdata['sms_body'];
        $order =  "
                <order>
                    <sender>{$originator}</sender>
                    <sendDateTime></sendDateTime>
                    <message>
                        <text><![CDATA[".$msg."]]></text>
                        <receipents>
                            <number>{$receipent}</number>
                        </receipents>
                    </message>
                </order>
                ";
        $xml = "<request>".$auth.$order."</request>";
        $data['url'] = 'https://api.iletimerkezi.com/v1/send-sms';
        $data['xml'] = $xml;
        $response = self::_sendRequest($data);
        preg_match_all('|\<code\>.*\<\/code\>|U', $response, $matches,PREG_PATTERN_ORDER);
        if(isset($matches[0])&&isset($matches[0][0])) {
             if( $matches[0][0] == '<code>200</code>' ) {
                return true;
             }
        }
        return false;
    }

    public function imGetDomain()
    {
        $auth = self::_iletimerkeziAuth();
        $pluginuser = "
            <pluginUser>
                <site><![CDATA[".$domain."]]></site>
                <name>tickera</name>
            </pluginUser>
            ";
        $xml = "<request>".$auth.$pluginuser."</request>";
        $data['url'] = 'https://api.iletimerkezi.com/v1/add-plugin-user';
        $data['xml'] = $xml;
        $response = self::_sendRequest($data);
        return true;
    }

    public function imGetBalance()
    {
        $auth = self::_iletimerkeziAuth();
        $xml = "<request>".$auth."</request>";
        $data['url'] = 'https://api.iletimerkezi.com/v1/get-balance';
        $data['xml'] = $xml;
        $response = self::_sendRequest($data);
        preg_match_all('|\<sms\>.*\<\/sms\>|U', $response, $matches,PREG_PATTERN_ORDER);
        if(isset($matches[0])&&isset($matches[0][0])) {
            return $matches[0][0];
        }
        return '';
    }

}