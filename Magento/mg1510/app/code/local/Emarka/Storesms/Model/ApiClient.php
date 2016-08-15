<?php

//File:  app/code/local/eMarka/Storesms/Model/ApiClient.php

/**
 * SMS API client class
 *
 * @category   eMarka
 * @package    StoresmsApi
 * @copyright  Copyright (c) 2014 eMarka (https://www.iletimerkezi.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Adem ARAS <adem@emarka.com.tr>
 * ...
 */
class Emarka_Storesms_Model_ApiClient {

    public function fixPhoneNumber($number) {

        $number = preg_replace('/\D/','',$number);
        $number = substr($number, -10);

        return $number;
    }

    public function sendSms($number, $message) {

        $number = $this->fixPhoneNumber($number);

        $config = Mage::getModel('storesms/config');

        $iletimerkezi_username = $config->getLogin();
        $iletimerkezi_password = $config->getPassword();
        $iletimerkezi_sender   = $config->getSender();

        $xml = <<<EOS
        <request>
            <authentication>
                <username>{$iletimerkezi_username}</username>
                <password>{$iletimerkezi_password}</password>
            </authentication>
            <order>
                <sender>{$iletimerkezi_sender}</sender>
                <sendDateTime></sendDateTime>
                <message>
                    <text><![CDATA[{$message}]]></text>
                    <receipents>
                        <number>{$number}</number>
                    </receipents>
                </message>
            </order>
        </request>
EOS;

        $result = $this->_connect($xml,true);
        
        if($result)
            $response = simplexml_load_string($result);
        else {
            Mage::log("Bağlantı Hatası : hata kodu 1071", null, Emarka_SMS.log);
            $error = true;
        }

        if($response->status->code==200){
            $report_id = $response->order->id;
            $status = "Gönderiliyor";//1 gönderiliyor
        } elseif($error===true) {//bağlanti hatasi olursa kullaniciya bildir
            $report_id = 0;
            $status = "Sms Gönderim Hatası";//0 hata
        } else {
            /* we must get error */
            $report_id = 0;
            $status = "Tekrar Eden Numara";//0 hata
        }

        $this->_saveSendedSms($report_id, $number, $message, $status);

    }

    /**
    * @desc it save to database for sms report
    * @param int $report_id response id for sms
    * @param string $number mobilephone number
    * @param string $message message text
    * @param int $status sms status
    */
    public function _saveSendedSms($report_id, $number, $message, $status) {

        $resource = Mage::getSingleton('core/resource');
         
        /**
         * Retrieve the write connection
         */
        $writeConnection = $resource->getConnection('core_write');
        $writeConnection->insert("storesms", array(
                'number'   => $number,
                'message'  => $message,
                'response' => $report_id,
                'status'   => $status,
                'created'  => date('Y-m-d H:i:s')
        ));
    }

    public function _connect($xml, $send = false) {
        
        if($send)
            $url = 'http://api.iletimerkezi.com/v1/send-sms';
        else
            $url = 'http://api.iletimerkezi.com/v1/get-report';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);

        return $result;
    }

    /**
    * @desc it give sms report from api
    * @param string $number mobilephone number
    * @param string $message message text
    */
    private function _getSmsReport() {

        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $results = $connection->fetchAll("select * from storesms where status=1 limit 0,5");
        //$results = Db::getInstance()->ExecuteS($sql);
        //die(var_export($results));
        if ($results){

            $config = Mage::getModel('storesms/config');

            $iletimerkezi_username = $config->getLogin();
            $iletimerkezi_password = $config->getPassword();
        //die(var_export($results));
            foreach ($results as $key => $result) {
                
                $report_id = $result['response'];
                
                $xml = <<<EOS
        <request>
            <authentication>
                <username>{$iletimerkezi_username}</username>
                <password>{$iletimerkezi_password}</password>
            </authentication>
            <order>
                <id>{$report_id}</id>
                <page></page>
                <rowCount></rowCount>
            </order>
        </request>
EOS;

                $response = $this->_connect($xml);

                //$response = simplexml_load_string($result);

                $result_matches = array();
                if(preg_match('/<message>(.*?)<number>(.*?)<\/number>(.*?)<status>(.*?)<\/status>(.*?)<\/message>/si', $response, $result_matches)) {
                    $sended_number  = $result_matches[2];
                    $status_message = $result_matches[4];

                    if($status_message=='111') {
                        //return 'success';
                        $this->_updateSmsStatus($result['id'],"Gönderildi");//2
                    } elseif($status_message=='110') {
                        //return '';
                        $this->_updateSmsStatus($result['id'],"Gönderiliyor");//1
                    } else {
                        //return 'error';
                        $this->_updateSmsStatus($result['id'],"Gönderilemedi");//3
                    }
                }

                /*if($response->status->code==200){
                    $this->_updateSmsStatus($result['id'],2);
                }*/

            }
            
        }
            
    }

    /**
    * @desc it update to database for sms report
    * @param int $id sms id
    * @param int $status sms status
    */
    private function _updateSmsStatus($id, $status) {

        $resource = Mage::getSingleton('core/resource');
         
        /**
         * Retrieve the write connection
         */
        $writeConnection = $resource->getConnection('core_write');
        $writeConnection->update("storesms", array(
                'status'   => $status
        ), $id);

    }

    public function getDelieveryReport(){
        //die("getDelieveryReport");
        $this->_getSmsReport();
    }

    /* this is hack for model\corn.php */
    public function saveDelieveryReport() {
        //die("saveDelieveryReport");
        $this->_getSmsReport();
    }

    public function getLogs($data, $file_name = "C:\\wamp\\www\\magento\\log.html") {
        $fp = fopen($file_name, 'a');
        fwrite($fp, "<pre>".var_export($data,1)."</pre>");
        fclose($fp);
    }

}