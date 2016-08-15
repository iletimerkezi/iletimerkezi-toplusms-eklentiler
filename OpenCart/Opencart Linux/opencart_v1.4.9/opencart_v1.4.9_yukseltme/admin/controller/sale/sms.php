<?php 
class ControllerSaleSms extends Controller {
	private $error = array();
	 
	public function index() {
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
							$this->load->model('setting/setting');
							$message_info = $this->model_setting_setting->getSetting('iletimerkezisms');

							$message = str_replace(array('%firstname%','%lastname%',),array($result['firstname'],$result['lastname']),$this->request->post['message']);
							$this->load->library('sms');
							$sms = new Sms();
							$sms->send($message_info['iletimerkezi_username'],
										$message_info['iletimerkezi_password'],
										$message,
										$result['telephone'],
										$message_info['iletimerkezi_originator']);
							$this->session->data['success'] = $this->language->get('text_success');

						}
						break;
					case 'customer':
						$results = $this->model_sale_customer->getCustomers();
				
						foreach ($results as $result) {
							//die('hasan');
							$emails[$result['customer_id']] = $result['telephone'];
							$this->load->model('setting/setting');
							$message_info = $this->model_setting_setting->getSetting('iletimerkezisms');

							$message = str_replace(array('%firstname%','%lastname%',),array($result['firstname'],$result['lastname']),$this->request->post['message']);
							$this->load->library('sms');
							$sms = new Sms();
							$sms->send($message_info['iletimerkezi_username'],
										$message_info['iletimerkezi_password'],
										$message,
										$result['telephone'],
										$message_info['iletimerkezi_originator']);
							$this->session->data['success'] = $this->language->get('text_success');

						}						
						break;
				}
			}
			
			if (isset($this->request->post['to']) && $this->request->post['to']) {					
				foreach ($this->request->post['to'] as $customer_id) {
					$customer_info = $this->model_sale_customer->getCustomer($customer_id);
					
					if ($customer_info) {
						$emails[] = $customer_info['telephone'];
						$this->load->model('setting/setting');
						$message_info = $this->model_setting_setting->getSetting('iletimerkezisms');

						$message = $this->request->post['message'];
						$message = str_replace(array('%firstname%','%lastname%',),array($customer_info['firstname'],$customer_info['lastname']),$this->request->post['message']);
						$this->load->library('sms');
						$sms = new Sms();
						$sms->send($message_info['iletimerkezi_username'],
									$message_info['iletimerkezi_password'],
									$message,
									$customer_info['telephone'],
									$message_info['iletimerkezi_originator']);
						$this->session->data['success'] = $this->language->get('text_success');

						
					}
				}
			}
			
			// All customers by product
			if (isset($this->request->post['product'])) {
				foreach ($this->request->post['product'] as $product_id) {
					$results = $this->model_sale_customer->getTelephoneByProduct($product_id);

					foreach ($results as $result) {
						$emails[] = $result['telephone'];
						$this->load->model('setting/setting');
						$message_info = $this->model_setting_setting->getSetting('iletimerkezisms');

						$message = $this->request->post['message'];
						$this->load->library('sms');
						$sms = new Sms();
						$sms->send($message_info['iletimerkezi_username'],
									$message_info['iletimerkezi_password'],
									$message,
									$customer_info['telephone'],
									$message_info['iletimerkezi_originator']);
						$this->session->data['success'] = $this->language->get('text_success');
					}
				}
			}	
			
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['text_customer'] = $this->language->get('text_customer');
		$this->data['text_search'] = $this->language->get('text_search');
		$this->data['text_default'] = $this->language->get('text_default');
		
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_to'] = $this->language->get('entry_to');
		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_subject'] = $this->language->get('entry_subject');
		$this->data['entry_message'] = $this->language->get('entry_message');
		
		$this->data['button_send'] = $this->language->get('button_send');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		
		$this->data['token'] = $this->session->data['token'];
		
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
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=sale/sms&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=sale/sms&token=' . $this->session->data['token'];
    	$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=sale/sms&token=' . $this->session->data['token'];

		if (isset($this->request->post['store_id'])) {
			$this->data['store_id'] = $this->request->post['store_id'];
		} else {
			$this->data['store_id'] = '';
		}

		$this->load->model('setting/store');
		
		$this->data['stores'] = $this->model_setting_store->getStores();
		
		$this->data['customers'] = array();
		
		if (isset($this->request->post['to']) && $this->request->post['to']) {					
			foreach ($this->request->post['to'] as $customer_id) {
				$customer_info = $this->model_sale_customer->getCustomer($customer_id);
					
				if ($customer_info) {
					$this->data['customers'][] = array(
						'customer_id' => $customer_info['customer_id'],
						'name'        => $customer_info['firstname'] . ' ' . $customer_info['lastname'] . ' (' . $customer_info['email'] . ')'
					);
				}
			}
		}

		$this->load->model('catalog/product');
		
		$this->data['products'] = $this->model_catalog_product->getProducts();
		
		if (isset($this->request->post['product'])) {
			$this->data['product'] = $this->request->post['product'];
		} else {
			$this->data['product'] = '';
		}
		
		if (isset($this->request->post['group'])) {
			$this->data['group'] = $this->request->post['group'];
		} else {
			$this->data['group'] = '';
		}
		
		if (isset($this->request->post['subject'])) {
			$this->data['subject'] = $this->request->post['subject'];
		} else {
			$this->data['subject'] = '';
		}
		
		if (isset($this->request->post['message'])) {
			$this->data['message'] = $this->request->post['message'];
		} else {
			$this->data['message'] = '';
		}
		
		$this->load->model('catalog/category');
				
		$this->data['categories'] = $this->model_catalog_category->getCategories(0);

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

	public function category() {
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = 0;
		}
		
		$product_data = array();
		
		$results = $this->model_catalog_product->getProductsByCategoryId($category_id);
		
		foreach ($results as $result) {
			$product_data[] = array(
				'product_id' => $result['product_id'],
				'name'       => $result['name'],
				'model'      => $result['model']
			);
		}
		
		$this->load->library('json');
		
		$this->response->setOutput(Json::encode($product_data));
	}
}
?>