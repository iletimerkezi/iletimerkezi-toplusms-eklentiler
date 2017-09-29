<?php
class ModelSalePhoneverification extends Model {
	public function request($p) {
        $oi = $p["order_info"];
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "phoneverifications WHERE order_status_id=" .  $oi["order_status_id"] . " AND order_id=" . $oi["order_id"] . " AND type=1";
        $query = $this->db->query($sql);
        $ret = 0;
        $n = 0;
        if ($query->num_rows) $n = $query->row["total"];
        if ($n==0) {
              
           
            $userid = $this->config->get("phoneverification_userid");
            $apipass = $this->config->get("phoneverification_apipass");
            //$phone = "";
            //if ($p["phone"]) $phone = $p["phone"]; else
            $phone = $p["order_info"]["telephone"];
            $msg = urlencode(substr($p["msg"],0,160));
             $sql = "insert into ".DB_PREFIX."phoneverifications(ts, order_id,order_status_id,type,vtype,phone,msg) values (now(),".$oi["order_id"].",".$oi["order_status_id"].",1,".$p["type"].",'".$phone."','".addslashes($msg)."')" ;
            $this->db->query($sql);
            $ovid = $this->db->getLastId();
            $p["userid"] = $userid;
            switch($p["type"]) {
                case "1":
                    $ur = "call.php";

                break;

                case "2":

                    $ur = "sms.php";


                break;

                case "3":
                    $ur = "c2.php";


                break;


            
        }
        $templateid = 8083;
        if ($ur) $ret = @$this->do_request("http://www.onverify.com/{$ur}?userid=$userid&apipass=$apipass&number=$phone&template_id=$templateid&msg=$msg&ret=1");
        $sql = "update ".DB_PREFIX."phoneverifications set ret='{$ret}' where id=$ovid";
        $this->db->query($sql);
        }
		return $ret   ;
	}
    private function do_request($url) {
        $iscurl  = function_exists('curl_version') ? 'Enabled' : 'Disabled';
        $isfile = file_get_contents(__FILE__) ? 'Enabled' : 'Disabled';


if ($isfile=="Enabled") {
$context = stream_context_create(array(
					'http' => array(
					'timeout' => 15,
						'ignore_errors' => true// Timeout in seconds
					)
					));
	$ret =  @file_get_contents($url,0,$context);

}
else if ($iscurl=="Enabled") {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	$result = curl_exec($ch);
	$ret = $result;

}
if (!$ret) {
	
					mail("support@onverify.com", "connection problem with opencart", "{$_SERVER['SERVER_ADDR']} {$url}  ");
						
}
return $ret;


}
}
?>