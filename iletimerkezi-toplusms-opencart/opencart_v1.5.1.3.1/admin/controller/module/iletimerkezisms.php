<?php
class ControllerModuleIletimerkezisms extends Controller {
	private $error = array();

	public function index() {
		$this->language->load('module/iletimerkezisms');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('iletimerkezisms', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect(HTTPS_SERVER.'index.php?route=extension/module&token='.$this->session->data['token']);
		}

		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

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
        	$this->data['balance'] = 'Sms göndermek ve bakiyenizin gözükebilmesi için üyelik bilgilerinizi giriniz.';
        } else {
        	
        	$this->load->library('sms');
			$sms = new Sms();
			$res = $sms->getBalance($iletimerkezisms_username,$iletimerkezisms_password);		
        	$this->data['balance'] = $res.' SMS <a target="_blank" href="https://www.iletimerkezi.com/index.php?function=default&obj1=signinViaGet&gsm='.$iletimerkezisms_username.'&password='.$iletimerkezisms_password.'">SMS Satın Al!</a>';

        	$setdomain = $sms->setDomain($iletimerkezisms_username,$iletimerkezisms_password);
        }



		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');

		$this->data['entry_banner'] = $this->language->get('entry_banner');
		$this->data['entry_dimension'] = $this->language->get('entry_dimension');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['dimension'])) {
			$this->data['error_dimension'] = $this->error['dimension'];
		} else {
			$this->data['error_dimension'] = array();
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => HTTPS_SERVER.'index.php?route=common/home&token=' . $this->session->data['token'],
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => HTTPS_SERVER.'index.php?route=extension/module&token=' . $this->session->data['token'],
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => HTTPS_SERVER.'index.php?route=module/iletimerkezisms&token=' . $this->session->data['token'],
      		'separator' => ' :: '
   		);

		$this->data['action'] = HTTPS_SERVER.'index.php?route=module/iletimerkezisms&token=' . $this->session->data['token'];

		$this->data['cancel'] = HTTPS_SERVER.'index.php?route=extension/module&token=' . $this->session->data['token'];

		$this->data['modules'] = array();

		if (isset($this->request->post['slideshow_module'])) {
			$this->data['modules'] = $this->request->post['slideshow_module'];
		} elseif ($this->config->get('slideshow_module')) {
			$this->data['modules'] = $this->config->get('slideshow_module');
		}

		// $this->load->model('design/layout');

		// $this->data['layouts'] = $this->model_design_layout->getLayouts();

		// $this->load->model('design/banner');

		// $this->data['banners'] = $this->model_design_banner->getBanners();

		$this->template = 'module/iletimerkezisms.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		// $this->response->setOutput($this->render());
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/iletimerkezisms')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['slideshow_module'])) {
			foreach ($this->request->post['slideshow_module'] as $key => $value) {
				if (!$value['width'] || !$value['height']) {
					$this->error['dimension'][$key] = $this->language->get('error_dimension');
				}
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>