<?php
class ControllerModulePhoneVerification extends Controller {
     private $ocversion;
    function __construct($p) {
       parent::__construct($p);
        $this->ocversion = substr(VERSION,0,1);
    }

    public function getrepl($ret=0) {
    	$data['button_continue'] = $this->language->get('button_continue');
        if ($this->ocversion==1) {
            $repl=$this->getChild('payment/' . $this->session->data['payment_method']['code']);
        } else {
        	$repl = '<a id="button-payment-method" class="button active">'.$data['button_continue'].'</a>';
        }

        if ($ret) {
        	return $repl;
        } else {
        	echo $repl;
        }

    }

    public function verify(){

        return $this->index();
    }


	public function index($repl="") {

        if ($this->ocversion=="1")  {
           $temp = "/template/module/phoneverification2.tpl";
        }

        if ($this->data) {
        	$data = $this->data;
        }  else {
       		$temp = "/default/template/module/phoneverification2.tpl";
        }

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . $temp)) {
			$this->template = $this->config->get('config_template') . $temp;
		} else {
			$this->template = $temp;
		}

		$this->language->load('module/phoneverification');
		$ll = array("heading_title", "text_phone","text_start","text_provide_valid_number","text_provide_valid_mobile_number","text_invalid_pin","text_verify","text_max_retries_exceeded","text_please_wait","text_please_wait_next","text_resend","text_explain1","text_explain_select_type","text_explain_phone_call","text_explain_sms","text_explain_started","text_explain_phone_call2","text_explain_sms2","text_explain_same_number","text_connection_problem","text_explain_unique_number");

		for ($i=0;$i<sizeof($ll);$i++) {
			$data[$ll[$i]] = $this->language->get($ll[$i]);
		}

        if ($this->ocversion=="1") {

            $this->data = $data;
            $this->render();

        } else {
        	return $this->load->view($this->template, $data);
        }

	}


    public function success() {
    	return true;
    }

	public function start() {

	   $this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$phone = $this->request->post['phone'];
		$phone = preg_replace('/\D/','',$phone);
		$phone = substr($phone, -10);

		if(strlen($phone)!=10) {
			die('5');
		}

		$this->session->data['phone'] = $phone;
		$pin = "";
		for ($i=0;$i<5;$i++) {
			$pin .= sprintf("%s", rand(1,9));
		}
		$this->session->data['pin'] = $pin;
		$msg = $this->config->get("phoneverification_smstemplate");
		$msg = sprintf($msg,$pin);

		$userid = $this->config->get("phoneverification_userid");
		$apipass = $this->config->get("phoneverification_apipass");
		$sender_id = $this->config->get("phoneverification_sender_id");

		$cr = $this->do_request($userid,$apipass,$msg,$phone,$sender_id);

		die('1');


	}

	public function do_request($userid,$apipass,$msg,$phone,$sender_id) {
		$xml = "
		<request>
        	<authentication>
                <username>$userid</username>
                <password>$apipass</password>
        	</authentication>
        	<order>
                <sender>$sender_id</sender>
                <sendDateTime></sendDateTime>
                <message>
                        <text><![CDATA[".$msg."]]></text>
                        <receipents>
                                <number>$phone</number>
                        </receipents>
                </message>
        	</order>
		</request>
		";

		$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL,"https://api.iletimerkezi.com/v1/send-sms");
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,1);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    	$res = curl_exec($ch);
		return $res;
	}

	public function confirm() {

		$pin = $this->request->post['pin'];
		if ($pin && $this->session->data['pin']==$pin) {

			$cid = $this->customer->getId();
			$phone = $this->session->data['phone'];
			if ($cid && $phone) {
				$this->db->query("UPDATE " . DB_PREFIX . "customer set telephone=$phone where customer_id=$cid");
			}
			echo "1";
		} else {
			echo "2";
		}

	}
}
?>
