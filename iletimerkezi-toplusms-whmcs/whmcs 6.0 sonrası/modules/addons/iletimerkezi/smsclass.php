<?php
/* Iletimerkezi SMS Eklentisi
 * whmcsSMS - http://www.whmcssms.com
 */
class iletimerkezi {
    public $params;
    public $gsmnumber;
    public $message;
    public $userid;
    public $sender;
    public $errors = array();
    public $logs   = array();
    
    public function setGsmnumber($number){        
        
        $number = preg_replace('/\D/','',$number);
        $number = substr($number, -10);
        
        $this->gsmnumber = $number;
    }
    
    public function getGsmnumber(){        
        return $this->gsmnumber;
    }
    
    public function setMessage($message){
        $this->message = $message;
    }

    public function getMessage(){
        return $this->message;
    }

    public function setUserid($userid){
        $this->userid = $userid;
    }

    public function getUserid(){        
        return $this->userid;
    }
    
    public function getParams(){
        $settings = $this->getSettings();
        $params   = json_decode($settings['apiparams']);
        return $params;
    }

    public function getSettings(){
        $result = select_query("mod_iletimerkezi_settings", "*");
        return mysql_fetch_array($result);
    }

    function send(){
        
        if (extension_loaded("curl")) {
            
            $params  = $this->getParams();
            $message = $this->message;

            $this->addLog("Params: ".json_encode($params));
            $this->addLog("To: ".$this->getGsmnumber());
            $this->addLog("Message: ".$message);                
            
            $send_xml = '<?xml version="1.0" encoding="UTF-8" ?>
                <request>
                    <authentication>
                        <username>'.$params->iletimerkezi_username.'</username>
                        <password>'.$params->iletimerkezi_password.'</password>
                    </authentication>
                    <order>
                        <sender>'.$params->senderid.'</sender>
                        <sendDateTime></sendDateTime>
                        <message>
                            <text><![CDATA['.$this->message.']]></text>
                            <receipents>
                                <number>'.$this->gsmnumber.'</number>
                            </receipents>
                        </message>                                
                    </order>
                </request>';

            $ch = curl_init('http://api.iletimerkezi.com/v1/send-sms');
            curl_setopt($ch, CURLOPT_MUTE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $send_xml);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            
            // $this->addLog("Sunucudan dönen cevap: ".$result);
            $order_status = false;
            if(preg_match('/<status>(.*?)<code>(.*?)<\/code>(.*?)<message>(.*?)<\/message>(.*?)<\/status>(.*?)<order>(.*?)<id>(.*?)<\/id>(.*?)<\/order>/si', $result, $result_matches)) {
                $status_code    = $result_matches[2];
                $status_message = $result_matches[4];
                $order_id       = $result_matches[8];

                if($status_code == '200') {
                    $order_status = true;
                    $this->addLog("Message sent.");
                } else {
                    $this->addLog("Mesaj gönderilemedi. Hata: $status_message");                
                    $this->addError("Mesaj gönderilirken hata oluştu. Hata: $status_message");
                }

            } else {
                $this->addLog("Mesaj gönderilemedi. Hata: $result");
                $this->addError("Mesaj gönderilirken hata oluştu. Hata: $result");
            }

            
            if(!$order_status){                
                $this->saveToDb($order_id,'error',$this->getErrors(),$this->getLogs());
                return false;
            } else {
                $this->saveToDb($order_id,'',null,$this->getLogs());
                return true;
            }

        } else {
            $this->addLog('Curl extension sunucunuzda yuklu degil.');
            $this->addError('Curl extension sunucunuzda yuklu degil.');
            $this->saveToDb(-1,'error',$this->getErrors(),$this->getLogs());
            return false;
        }
        
    }

    function getBalance(){        
        
        $params  = $this->getParams();
        $balance_xml = '<?xml version="1.0" encoding="UTF-8" ?>
            <request>
                <authentication>
                    <username>'.$params->iletimerkezi_username.'</username>
                    <password>'.$params->iletimerkezi_password.'</password>
                </authentication>
            </request>';

        $ch = curl_init('http://api.iletimerkezi.com/v1/get-balance');
        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $balance_xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        if(preg_match('/<status>(.*?)<code>(.*?)<\/code>(.*?)<message>(.*?)<\/message>(.*?)<\/status>(.*?)<balance>(.*?)<sms>(.*?)<\/sms>(.*?)<\/balance>/si', $result, $result_matches)) {
            $status_code    = $result_matches[2];
            $status_message = $result_matches[4];
            $balance        = $result_matches[8];
        }

        if($status_code=='200') {
            return $balance;        
        } else {
            return $status_message;        
        }
    }

    function getReport($msgid){        

        $params  = $this->getParams();
        $balance_xml = '<?xml version="1.0" encoding="UTF-8" ?>
            <request>
                <authentication>
                    <username>'.$params->iletimerkezi_username.'</username>
                    <password>'.$params->iletimerkezi_password.'</password>
                </authentication>
                <order>
                    <id>'.$msgid.'</id>
                    <page></page>
                    <rowCount></rowCount>
                </order>
            </request>';

        $ch = curl_init('http://api.iletimerkezi.com/v1/get-report');
        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $balance_xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        if(preg_match('/<message>(.*?)<number>(.*?)<\/number>(.*?)<status>(.*?)<\/status>(.*?)<\/message>/si', $result, $result_matches)) {
            $sended_number  = $result_matches[2];
            $status_message = $result_matches[4];

            if($status_message=='111') {
                return 'success';
            } elseif($status_message=='110') {
                return '';
            } else {
                return 'error';
            }
        }

        return 'error';        
    }    

