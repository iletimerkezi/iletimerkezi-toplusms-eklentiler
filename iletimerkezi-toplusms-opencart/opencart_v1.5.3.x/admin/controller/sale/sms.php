<?php 
class ControllerSaleSms extends Controller {
	private $error = array();
	 
	public function index() {
		$this->language->load('sale/sms');
 
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['text_customer_all'] = $this->language->get('text_customer_all');	
		$this->data['text_customer'] = $this->language->get('text_customer');	
		$this->data['text_customer_group'] = $this->language->get('text_customer_group');
		$this->data['text_affiliate_all'] = $this->language->get('text_affiliate_all');	
		$this->data['text_affiliate'] = $this->language->get('text_affiliate');	
		$this->data['text_product'] = $this->language->get('text_product');	

		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_to'] = $this->language->get('entry_to');
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_customer'] = $this->language->get('entry_customer');
		$this->data['entry_affiliate'] = $this->language->get('entry_affiliate');
		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_subject'] = $this->language->get('entry_subject');
		$this->data['entry_message'] = $this->language->get('entry_message');
		
		$this->data['button_send'] = $this->language->get('button_send');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->load->model('setting/setting');
		
		$this->data['token'] = $this->session->data['token'];

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/sms', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
    	$this->data['cancel'] = $this->url->link('sale/sms', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('setting/store');
		
		$this->data['stores'] = $this->model_setting_store->getStores();
		
		$this->load->model('sale/customer_group');
				
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups(0);
				
		$this->template = 'sale/sms.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);


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

        	$this->response->setOutput($this->render());
	}
	
	public function send() {
		$this->language->load('sale/sms');

		$smsmessage = "";
		$tag1 = '<message>
                 <text><![CDATA[';
        $tag2 = ']]></text>
                        <receipents>
                                <number>';
        $tag3 = '</number>
                        </receipents>
                </message>';
		
		$json = array();
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'sale/sms')) {
				$json['error']['warning'] = $this->language->get('error_permission');
			}
					
	
			if (!$this->request->post['message']) {
				$json['error']['message'] = $this->language->get('error_message');
			}
			
			if (!$json) {
				$this->load->model('setting/store');
			
				$store_name = $this->config->get('config_name');
	
				$this->load->model('sale/customer');
				
				$this->load->model('sale/customer_group');
				
				$this->load->model('sale/affiliate');
	
				$this->load->model('sale/order');
	
				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
				} else {
					$page = 1;
				}
								
				$email_total = 0;
							
				$numbers = array();
				$customer_data = array();
				
				switch ($this->request->post['to']) {
					case 'newsletter':
						
						$email_total = $this->model_sale_customer->getTotalCustomers($customer_data);
							
						$results = $this->model_sale_customer->getCustomers($customer_data);
					
						foreach ($results as $result) {
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
					case 'customer_all':
		
						$email_total = $this->model_sale_customer->getTotalCustomers($customer_data);
										
						$results = $this->model_sale_customer->getCustomers($customer_data);
						
						foreach ($results as $result) {
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
					case 'customer_group':
						
						$email_total = $this->model_sale_customer->getTotalCustomers($customer_data);
										
						$results = $this->model_sale_customer->getCustomers($customer_data);
				
						foreach ($results as $result) {
							$numbers[$result['customer_id']] = $result['telephone'];
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
						if (!empty($this->request->post['customer'])) {					
							foreach ($this->request->post['customer'] as $customer_id) {
								$customer_info = $this->model_sale_customer->getCustomer($customer_id);
								
								if ($customer_info) {
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
							$this->load->library('sms');
							$sms = new Sms();
							$sms->sendBulk($message_info['iletimerkezi_username'],
										$message_info['iletimerkezi_password'],
										$smsmessage,
										$message_info['iletimerkezi_originator']);

							$data['success'] = $this->language->get('text_success');
						}
						break;	
					case 'affiliate_all':
						$affiliate_data = array();
						
						$email_total = $this->model_sale_affiliate->getTotalAffiliates($affiliate_data);		
						
						$results = $this->model_sale_affiliate->getAffiliates($affiliate_data);
				
						foreach ($results as $result) {
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
					case 'affiliate':
						if (!empty($this->request->post['affiliate'])) {					
							foreach ($this->request->post['affiliate'] as $affiliate_id) {
								$affiliate_info = $this->model_sale_affiliate->getAffiliate($affiliate_id);
								
								if ($affiliate_info) {
									$numbers = $affiliate_info['telephone'];
									$numbers = preg_replace('/\D/','',$numbers);
		        					$numbers = substr($numbers, -10);
		                            $message = $this->request->post['message'];
									$message = str_replace(array('%firstname%','%lastname%',),array($affiliate_info['firstname'],$affiliate_info['lastname']),$this->request->post['message']);
									$smsmessage .= $tag1.$message.$tag2.$numbers.$tag3;		

								}
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
						}
						break;											
					case 'product':
						if (isset($this->request->post['product'])) {
							$email_total = $this->model_sale_order->getTotalEmailsByProductsOrdered($this->request->post['product']);	
							
							$results = $this->model_sale_order->getTelephonesByProductsOrdered($this->request->post['product'], ($page - 1) * 10, 10);
													
							foreach ($results as $result) {
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
						}
						break;												
				}
			$json['success'] = "Mesajınız başarılı bir şekilde gönderildi.";


			}
		}
		
		$this->response->setOutput(json_encode($json));	
	}
}
?>