<?php
class ControllerMarketingSms extends Controller {
	private $error = array();

	public function index() {
		$this->language->load('marketing/sms');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_default'] = $this->language->get('text_default');
		$data['text_newsletter'] = $this->language->get('text_newsletter');
		$data['text_customer_all'] = $this->language->get('text_customer_all');
		$data['text_customer'] = $this->language->get('text_customer');
		$data['text_customer_group'] = $this->language->get('text_customer_group');
		$data['text_affiliate_all'] = $this->language->get('text_affiliate_all');
		$data['text_affiliate'] = $this->language->get('text_affiliate');
		$data['text_product'] = $this->language->get('text_product');
		$data['help_customer'] = $this->language->get('help_customer');
		$data['help_affiliate'] = $this->language->get('help_affiliate');
		$data['help_product'] = $this->language->get('help_product');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_to'] = $this->language->get('entry_to');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_affiliate'] = $this->language->get('entry_affiliate');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_message'] = $this->language->get('entry_message');

		$data['button_send'] = $this->language->get('button_send');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$this->load->model('setting/setting');

		$data['user_token'] = $this->session->data['user_token'];

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('marketing/sms', 'user_token=' . $this->session->data['user_token'], 'SSL'),
   		);

    	$data['cancel'] = $this->url->link('marketing/sms', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups(0);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');


		$this->load->model('localisation/order_status');
		$data['order_statuses']  = $this->model_localisation_order_status->getOrderStatuses();
		$data['iletimerkezisms'] = $this->model_setting_setting->getSetting('iletimerkezisms');

		if(!empty($data['iletimerkezisms']['iletimerkezisms_username'])) {
        	$iletimerkezisms_username = $data['iletimerkezisms']['iletimerkezisms_username'];
        } else {
        	$iletimerkezisms_username = '';
		}

        if(!empty($data['iletimerkezisms']['iletimerkezisms_password'])) {
            $iletimerkezisms_password = $data['iletimerkezisms']['iletimerkezisms_password'];
        } else {
            $iletimerkezisms_password = '';
        }

        	$this->response->setOutput($this->load->view('marketing/sms', $data));
	}

	public function send() {
		$this->language->load('marketing/sms');

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
			if (!$this->user->hasPermission('modify', 'marketing/sms')) {
				$json['error']['warning'] = $this->language->get('error_permission');
			}


			if (!$this->request->post['message']) {
				$json['error']['message'] = $this->language->get('error_message');
			}

			if (!$json) {
				$this->load->model('setting/store');

					$store_name = $this->config->get('config_name');

				$this->load->model('customer/customer');

				$this->load->model('customer/customer_group');

				$this->load->model('sale/order');

				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
				} else {
					$page = 1;
				}

				$email_total = 0;

				$numbers = array();

				switch ($this->request->post['to']) {
					case 'newsletter':
						$customer_data = array();

						$email_total = $this->model_customer_customer->getTotalCustomers($customer_data);

						$results = $this->model_customer_customer->getCustomers($customer_data);

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


								$sms = new Sms();
								$sms->sendBulk($message_info['iletimerkezisms_username'],
											$message_info['iletimerkezisms_password'],
											$smsmessage,
											$message_info['iletimerkezisms_originator']);

								$data['success'] = $this->language->get('text_success');
						break;
					case 'customer_all':
						$customer_data = array();

						$email_total = $this->model_customer_customer->getTotalCustomers($customer_data);

						$results = $this->model_customer_customer->getCustomers($customer_data);

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


								$sms = new Sms();
								$sms->sendBulk($message_info['iletimerkezisms_username'],
											$message_info['iletimerkezisms_password'],
											$smsmessage,
											$message_info['iletimerkezisms_originator']);

								$data['success'] = $this->language->get('text_success');

						break;
					case 'customer_group':
						$customer_data = array();

						$email_total = $this->model_customer_customer->getTotalCustomers($customer_data);

						$results = $this->model_customer_customer->getCustomers($customer_data);
						if (!empty($this->request->post['customer_group_id'])) {
							$customer_group_id = $this->request->post['customer_group_id'];
							foreach ($results as $result) {
								$numbers[$result['customer_id']] = $result['telephone'];
								$numbers = $result['telephone'];
								$numbers = preg_replace('/\D/','',$numbers);
	        					$numbers = substr($numbers, -10);
	                            $message = $this->request->post['message'];
	                            if($customer_group_id == $result['customer_group_id']){
									$message = str_replace(array('%firstname%','%lastname%',),array($result['firstname'],$result['lastname']),$this->request->post['message']);
									$smsmessage .= $tag1.$message.$tag2.$numbers.$tag3;
								}
							}
						}
						$this->load->model('setting/setting');
								$message_info = $this->model_setting_setting->getSetting('iletimerkezisms');



								$sms = new Sms();
								$sms->sendBulk($message_info['iletimerkezisms_username'],
											$message_info['iletimerkezisms_password'],
											$smsmessage,
											$message_info['iletimerkezisms_originator']);

								$data['success'] = $this->language->get('text_success');

						break;
					case 'customer':
						if (!empty($this->request->post['customer'])) {
							foreach ($this->request->post['customer'] as $customer_id) {
								$customer_info = $this->model_customer_customer->getCustomer($customer_id);

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

							$sms = new Sms();
							$sms->sendBulk($message_info['iletimerkezisms_username'],
										$message_info['iletimerkezisms_password'],
										$smsmessage,
										$message_info['iletimerkezisms_originator']);

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

						$sms = new Sms();
						$sms->sendBulk($message_info['iletimerkezisms_username'],
									$message_info['iletimerkezisms_password'],
									$smsmessage,
									$message_info['iletimerkezisms_originator']);

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

							$sms = new Sms();
							$sms->sendBulk($message_info['iletimerkezisms_username'],
										$message_info['iletimerkezisms_password'],
										$smsmessage,
										$message_info['iletimerkezisms_originator']);

							$data['success'] = $this->language->get('text_success');
						}
						break;
					case 'product':
						if (isset($this->request->post['product'])) {

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

							$sms = new Sms();
							$sms->sendBulk($message_info['iletimerkezisms_username'],
										$message_info['iletimerkezisms_password'],
										$smsmessage,
										$message_info['iletimerkezisms_originator']);

							$data['success'] = $this->language->get('text_success');
						}
					break;
				}
			$json['success'] = "Mesajınız başarılı bir şekilde gönderildi.";

			}
		}

		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'marketing/sms')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
?>