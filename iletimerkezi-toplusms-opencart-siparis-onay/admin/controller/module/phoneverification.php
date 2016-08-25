<?php

class ControllerModulePhoneVerification extends Controller {
	private $error = array(); 
    private $data2 = array();
    private $ocversion;
    function __construct($p) {
       parent::__construct($p);
        $this->ocversion = substr(VERSION,0,1);
    }
    
    public function install() {               
    }

    public function uninstall() {
    }

	public function index() {   
        
        if ($this->ocversion=="1") {
        	$this->data2 = $this->data;
        }        
		
		$this->load->language('module/phoneverification');
		$this->load->model('setting/setting');
		$this->load->model('sale/customer_group');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->editSetting('phoneverification', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
            
            
             if ($this->ocversion=="1") 
                $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
            else
					$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
            
		}
		$this->document->setTitle($this->language->get('heading_title'));

		$this->data2['text_enabled'] = $this->language->get('text_enabled');
		$this->data2['text_disabled'] = $this->language->get('text_disabled');
		$this->data2['text_content_top'] = $this->language->get('text_content_top');
		$this->data2['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data2['text_column_left'] = $this->language->get('text_column_left');
		$this->data2['text_column_right'] = $this->language->get('text_column_right');
		
		$this->data2['entry_layout'] = $this->language->get('entry_layout');
		$this->data2['entry_position'] = $this->language->get('entry_position');
		$this->data2['entry_status'] = $this->language->get('entry_status');
		$this->data2['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data2['button_save'] = $this->language->get('button_save');
		$this->data2['button_cancel'] = $this->language->get('button_cancel');
		$this->data2['button_add_module'] = $this->language->get('button_add_module');
		$this->data2['button_remove'] = $this->language->get('button_remove');
		if (isset($this->error['warning'])) {
			$this->data2['error_warning'] = $this->error['warning'];
		} else {
			$this->data2['error_warning'] = '';
		}
		$this->data2['breadcrumbs'] = array();

   		$this->data2['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);		
		
		$this->fillin('phoneverification_userid');		
		$this->fillin('phoneverification_apipass');
		$this->fillin('phoneverification_sender_id');		
		$this->fillin('phoneverification_smstemplate',"Şifreniz %s");
		        
		$this->template = 'module/phoneverification.tpl';
         // if ($this->ocversion=="2") $this->template = 'module/phoneverification2.tpl';
       
		$this->children = array(
			'common/header',
			'common/footer',
		);
        if ($this->ocversion=="2") {
		    $this->data2['header'] = $this->load->controller('common/header');
            $this->data2['column_left'] = $this->load->controller('common/column_left');
            $this->data2['footer'] = $this->load->controller('common/footer');
        }
		$this->data2['action'] = $this->url->link('module/phoneverification', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data2['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        
        if ($this->ocversion=="1")  {
            if ($this->ocversion=="1") $this->data = $this->data2;
        	$this->response->setOutput($this->render());
        } else {
        	$this->response->setOutput($this->load->view($this->template, $this->data2));
        }

	}
	private function fillin($var,$default=0) {
        
	//if ($var=="phoneverification_verifiedgroup2") {print_r($this->request->post[$var]); exit;} 
	if (isset($this->request->post[$var])) {
			$this->data2[$var] = $this->request->post[$var];
		} else {
			$this->data2[$var] = $this->config->get($var);
			if ($default)
			if (!$this->data2[$var]) $this->data2[$var] = $default;
		}	
	
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/phoneverification')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>