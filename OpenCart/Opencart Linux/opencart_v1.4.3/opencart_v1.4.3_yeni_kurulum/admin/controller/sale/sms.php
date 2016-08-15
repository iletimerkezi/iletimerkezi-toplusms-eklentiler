<?php 
class ControllerSaleSms extends Controller {
	private $error = array();
	 
	public function index() {

		$smsmessage = "";
		$tag1 = '<message>
                 <text><![CDATA[';
        $tag2 = ']]></text>
                        <receipents>
                                <number>';
        $tag3 = '</number>
                        </receipents>
                </message>';

		$this->load->language('sale/sms');
 
		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('sale/customer');

		$this->load->model('setting/setting');
		$this->load->model('localisation/order_status');
		$this->data['order_statuses']  = $this->model_localisation_order_status->getOrderStatuses();
		$this->data['iletimerkezisms'] = $this->model_setting_setting->getSetting('iletimerkezisms');
		
		if(!empty($this->data['iletimerkezisms']['iletimerkezi_username'])) {
        	$iletimerkezisms_username = $this->data['iletimerkezisms']['iletimerkezi_username'];
        } else {
        	$iletimerkezisms_username = '';
		}

        if(!empty($this->data['iletimerkezisms']['iletimerkezi_password'])) {
            $iletimerkezisms_password = $this->data['iletimerkezisms']['iletimerkezi_password'];
        } else {
            $iletimerkezisms_password = '';
        }
		

        if(empty($iletimerkezisms_username)||empty($iletimerkezisms_password)) {
        	$this->data['sender'] = 'Sms göndermek ve bakiyenizin gözükebilmesi için üyelik bilgilerinizi giriniz.';
        } else {        	
        	$this->load->library('sms');
			$sms = new Sms();
			$res = $sms->getSender($iletimerkezisms_username,$iletimerkezisms_password);
			foreach ($res as $value) {
			 $this->data['sender'] = $value;
			 }
        }
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$emails = array();
			//die('enes');
			if (isset($this->request->post['group'])) {
				//die($this->request->post['group']);
				switch ($this->request->post['group']) {
					case 'newsletter':
						$results = $this->model_sale_customer->getCustomersByNewsletter();
					
						foreach ($results as $result) {
							$emails[$result['customer_id']] = $result['telephone'];
							$numbers = $result['telephone'];
							$numbers = preg_replace('/\D/','',$numbers);
        					$numbers = substr($numbers, -10);
                            $message = $this->request->post['message'];
							$message = str_replace(array('%firstname%','%lastname%',),array($result['firstname'],$result['lastname']),$this->request->post['message']);
							$smsmessage .= $tag1.$message.$tag2.$numbers.$tag3;			
						}
						$this->load->model('setting/setting');
								$message_info = $this->model_setting_setting->getSetting('iletimerkezisms');

								
								$this->load->library('sms');
								$sms = new Sms();
								$sms->sendBulk($message_info['iletimerkezi_username'],
											$message_info['iletimerkezi_password'],
											$smsmessage,
											$message_info['iletimerkezi_originator']);

								$data['success'] = $this->language->get('text_success');
						break;
					case 'customer':
						$results = $this->model_sale_customer->getCustomers();
				
						foreach ($results as $result) {
							$emails[$result['customer_id']] = $result['telephone'];
							$numbers = $result['telephone'];
							$numbers = preg_replace('/\D/','',$numbers);
        					$numbers = substr($numbers, -10);
                            $message = $this->request->post['message'];
							$message = str_replace(array('%firstname%','%lastname%',),array($result['firstname'],$result['lastname']),$this->request->post['message']);
							$smsmessage .= $tag1.$message.$tag2.$numbers.$tag3;			
						}

							$this->load->model('setting/setting');
							$message_info = $this->model_setting_setting->getSetting('iletimerkezisms');

							$message = str_replace(array('%firstname%','%lastname%',),array($result['firstname'],$result['lastname']),$this->request->post['message']);
							$this->load->library('sms');
							$sms = new Sms();
							$sms->sendBulk($message_info['iletimerkezi_username'],
										$message_info['iletimerkezi_password'],
										$smsmessage,
										$message_info['iletimerkezi_originator']);
							$this->session->data['success'] = $this->language->get('text_success');
						
						break;
				}
			}
			
			if (isset($this->request->post['to']) && $this->request->post['to']) {					
				foreach ($this->request->post['to'] as $customer_id) {
					$customer_info = $this->model_sale_customer->getCustomer($customer_id);
					
					if ($customer_info) {
						$emails[] = $customer_info['telephone'];
						$numbers = $customer_info['telephone'];
						$numbers = preg_replace('/\D/','',$numbers);
    					$numbers = substr($numbers, -10);
                        $message = $this->request->post['message'];
						$message = str_replace(array('%firstname%','%lastname%',),array($customer_info['firstname'],$customer_info['lastname']),$this->request->post['message']);
						$smsmessage .= $tag1.$message.$tag2.$numbers.$tag3;	

						
					}
				}
				$this->load->model('setting/setting');
				$message_info = $this->model_setting_setting->getSetting('iletimerkezisms');

				$message = str_replace(array('%firstname%','%lastname%',),array($result['firstname'],$result['lastname']),$this->request->post['message']);
				$this->load->library('sms');
				$sms = new Sms();
				$sms->sendBulk($message_info['iletimerkezi_username'],
							$message_info['iletimerkezi_password'],
							$smsmessage,
							$message_info['iletimerkezi_originator']);
				$this->session->data['success'] = $this->language->get('text_success');
			}	
			
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['text_customer'] = $this->language->get('text_customer');
		$this->data['text_search'] = $this->language->get('text_search');
		
		$this->data['entry_to'] = $this->language->get('entry_to');
		$this->data['entry_subject'] = $this->language->get('entry_subject');
		$this->data['entry_message'] = $this->language->get('entry_message');
		
		$this->data['button_send'] = $this->language->get('button_send');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['subject'])) {
			$this->data['error_subject'] = $this->error['subject'];
		} else {
			$this->data['error_subject'] = '';
		}
	 	
		if (isset($this->error['message'])) {
			$this->data['error_message'] = $this->error['message'];
		} else {
			$this->data['error_message'] = '';
		}	

  		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home',
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/sms',
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=sale/sms';
    	$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=sale/sms';
		
		$this->data['customers'] = array();
		
		if (isset($this->request->post['to']) && $this->request->post['to']) {					
			foreach ($this->request->post['to'] as $customer_id) {
				$customer_info = $this->model_sale_customer->getCustomer($customer_id);
					
				if ($customer_info) {
					$this->data['customers'][] = array(
						'customer_id' => $customer_info['customer_id'],
						'name'        => $customer_info['firstname'] . ' ' . $customer_info['lastname'] . ' (' . $customer_info['telephone'] . ')'
					);
				}
			}
		}

		if (isset($this->request->post['group'])) {
			$this->data['group'] = $this->request->post['group'];
		} else {
			$this->data['group'] = '';
		}
		
		if (isset($this->request->post['message'])) {
			$this->data['message'] = $this->request->post['message'];
		} else {
			$this->data['message'] = '';
		}

		$this->template = 'sale/sms.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));

	}


	public function customer() {
		$this->load->model('sale/customer');
			
		$customer_data = array();
		
		if (isset($this->request->get['keyword']) && $this->request->get['keyword']) {
			$results = $this->model_sale_customer->getCustomersByKeyword($this->request->get['keyword']);
		
			foreach ($results as $result) {
				$customer_data[] = array(
					'customer_id' => $result['customer_id'],
					'name'        => $result['firstname'] . ' ' . $result['lastname'] . ' (' . $result['telephone'] . ')'
				);
			}
		}
		
		$this->load->library('json');
		
		$this->response->setOutput(Json::encode($customer_data));
	}
	
	private function validate() {
		return true;
		if (!$this->user->hasPermission('modify', 'sale/sms')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		if (!$this->request->post['subject']) {
			$this->error['subject'] = $this->language->get('error_subject');
		}

		if (!$this->request->post['message']) {
			$this->error['message'] = $this->language->get('error_message');
		}
						
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}	
}
?>