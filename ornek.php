<?php

function sendRequest($site_name,$send_xml,$header_type)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$site_name);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$send_xml);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$header_type);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $result = curl_exec($ch);

    return $result;
}

function sendSMS($data)
{
    $apikey     = $data['api_key'];
    $apisecret  = $data['api_secret'];
    $apisecret  = hash_hmac('sha256', $apikey, $apisecret);
    $orgin      = $data['orgin'];
    $receipents = $data['receip'];
    $url        = $data['apiurl'];
    @$senddate  = $data['date'];
    $message    = $data['message'];
    $xml = "
                        <request>
                                <authentication>
                                        <key>{$apikey}</key>
                                        <hash>{$apisecret}</hash>
                                </authentication>
                                <order>
                                    <sender>{$orgin}</sender>
                                    <sendDateTime>{$senddate}</sendDateTime>
                                    <message>
                                        <text><![CDATA[".$message."]]></text>
                                        <receipents>
                                            <number>{$receipents}></number>
                                        </receipents>
                                    </message>
                                </order>
                        </request>
    ";
    $result = sendRequest($url,$xml,array('Content-Type: text/xml'));
    preg_match_all('|\<code\>.*\<\/code\>|U', $result, $matches,PREG_PATTERN_ORDER);
        if(isset($matches[0])&&isset($matches[0][0])) {
             if( $matches[0][0] == '<code>200</code>' ) {
                return 'Gönderim Başarılı';
             }
        }
        return 'Gönderim Başarısız';
}