<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class IletimerkeziSms extends Module
{
	public function __construct()
	{
		$this->name = 'iletimerkezisms';
		$this->tab = 'administration';
		$this->version = 0.1;
		$this->author = 'www.iletimerkezisms.com';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Iletimerkezi Sms');
		$this->description = $this->l('Müşterilerinize, siparişinin kargo durumlarını sms ile bildirin.');
	}

	public function install()
	{
		//return
			if(
				parent::install() == false OR
				!$this->registerHook('newOrder') OR
				!$this->registerHook('postUpdateOrderStatus') OR
				!$this->registerHook('createAccount')
			){
			return false;
			}
			$this->installSmsReportTable();
			return true;
	}

	public function uninstall()
	{
  		return (
  			parent::uninstall() && 
  			Configuration::deleteByName('iletimerkezisms') && 
  			Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'iletimerkezisms')
  			);
	}

	/**
	* @desc it creative sms report table
	*/
	private function installSmsReportTable() {
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'iletimerkezisms`');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'iletimerkezisms` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `report_id` int(11) NOT NULL,
		  `number` varchar(55) NOT NULL,
		  `message` text NOT NULL,
		  `status` tinyint(1) NOT NULL,
		  `date_send` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `report_id` (`report_id`)
		) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;');
	}

	public function getContent()
	{    
		global $smarty;
	    $output = null;

	    /* for send multiple sms */
	    if(isset($_POST['bulk']) && $_POST['bulk'] == '1' ) {
	    	//die(var_export($_POST));
	    	$this->sendMultipleSms($_POST['iletimerkezi_customer_group'], $_POST['iletimerkezi_message']);
	    	//die("islem tamam");
	    } else {

	    if (isset($_POST['submitModule']))
		{ 
			Configuration::updateValue('iletimerkezi_username', $_POST['iletimerkezi_username']);
			Configuration::updateValue('iletimerkezi_password', $_POST['iletimerkezi_password']);
			Configuration::updateValue('iletimerkezi_sender', $_POST['iletimerkezi_sender']);
			Configuration::updateValue('iletimerkezi_admin_gsm', $_POST['iletimerkezi_admin_gsm']);
			Configuration::updateValue('iletimerkezi_new_member_text', $_POST['iletimerkezi_new_member_text']);
			Configuration::updateValue('iletimerkezi_new_order_text', $_POST['iletimerkezi_new_order_text']);

			unset($_POST['iletimerkezi_username']);
			unset($_POST['iletimerkezi_password']);
			unset($_POST['iletimerkezi_sender']);
			unset($_POST['iletimerkezi_admin_gsm']);
			unset($_POST['iletimerkezi_new_member_text']);
			unset($_POST['iletimerkezi_new_order_text']);
			unset($_POST['submitModule']);
			unset($_POST['tab']);

			foreach ($_POST as $key => $value) {
				Configuration::updateValue($key, $value);
			}
			// die(var_export($_POST,1));
		}

	    if (Tools::isSubmit('submit'.$this->name))
	    {
	        $my_module_name = strval(Tools::getValue('iletimerkezisms'));
	        if (!$my_module_name  || empty($my_module_name) || !Validate::isGenericName($my_module_name))
	            $output .= $this->displayError( $this->l('Invalid Configuration value') );
	        else
	        {
	            Configuration::updateValue('iletimerkezisms', $my_module_name);
	            $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }

	    }

	    $order_state = new OrderState();

	    // die('<pre>'.var_export($order_state,1).'</pre>');
	    $db_id_order = Tools::getValue('id_order_state');
	    if(empty($db_id_order))
	    	$id_order_state = 1;
	    else
	    	$id_order_state = $db_id_order;

	    $order_status = $order_state->getOrderStates($id_order_state);
	    // die('<pre>'.var_export($order_status,1).'</pre>');

	    $html = '';
	    $setting_form = "";

		foreach ($order_status as $key => $value) {

			$html .= '
	          <tr>
	          <td style="vertical-align: top;" >'.$this->l('Siparişin durumu '.$value['name'].' olduğunda aşağıdaki mesaj gönderilsin').' : </td>
	          <td>
	            <textarea id="iletimerkezi_order_'.$value['id_order_state'].'_text" name="iletimerkezi_order_'.$value['id_order_state'].'_text" cols="60" rows="5">'.Tools::safeOutput(Configuration::get('iletimerkezi_order_'.$value['id_order_state'].'_text')).'</textarea>
	            <p class="help-block">
	              (Mesajin içinde %orderid% %orderreference% %firstname% %lastname% %telephone% degiskenini kullanabilirsiniz.)
	            </p>
	          </td>                                
	          </tr>
			';

		}
		

		$smarty->assign(
	        array(
	            'action' => Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']),
	            'heading_title' => $this->displayName,
	            'text_iletimerkezi_username' => $this->l('iletimerkezi.com telefon numaranız'),
	            'value_iletimerkezi_username' => Tools::safeOutput(Configuration::get('iletimerkezi_username')),
	            'text_iletimerkezi_password' => $this->l('iletimerkezi.com şifreniz'),
	            'value_iletimerkezi_password' => Tools::safeOutput(Configuration::get('iletimerkezi_password')),
	            'text_iletimerkezi_sender' => $this->l('Başlık Bilginiz'),
	            'value_iletimerkezi_sender' => Tools::safeOutput(Configuration::get('iletimerkezi_sender')),
	            'text_iletimerkezi_admin_gsm' => $this->l('Yönetici gsm numarası'),
	            'value_iletimerkezi_admin_gsm' => Tools::safeOutput(Configuration::get('iletimerkezi_admin_gsm')),
	            'text_iletimerkezi_new_member_text' => $this->l('Sitenize yeni bir üye geldiğinde yönetici gsm numarasına aşağıdaki mesaj gönderilsin'),
	            'value_iletimerkezi_new_member_text' => Tools::safeOutput(Configuration::get('iletimerkezi_new_member_text')),
	            'text_iletimerkezi_new_order_text' => $this->l('Yeni bir sipariş geldiğinde yönetici gsm numarasına aşağıdaki mesaj gönderilsin'),
	            'value_iletimerkezi_new_order_text' => Tools::safeOutput(Configuration::get('iletimerkezi_new_order_text')),
	            'html' => $html,
	            'text_iletimerkezi_new_member_ttm' => $this->l('Yeni üyeye, kullanıcı adı ve şifresi sms olarak gönderilsin'),
	            'value_iletimerkezi_new_member_ttm' => Tools::safeOutput(Configuration::get('iletimerkezi_new_member_ttm')),
	            'text_iletimerkezi_new_order_ttm' => $this->l('Yeni bir sipariş oluşturulduğunda müşteriye sms gitsin'),
	            'value_iletimerkezi_new_order_ttm' => Tools::safeOutput(Configuration::get('iletimerkezi_new_order_ttm')),
	            'button_value' => $this->l('Güncelle')
	        )
	    );
		//die(var_dump($smarty));
	    $setting_form = $this->display(__FILE__, 'setting.tpl');

		//$results = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'group_lang');
		//die(var_export($results));
		
		 /* list of customer groups */
	    $Group = new Group();
	    $groups = $Group->getGroups(1);
		//die(var_export($Group->getGroups(1)));
		array_unshift($groups,  array( 
			'id_group' => '-1',
			'reduction' => '0.00',
			'price_display_method' => '0',
			'name' => 'Tüm Gruplar'
		));

		$smarty->assign(
	        array(
	            'action_multiple' => Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']),
	            'groups' => $groups
	        )
	    );

	    $multiple = $this->display(__FILE__, 'multiple.tpl');
		//it take sms report
		/*
		$this->_getSmsReport();
		
		$sayfada = 10; // sayfada gösterilecek içerik miktarını belirtiyoruz.
 
		$total = $this->_getSendedSmsCount();
 		//die(var_dump($total));
 		if($total)
			$total_page = ceil($total / $sayfada);
		else
 			$total_page = 1;

		$page = isset($_POST['submitFiltersms']) ? (int)$_POST['submitFiltersms'] : 1;
 
		if($page < 1) $page = 1; 
		if($page > $total_page) $page = $total_page; 
 
		$limit = ($page - 1) * $sayfada;

		$smarty->assign(
	        array(
	            
	            'reports' => $this->_getSendedSms($limit, $sayfada),
	            'list_total' => 25,
	            'simple_header' => false,
	            'page' => $page,
	            'total_pages' => $total_page,
	            'table' => 'sms',
	        )
	    );

	    $reports = $this->display(__FILE__,'report.tpl');
		*/
	    

	    $tabs = '<div id="tabs-container">
				    <ul class="tabs-menu">
				        <li class="current"><a href="#tab-1">Ayarlar</a></li>
				        <li><a href="#tab-2">TopluSMS Gönderimi</a></li>
				       <!-- <li><a href="#tab-2">Raporlar</a></li> -->
				    </ul>
				    <div class="tab">
				        <div id="tab-1" class="tab-content">
				        	'.$setting_form.'
				        </div>
				        <div id="tab-2" class="tab-content">
				            '.$multiple.'
				        </div>
				       <!-- <div id="tab-2" class="tab-content">
				            '.$report.'
				        </div> -->
				    </div>
				</div>';

	    //return $output.$setting_form.$reports.$multiple; //$this->displayForm();
	    return $output.$tabs; //$this->displayForm();
	}

	/**
	* @desc it give into database sms count
	* @return int total
	*/
	private function _getSendedSmsCount() {

		$sql = 'SELECT COUNT(*) AS toplam FROM '._DB_PREFIX_.'iletimerkezisms';
		$results = Db::getInstance()->ExecuteS($sql);
 		//die(var_dump($results));
 		if($results == null)
			return $results[0]['toplam'];
		else
			return 0;
	}

	/**
	* @desc it give into database sms report
	* @param string $limit start limit
	* @param string $page final limit
	* @return array sms
	*/
	private function _getSendedSms($limit, $page) {

		$sql = 'SELECT * FROM '._DB_PREFIX_.'iletimerkezisms LIMIT ' . $limit . ', ' . $page;
		//$results = Db::getInstance()->ExecuteS($sql);
		if ($results = Db::getInstance()->ExecuteS($sql))
			return $results;
	}

	/**
	* @desc it give sms report from api
	* @param string $number mobilephone number
	* @param string $message message text
	*/
	private function _getSmsReport() {

		$sql = 'SELECT * FROM '._DB_PREFIX_.'iletimerkezisms WHERE status=1 LIMIT 0,3';
		//$results = Db::getInstance()->ExecuteS($sql);
		//die(var_export($results));
		if ($results = Db::getInstance()->ExecuteS($sql)){
			$iletimerkezi_username = Configuration::get('iletimerkezi_username');
			$iletimerkezi_password = Configuration::get('iletimerkezi_password');
			foreach ($results as $key => $result) {
				$report_id = $result['report_id'];
				$xml = <<<EOS
		<request>
	        <authentication>
	            <username>{$iletimerkezi_username}</username>
	            <password>{$iletimerkezi_password}</password>
	        </authentication>
	        <order>
	            <id>{$report_id}</id>
                <page></page>
                <rowCount></rowCount>
	        </order>
		</request>
EOS;

		        $response = $this->_connect($xml);

		        //$response = simplexml_load_string($result);

				$result_matches = array();
				if(preg_match('/<message>(.*?)<number>(.*?)<\/number>(.*?)<status>(.*?)<\/status>(.*?)<\/message>/si', $response, $result_matches)) {
		            $sended_number  = $result_matches[2];
		            $status_message = $result_matches[4];

		            if($status_message=='111') {
		                //return 'success';
		                $this->_updateSmsStatus($result['id'],2);
		            } elseif($status_message=='110') {
		                //return '';
		                $this->_updateSmsStatus($result['id'],1);
		            } else {
		                //return 'error';
		                $this->_updateSmsStatus($result['id'],3);
		            }
		        }

		        /*if($response->status->code==200){
		        	$this->_updateSmsStatus($result['id'],2);
		        }*/

			}
	        
		}
			
	}

	/**
	* @desc it update to database for sms report
	* @param int $id sms id
	* @param int $status sms status
	*/
	private function _updateSmsStatus($id, $status) {

		Db::getInstance()->execute(
					'UPDATE `'._DB_PREFIX_.'iletimerkezisms` 
					SET `status` = \''.(int)$status.'\' 
					WHERE `id` = '.(int)$id
				);
	}

	/**
	* @desc it save to database for sms report
	* @param int $report_id response id for sms
	* @param string $number mobilephone number
	* @param string $message message text
	* @param int $status sms status
	*/
	private function _saveSendedSms($report_id, $number, $message, $status) {

		Db::getInstance()->insert('iletimerkezisms', array(
		    'report_id' => (int)$report_id,
		    'number' => pSQL($number),
		    'message'      => pSQL($message),
		    'status'      => (int)$status,
		));
	}

	/**
	* @desc fix number
	* @param string $number phone number
	*/
	private function fixPhoneNumber($number) {

		$number = preg_replace('/\D/','',$number);
		$number = substr($number, -10);

		return $number;
	}

	/**
	* @desc send sms
	* @param string $number phone number
	* @param string $message sms text
	*/
	private function sendSms($number, $message) {

		$number = $this->fixPhoneNumber($number);

		$iletimerkezi_username = Configuration::get('iletimerkezi_username');
		$iletimerkezi_password = Configuration::get('iletimerkezi_password');
		$iletimerkezi_sender   = Configuration::get('iletimerkezi_sender');

		//die($iletimerkezi_username.$iletimerkezi_password.$iletimerkezi_sender);
		$xml = <<<EOS
		<request>
	        <authentication>
	            <username>{$iletimerkezi_username}</username>
	            <password>{$iletimerkezi_password}</password>
	        </authentication>
	        <order>
	            <sender>{$iletimerkezi_sender}</sender>
	            <sendDateTime></sendDateTime>
	            <message>
	            	<text><![CDATA[{$message}]]></text>
	                <receipents>
	                	<number>{$number}</number>
	                </receipents>
	            </message>
	        </order>
		</request>
EOS;

		$result = $this->_connect($xml,true);

        $response = simplexml_load_string($result);
  
        if($response->status->code==200){
        	$report_id = $response->order->id;
        	$status = 1;//gönderiliyor
        } else {
        	$report_id = 0;
        	$status = 0;//hata
        }
        //$this->_saveSendedSms($report_id, $number, $message, $status);
        //die(var_dump($result));
        return $response;
	}

	/**
	* @desc connect api
	* @param string $xml post data
	* @param bool $send url address
	*/
	private function _connect($xml, $send = false) {
		
		if($send)
			$url = 'http://api.iletimerkezi.com/v1/send-sms';
		else
			$url = 'http://api.iletimerkezi.com/v1/get-report';

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);

        return $result;
	}

	/**
	* @desc send multiple sms
	* @param int $group_id customer group id
	* @param string $message sms text
	*/
	private function sendMultipleSms($group_id, $message) {
		/* group_id = -1 all groups */
		if($group_id != -1){
			$Group = new Group($group_id);
			//die(var_export($Group->getCustomers()));
			if(isset($message) && !empty($message)){ //die("asd");

				foreach ($Group->getCustomers() as $key => $customer) {
					$Customer = new Customer(intval($customer['id_customer']));
					//die("<pre>".var_export($Customer->getAddresses(1),1)."</pre>");
					$address = $Customer->getAddresses(1);
					
					if(!empty($address[0]['phone_mobile'])){
						$val = array('%firstname%', '%lastname%');
						$change = array($customer['firstname'], $customer['lastname']);
						$message = str_replace($val, $change, $message);

						$this->sendSms( $address[0]['phone_mobile'], $message);
					}
				}
			}
		} else { //multipl sms
			$Groups = new Group();

			/* customer groups */
			foreach ($Groups->getGroups(1) as $key => $value) {
				$Group = new Group($value['id_group']);
				//die(var_export($Group->getCustomers()));
				if(isset($message) && !empty($message)){ //die("asd");

					foreach ($Group->getCustomers() as $key => $customer) {
						$Customer = new Customer(intval($customer['id_customer']));
						//die("<pre>".var_export($Customer->getAddresses(1),1)."</pre>");
						$address = $Customer->getAddresses(1);
						//die(var_dump($address[0]['phone_mobile']));
						
						if(!empty($address[0]['phone_mobile'])){
							//die(var_dump($address[0]['phone_mobile']));
							$val = array('%firstname%', '%lastname%');
							$change = array($customer['firstname'], $customer['lastname']);
							$message = str_replace($val, $change, $message);
							$response = $this->sendSms( $address[0]['phone_mobile'], $message);
							
						}
					}
				}
			}
			
		}
		if($response->status->code==200){
	    	echo '<p class ="conf confirm">Mesajınız başarılı bir şekilde iletildi.<p>';
	    } else {
	    	echo '<p class ="alert error">Mesajınız oluşan bir hata sebebiyle gönderilemedi.<p>';
	    }
	}

	/**
	* @desc order status update
	* @param array $params Parameters
	*/
	public function hookPostUpdateOrderStatus($params) {

		//Siparisin durumu degistigi zaman durumu sms ile bildir
		$message = Configuration::get('iletimerkezi_order_'.$params['newOrderStatus']->id.'_text');

		if(isset($message) && !empty($message)){
			
			$order = new Order ($params['id_order']);
			$customer = new Customer ($order->id_customer);
			$addressInvoice = new Address(intval($order->id_address_invoice));
			//$Customer = new Customer(intval($params['cart']->id_customer));
			$val = array('%orderid%', '%firstname%', '%lastname%', '%telephone%');
			$change = array($params['id_order'], $customer->firstname, $customer->lastname, $addressInvoice->phone_mobile);
			$message = str_replace($val, $change, $message);
			//die($message);

			$this->sendSms($addressInvoice->phone_mobile,$message);
		}
	}

	/**
	* @desc new order
	* @param array $params Parameters
	*/
	public function hookNewOrder($params) {
		//die(var_dump($params));
		$order = $params['order'];
		//die(var_dump($order));

		//we did this for product detail
		$products = Db::getInstance()->executeS('
			SELECT product_id, product_name, product_quantity, product_reference
			FROM '._DB_PREFIX_.'order_detail
			WHERE id_order='.(int)$order->id
		);

		// bir siparisde birden fazla urun icin dongude
		$product_name 	   = "";			
		$product_reference = "";			
		$product_quantity  = "";	
		
		foreach ($products as $key => $product) {

			if($key==0)
				$parser = "";
			else
				$parser = ",";

			$product_name .= $parser.$product['product_name'];			
			$product_reference .= $parser.$product['product_reference'];			
			$product_quantity .= $parser.$product['product_quantity'];			
		}

		//Yeni bir siparis geldiginde yoneticiye haber ver
		$message      = Configuration::get('iletimerkezi_new_order_text');

		if(isset($message) && !empty($message)){

			$phone_mobile = Configuration::get('iletimerkezi_admin_gsm');

			$val = array('%orderid%', '%productname%', '%productmodel%', '%productquantity%');
			$change = array($order->id, $product_name, $product_reference, $product_quantity);
			$message = str_replace($val, $change, $message);
			$this->sendSms($phone_mobile,$message);
		}

		//Yeni bir siparis geldiginde musteriye gidicek mesaji yolla
		$message_member      = Configuration::get('iletimerkezi_new_order_ttm');

		if(isset($message_member) && !empty($message_member)){

			$Address = new Address((int)($order->id_address_invoice));
			$phone_mobile_member = $Address->phone_mobile;
			
			$val = array('%orderid%', '%productname%', '%productmodel%', '%productquantity%');
			$change = array($order->id, $product_name, $product_reference, $product_quantity);
			$message_member = str_replace($val, $change, $message_member);
			$this->sendSms($phone_mobile_member,$message_member);
		}
		
		/* //debug	
		$fp = fopen("C:\\wamp\\www\\prestashop\\loasdas.html", 'a');
		fwrite($fp, "<pre>".$product_name."===".$product_reference."===".$product_quantity."===".var_export($products,1)."</pre>");
		fclose($fp);

		$fp = fopen("C:\\wamp\\www\\prestashop\\loatesa.html", 'a');
		fwrite($fp, "<pre>".var_export($message,1)."</pre>");
		fclose($fp);
		*/
	}

	/**
	* @desc Customer account add 
	* @param array $params Parameters
	*/
	public function hookCreateAccount($params) {

		$customer = $params['newCustomer'];
		$customer_pass = $params['_POST']['passwd'];

		$customer_phone = false;

		if(array_key_exists('phone_mobile', $params['_POST']) ){
			$customer_phone = $params['_POST']['phone_mobile'];

			$val = array('%firstname%', '%lastname%', '%telephone%');
			$change = array($customer->firstname, $customer->lastname, $customer_phone);
		} else {

			$val = array('%firstname%', '%lastname%');
			$change = array($customer->firstname, $customer->lastname);
		}

		//Yeni bir musteri siteye kaydolunca yoneticiye gidicek mesaji yolla
		$message      = Configuration::get('iletimerkezi_new_member_text');

		if(isset($message) && !empty($message) ){
			$phone_mobile = Configuration::get('iletimerkezi_admin_gsm');
			
			$message = str_replace($val, $change, $message);
			$this->sendSms($phone_mobile, $message);
		}

		//Yeni bir musteri siteye kaydolunca musteriye gidicek mesaji yolla
		if($customer_phone != false){
			$message_member      = Configuration::get('iletimerkezi_new_member_ttm');
			if(isset($message_member) && !empty($message_member)){
				$val = array('%firstname%', '%lastname%', '%telephone%', '%email%', '%password%');
				$change = array($customer->firstname, $customer->lastname, $customer_phone, $customer->email, $customer_pass);
				$message_member = str_replace($val, $change, $message_member);
				$this->sendSms($customer_phone,$message_member);
			}
		}

	}

	/**
	* Returns module content for header
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookTop($params)
	{
		if (!$this->active)
			return;

		$this->smarty->assign(array(
			'cart' => $this->context->cart,
			'cart_qties' => $this->context->cart->nbProducts(),
			'logged' => $this->context->customer->isLogged(),
			'customerName' => ($this->context->customer->logged ? $this->context->customer->firstname.' '.$this->context->customer->lastname : false),
			'firstName' => ($this->context->customer->logged ? $this->context->customer->firstname : false),
			'lastName' => ($this->context->customer->logged ? $this->context->customer->lastname : false),
			'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order'
		));
		return $this->display(__FILE__, 'blockuserinfo.tpl');
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockuserinfo.css', 'all');
	}
}
?>
<link type="text/css" rel="stylesheet" href="../modules/iletimerkezisms/iletimerkezi.css" />
<script type="text/javascript" src="../modules/iletimerkezisms/js/iletimerkezi.js"></script>