    function getHooks() {
        if ($handle = opendir(dirname(__FILE__).'/hooks')) {
            while (false !== ($entry = readdir($handle))) {
                if(substr($entry,strlen($entry)-4,strlen($entry)) == ".php"){
                    $file[] = require_once('hooks/'.$entry);
                }
            }
            closedir($handle);
        }
        return $file;
    }

    function saveToDb($msgid,$status,$errors = null,$logs = null){
        mysql_query("SET NAMES utf8");
        mysql_query("SET CHARACTER SET utf8");
        mysql_query("SET COLLATION_CONNECTION='utf8_general_ci'");
        
        $now    = date("Y-m-d H:i:s");
        $table  = "mod_iletimerkezi_messages";
        $values = array(
            "to"       => $this->getGsmnumber(),
            "text"     => $this->getMessage(),
            "msgid"    => $msgid,
            "status"   => $status,
            "errors"   => $errors,
            "logs"     => $logs,
            "user"     => $this->getUserid(),
            "datetime" => $now
        );
        insert_query($table, $values);

        $this->addLog("Mesaj veritabanına kaydedildi");
    }    

    function util_gsmnumber($number){

        $replacefrom = array('-', '(',')', '.', '+', ' ');
        $number      = str_replace($replacefrom, '', $number);
        if (strlen($number) < 10) {
            $this->addLog("Numara formatı hatalı: ".$number);
            $this->addError("Numara formatı hatalı: ".$number);
            return null;
        }        

        return $number;
    }

    public function addError($error){
        $this->errors[] = $error;
    }

    public function addLog($log){
        $this->logs[] = $log;
    }

    public function getErrors()
    {
        $res = '<pre><p><ul>';
        foreach($this->errors as $d){
            $res .= "<li>$d</li>";
        }
        $res .= '</ul></p></pre>';
        return $res;
    }

    public function getLogs()
    {
        $res = '<pre><p><strong>Sms gönderim detayı </strong><ul>';
        foreach($this->logs as $d){
            $res .= "<li>$d</li>";
        }
        $res .= '</ul></p></pre>';
        return $res;
    }

    function checkHooks($hooks = null) {

        if($hooks == null){
            $hooks = $this->getHooks();
        }

        $i=0;
        foreach($hooks as $hook){
            $sql      = "SELECT `id` FROM `mod_iletimerkezi_templates` WHERE `name` = '".$hook['function']."' AND `type` = '".$hook['type']."' LIMIT 1";
            $result   = mysql_query($sql);
            $num_rows = mysql_num_rows($result);
            if($num_rows == 0){
                if($hook['type']){
                    $values = array(
                        "name"        => $hook['function'],
                        "type"        => $hook['type'],
                        "template"    => $hook['defaultmessage'],
                        "variables"   => $hook['variables'],
                        "extra"       => $hook['extra'],
                        "description" => json_encode(@$hook['description']),
                        "active"      => 1
                    );
                    insert_query("mod_iletimerkezi_templates", $values);
                    $i++;
                }
            }
        }
        return $i;
    }

    function getTemplateDetails($template = null){
        /* musteriye yonetici mesajini gonderdigi icin hack yapildi normal hali yorum icindeki
        $where  = array("name" => array("sqltype" => "LIKE", "value" => $template));
        */
        $where  = array("name" => $template );
        $result = select_query("mod_iletimerkezi_templates", "*", $where);
        $data   = mysql_fetch_assoc($result);

        return $data;
    }

    function changeDateFormat($date = null){
        $settings   = $this->getSettings();
        $dateformat = $settings['dateformat'];
        if(!$dateformat){
            return $date;
        }

        $date  = explode("-",$date);
        $year  = $date[0];
        $month = $date[1];
        $day   = $date[2];

        $dateformat = str_replace(array("%d","%m","%y"),array($day,$month,$year),$dateformat);
        return $dateformat;
    }

    function getTotalReport(){
        $sql        = "SELECT COUNT(*) AS total FROM mod_iletimerkezi_messages";
        $result     = mysql_query($sql);
        $detail     = mysql_fetch_assoc($result);
        return $detail;
    }

    function getReports ($limit, $reportperpage){
        $sql        =" SELECT `m`.*,`user`.`firstname`,`user`.`lastname` FROM `mod_iletimerkezi_messages` as `m` JOIN `tblclients` as `user` ON `m`.`user` = `user`.`id` ORDER BY `m`.`datetime` DESC  LIMIT  $limit  ,  $reportperpage";
        //SELECT `m`.*,`user`.`firstname`,`user`.`lastname` FROM `mod_iletimerkezi_messages` as `m` JOIN `tblclients` as `user` ON `m`.`user` = `user`.`id` ORDER BY `m`.`datetime` DESC
        $result     = mysql_query($sql);
        $return_data= array();
        while ($row = mysql_fetch_assoc($result)) {
            $return_data[] = $row;
        }
        return $return_data;   
    }

}