<?php
class ControllerModulePhoneVerification extends Controller {
     private $ocversion;
    function __construct($p) {
       parent::__construct($p);
        $this->ocversion = substr(VERSION,0,1);
    }

    public function getrepl($ret=0) {

        if ($this->ocversion==1) {
            $repl=$this->getChild('payment/' . $this->session->data['payment_method']['code']);
        } else {
        	$repl = $this->load->controller('payment/' . $this->session->data['payment_method']['code']);
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
       		$temp = "/template/module/phoneverification2.tpl";
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

		$cr = $this->do_request("http://api.iletimerkezi.com/v1/send-sms/get/?username=".urlencode($userid)."&password=".urlencode($apipass)."&text=".urlencode($msg)."&receipents=".$phone."&sender=".urlencode($sender_id));

		die('1');


	}

	private function do_request($url) {

		$res = file_get_contents($url);
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
