<?php

/**
 * SMS Gateway handler class
 *
 * @author iletimerkezi
 */
class iletimerkeziSMSGateway {

    private static $_instance;

    public static function init()
    {
        if ( !self::$_instance ) {
            self::$_instance = new iletimerkeziSMSGateway();
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

        $username = iletimerkezisms_get_option( 'iletimerkezi_username', 'iletimerkezisms_gateway', '' );
        $password = iletimerkezisms_get_option( 'iletimerkezi_password', 'iletimerkezisms_gateway', '' );

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

    public function iletimerkeziSendSMS( $smsdata )
    {

        $originator = iletimerkezisms_get_option( 'iletimerkezi_originator', 'iletimerkezisms_gateway', '' );
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
        $data['url'] = 'http://api.iletimerkezi.com/v1/send-sms';
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

}
