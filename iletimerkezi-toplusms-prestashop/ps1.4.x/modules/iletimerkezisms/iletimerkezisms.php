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
	private $debug;

	public function __construct()
	{
		$this->name = 'iletimerkezisms';
		$this->tab = 'administration';
		$this->version = 1.07;
		$this->author = 'www.iletimerkezi.com';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Iletimerkezi Sms');
		$this->description = $this->l('Müşterilerinize, siparişinin kargo durumlarını sms ile bildirin.');
		$this->bootstrap = true;

		$this->debug = false;
	}

	public function install()
	{
		//return
			if(
				parent::install() AND
				$this->registerHook('actionOrderStatusUpdate') AND
				$this->registerHook('actionCustomerAccountAdd') AND
				$this->registerHook('actionAdminOrdersTrackingNumberUpdate') AND
				$this->registerHook('displayOrderConfirmation')

			){
				$this->installSmsReportTable();
				return true;
			}
			// buraya else durumu yapilmali
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
		  `error` text NOT NULL,
		  `log` text NOT NULL,
		  `date_send` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `report_id` (`report_id`)
		) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;');
	}

	public function getContent()
	{
	    $output = null;

	    /* for send multiple sms */
	    if(isset($_POST['bulk']) && $_POST['bulk'] == '1' ) {
	    	// die(var_export($_POST));
	    	$this->sendMultipleSms($_POST['iletimerkezi_customer_group'], $_POST['iletimerkezi_message']);

	    	Tools::redirectAdmin(
				'index.php?controller=AdminModules&token='.Tools::getValue('token').'&configure=iletimerkezisms&tab_module=administration&module_name=iletimerkezisms'
			);
	    } else {

		    if (isset($_POST['submitModule'])) {
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

		    if (Tools::isSubmit('submit'.$this->name)) {
		        $my_module_name = strval(Tools::getValue('iletimerkezisms'));
		        if (!$my_module_name  || empty($my_module_name) || !Validate::isGenericName($my_module_name))
		            $output .= $this->displayError( $this->l('Invalid Configuration value') );
		        else {
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

	    //print content of setting.tpl
		foreach ($order_status as $key => $value) {

			if(Configuration::get('iletimerkezi_order_'.$value['id_order_state'].'_status'))
				$checked = "checked";
			else
				$checked = "";

			$html .= '<tr class="odd">
                <td class="">'.$this->l('Siparişin durumu '.$value['name'].' olduğunda aşağıdaki mesaj gönderilsin').' :
                  <p class="help-block">
	              	(Mesajin içinde %orderid% %orderreference% %firstname% %lastname% %telephone% degiskenini kullanabilirsiniz.)
	              </p>
                </td>
                <td class="">
                  <textarea id="iletimerkezi_order_'.$value['id_order_state'].'_text" name="iletimerkezi_order_'.$value['id_order_state'].'_text" cols="60" rows="5"  onkeypress="smsCalculatorSetting(\'order_'.$value['id_order_state'].'_text\');" onkeyup="smsCalculatorSetting(\'order_'.$value['id_order_state'].'_text\');">'.Tools::safeOutput(Configuration::get('iletimerkezi_order_'.$value['id_order_state'].'_text')).'</textarea>
                </td>
                <td class="">
                  <div class="span7" style="margin-left: 0;">
                    <p>Mesaj sayısı: <span id="smsCount_order_'.$value['id_order_state'].'_text" style="font-weight: bold;">1</span><br>
                      Karakter sayısı: <span id="characterCount_order_'.$value['id_order_state'].'_text" style="font-weight: bold;">0</span></p>
                  </div>
                </td>
                <td class="text-right">
                	<input id="iletimerkezi_order_'.$value['id_order_state'].'_status" name="iletimerkezi_order_'.$value['id_order_state'].'_status" value="0" type="hidden">
                	<input id="iletimerkezi_order_'.$value['id_order_state'].'_status" name="iletimerkezi_order_'.$value['id_order_state'].'_status" '.$checked.' value="1" type="checkbox">
                </td>
              </tr>';

		}

		if(Configuration::get('iletimerkezi_new_member_status'))
			$checked_iletimerkezi_new_member_status = 'checked';
		else
			$checked_iletimerkezi_new_member_status = '';

		if(Configuration::get('iletimerkezi_new_order_status'))
			$checked_iletimerkezi_new_order_status = 'checked';
		else
			$checked_iletimerkezi_new_order_status = '';

		if(Configuration::get('iletimerkezi_new_member_status_to_member'))
			$checked_iletimerkezi_new_member_status_to_member = 'checked';
		else
			$checked_iletimerkezi_new_member_status_to_member = '';

		if(Configuration::get('iletimerkezi_new_order_status_to_member'))
			$checked_iletimerkezi_new_order_status_to_member = 'checked';
		else
			$checked_iletimerkezi_new_order_status_to_member = '';

		if(Configuration::get('iletimerkezi_tracking_number_status'))
			$checked_iletimerkezi_tracking_number_status = 'checked';
		else
			$checked_iletimerkezi_tracking_number_status = '';


		$this->context->smarty->assign(
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
	            'checked_iletimerkezi_new_member_status' => Tools::safeOutput($checked_iletimerkezi_new_member_status),
	            'text_iletimerkezi_new_order_text' => $this->l('Yeni bir sipariş geldiğinde yönetici gsm numarasına aşağıdaki mesaj gönderilsin'),
	            'value_iletimerkezi_new_order_text' => Tools::safeOutput(Configuration::get('iletimerkezi_new_order_text')),
	            'checked_iletimerkezi_new_order_status' => Tools::safeOutput($checked_iletimerkezi_new_order_status),
	            'html' => $html,
	            'text_iletimerkezi_new_member_text_to_member' => $this->l('Yeni üyeye, kullanıcı adı ve şifresi sms olarak gönderilsin'),
	            'value_iletimerkezi_new_member_text_to_member' => Tools::safeOutput(Configuration::get('iletimerkezi_new_member_text_to_member')),
	            'checked_iletimerkezi_new_member_status_to_member' => Tools::safeOutput($checked_iletimerkezi_new_member_status_to_member),
	            'text_iletimerkezi_new_order_text_to_member' => $this->l('Yeni bir sipariş oluşturulduğunda müşteriye sms gitsin'),
	            'value_iletimerkezi_new_order_text_to_member' => Tools::safeOutput(Configuration::get('iletimerkezi_new_order_text_to_member')),
	            'checked_iletimerkezi_new_order_status_to_member' => Tools::safeOutput($checked_iletimerkezi_new_order_status_to_member),
	            'text_iletimerkezi_tracking_number' => $this->l('Takip numarası oluşturulduğunda müşteriye sms gitsin'),
	            'value_iletimerkezi_tracking_number' => Tools::safeOutput(Configuration::get('iletimerkezi_tracking_number')),
	            'checked_iletimerkezi_tracking_number_status' => Tools::safeOutput($checked_iletimerkezi_tracking_number_status),
	            'button_value' => $this->l('Güncelle')
	        )
	    );

	    $setting_form = $this->display(__FILE__, 'views/templates/admin/setting.tpl');

		//$results = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'group_lang');
		//die(var_export($results));
		//it take sms report
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

		$this->context->smarty->assign(
	        array(
	            'action' => Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']),
	            'reports' => $this->_getSendedSms($limit, $sayfada),
	            'list_total' => 25,
	            'simple_header' => false,
	            'page' => $page,
	            'total_pages' => $total_page,
	            'table' => 'sms',
	        )
	    );

	    $reports = $this->display(__FILE__, 'views/templates/admin/report.tpl');

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

		$this->context->smarty->assign(
	        array(
	            'action_multiple' => Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']),
	            'groups' => $groups
	        )
	    );
//$LANG['credit'].': <b>'.$credit.'</b> <a style=" background: none; border: none;color: red;display: inline;margin: 0;padding: 0 10px;text-decoration: none;" href="https://www.iletimerkezi.com/index.php?function=default&obj1=signinViaGet&gsm='.$apiparams->iletimerkezi_username.'&password='.$apiparams->iletimerkezi_password.'">SMS Satin Al</a>';
	    $multiple = $this->display(__FILE__, 'views/templates/admin/multiple.tpl');

	    $getdomain = $this->_getDomain();
	    $credit = $this->_getBalance();
	    $iletimerkezi_username = Configuration::get('iletimerkezi_username');
		$iletimerkezi_password = Configuration::get('iletimerkezi_password');

	    $tabs = '<div class="panel">
		    <h3><i class="icon-cogs"></i> Iletimerkezi Sms</h3>
		    <div class="bs-example bs-example-tabs">
		    <ul id="myTab" class="nav nav-tabs">
		      <li class=""><a href="#setting" data-toggle="tab">Ayarlar</a></li>
		      <li class=""><a href="#bulkSms" data-toggle="tab">Toplu SMS Gönderimi</a></li>
		      <li class="active"><a href="#reports" data-toggle="tab">Raporlar</a></li>
		    	<div style="float:right;">Kalan SMS : <b>' . $credit . '</b>  <a style=" background: none; border: none;color: red;display: inline;margin: 0;padding: 0 10px;text-decoration: none;" href="https://www.iletimerkezi.com/index.php?function=default&obj1=signinViaGet&gsm='.$iletimerkezi_username.'&password='.$iletimerkezi_password.'">SMS Satin Al</a></div>
		    </ul>
		    <div id="myTabContent" class="tab-content">
		      <div class="tab-pane fade" id="setting">
		        '.$setting_form.'
		      </div>
		      <div class="tab-pane fade" id="bulkSms">
		        '.$multiple.'
		      </div>
		      <div class="tab-pane fade active in" id="reports">
		        '.$reports.'
		      </div>
		    </div>
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
 		if($results != null)
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

		$sql = 'SELECT * FROM '._DB_PREFIX_.'iletimerkezisms ORDER BY id DESC LIMIT ' . $limit . ', ' . $page;
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
	private function _saveSendedSms($report_id, $number, $message, $status, $error, $log) {

		Db::getInstance()->insert('iletimerkezisms', array(
		    'report_id' => (int)$report_id,
		    'number' => pSQL($number),
		    'message'      => pSQL($message),
		    'status'      => (int)$status,
		    'error'      => pSQL($error),
		    'log'      => pSQL($log),
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

	private function _getBalance(){

		$iletimerkezi_username = Configuration::get('iletimerkezi_username');
		$iletimerkezi_password = Configuration::get('iletimerkezi_password');

        $balance_xml = '<?xml version="1.0" encoding="UTF-8" ?>
            <request>
                <authentication>
                    <username>'.$iletimerkezi_username.'</username>
                    <password>'.$iletimerkezi_password.'</password>
                </authentication>
            </request>';

        $ch = curl_init('http://api.iletimerkezi.com/v1/get-balance');
        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $balance_xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        if(preg_match('/<status>(.*?)<code>(.*?)<\/code>(.*?)<message>(.*?)<\/message>(.*?)<\/status>(.*?)<balance>(.*?)<sms>(.*?)<\/sms>(.*?)<\/balance>/si', $result, $result_matches)) {
            $status_code    = $result_matches[2];
            $status_message = $result_matches[4];
            $balance        = $result_matches[8];
        }

        if($status_code=='200') {
            return $balance;
        } else {
            return $status_message;
        }
    }


    private function _getDomain(){

    	$domain = $_SERVER['HTTP_HOST'];
		$iletimerkezi_username = Configuration::get('iletimerkezi_username');
		$iletimerkezi_password = Configuration::get('iletimerkezi_password');

        $balance_xml = '<?xml version="1.0" encoding="UTF-8" ?>
            <request>
                <authentication>
                    <username>'.$iletimerkezi_username.'</username>
                    <password>'.$iletimerkezi_password.'</password>
                </authentication>
                <pluginUser>
                        <site><![CDATA['.$domain.']]></site>
                        <name>opencart</name>
                </pluginUser>
            </request>';

        $ch = curl_init('http://api.iletimerkezi.com/v1/add-plugin-user');
        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $balance_xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return true;
    }

	/**
	* @desc send sms
	* @param string $number phone number
	* @param string $message sms text
	*/
	private function sendSms($number, $message) {

		if($this->debug) {
			$this->_writeLog("Number : ".$number." Mesaj: " . $message);
		}

		$number = $this->fixPhoneNumber($number);

		$iletimerkezi_username = Configuration::get('iletimerkezi_username');
		$iletimerkezi_password = Configuration::get('iletimerkezi_password');
		$iletimerkezi_sender   = Configuration::get('iletimerkezi_sender');

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

		// for DEBUG
		if($this->debug) {
			$this->_writeLog("XML : ".var_export($xml,1));
			$this->_writeLog("Result : ".var_export($result,1));
		}
		/*
		// for DEBUG TEST
		$result = '<!--?xml version="1.0" encoding="UTF-8"?-->
		<response>
		  <status>
		    <code>200</code>
		    <message>Ä°ÅŸlem baÅŸarÄ±lÄ±</message>
		  </status>
		  <order>
		    <id>4343549</id>
		  </order>
		</response>
		';
		*/

		$response = simplexml_load_string($result);

        if($response->status->code==200){
        	$report_id = $response->order->id;
        	$status = 1;//gönderiliyor

        	//$this->addLog($xml);
        	$this->addError("Status Code: ".$response->status->code." Message: ".$response->status->message);
        } else {
        	$report_id = 0;
        	$status = 0;//hata

        	//$this->addLog($xml);
            $this->addError("Status Code: ".$response->status->code." Message: ".$response->status->message);
        }

        $this->_saveSendedSms($report_id, $number, $message, $status, $this->getErrors(), $this->getLogs());

	}

	private function _writeLog($log, $filename = "/log_iletimerkezi.txt") {
		$fp = fopen(dirname(__FILE__) . $filename, 'a');
		fwrite($fp, $log . "\n\n");
		fclose($fp);
	}

	public function addError($error) {
        $this->errors[] = $error;
    }

    public function addLog($log) {
        $this->logs[] = $log;
    }

    public function getErrors() {
        $res = '<pre><p><ul>';
        foreach($this->errors as $d){
            $res .= "<li>$d</li>";
        }
        $res .= '</ul></p></pre>';
        return $res;
    }

    public function getLogs() {
        $res = '<pre><p><strong>Sms gönderim detayı </strong><ul>';
        foreach($this->logs as $d){
            $res .= "<li>$d</li>";
        }
        $res .= '</ul></p></pre>';
        return $res;
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

		if($group_id != -1){ //single group

			$Group = new Group($group_id);

			$group_customers = $Group->getCustomers();
			// die(var_export($Group->getCustomers()));

			if(isset($message) && !empty($message) && !empty($group_customers)){

				foreach ($group_customers as $key => $customer) {
					$Customer = new Customer(intval($customer['id_customer']));
					//die("<pre>".var_export($Customer->getAddresses(1),1)."</pre>");
					$address = $Customer->getAddresses(1);

					if(!empty($address[0]['phone_mobile'])) {
						$val = array('%firstname%', '%lastname%');
						$change = array($customer['firstname'], $customer['lastname']);
						$message = str_replace($val, $change, $message);

						$this->sendSms( $address[0]['phone_mobile'], $message);
					}
				}
			}
		} else { //multipl sms all groups
			$Groups = new Group();

			/* customer groups */
			foreach ($Groups->getGroups(1) as $key => $value) {
				$Group = new Group($value['id_group']);

				$group_customers = $Group->getCustomers();
				// die(var_export($Group->getCustomers()));

				if(isset($message) && !empty($message) && !empty($group_customers)){
					// die(var_export($group_customers));
					foreach ($group_customers as $key => $customer) {

						$Customer = new Customer(intval($customer['id_customer']));
						// die("<pre>".var_export($Customer->getAddresses(1),1)."</pre>");
						$address = $Customer->getAddresses(1);

						if(!empty($address[0]['phone_mobile'])){
							$val = array('%firstname%', '%lastname%');
							$change = array($customer['firstname'], $customer['lastname']);
							$message = str_replace($val, $change, $message);

							$this->sendSms( $address[0]['phone_mobile'], $message);
						}
					}
				}
			}

		}
	}

	/**
	* @desc order status update
	* @param array $params Parameters
	*/
	public function hookActionOrderStatusUpdate($params) {

		//Siparisin durumu degistigi zaman durumu sms ile bildir
		$message = Configuration::get('iletimerkezi_order_'.$params['newOrderStatus']->id.'_text');
		$status = Configuration::get('iletimerkezi_order_'.$params['newOrderStatus']->id.'_status');

		//siparisin referans kodu müsteri tarafında gozukuyor
		$order = new Order($params['id_order']);
		//die(var_dump($order->reference));
		if(isset($message) && !empty($message) && isset($status) && !empty($status) ){
			$addressInvoice = new Address(intval($params['cart']->id_address_invoice));
			$Customer = new Customer(intval($params['cart']->id_customer));

			$val = array('%orderid%', '%orderreference%', '%firstname%', '%lastname%', '%telephone%');
			$change = array($params['id_order'], $order->reference, $Customer->firstname, $Customer->lastname, $addressInvoice->phone_mobile);
			$message = str_replace($val, $change, $message);

			$this->sendSms($addressInvoice->phone_mobile,$message);
		}
	}

	/**
	* @desc new order
	* @param array $params Parameters
	*/
	public function hookDisplayOrderConfirmation($params) {

		$order = $params['objOrder'];
		// die(var_dump($order->id_customer));

		$Customer = new Customer(intval($order->id_customer));
		// die(var_export($Customer));

		$Address = new Address((int)($order->id_address_invoice));
		$phone_mobile_member = $Address->phone_mobile;

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

		// Yeni bir siparis geldiginde yoneticiye haber ver
		$message 	= Configuration::get('iletimerkezi_new_order_text');
		$status 	= Configuration::get('iletimerkezi_new_order_status');

		if(isset($message) && !empty($message) && isset($status) && !empty($status)){

			$phone_mobile = Configuration::get('iletimerkezi_admin_gsm');

			$val = array(
					'%orderid%', '%orderreference%', '%productname%', '%productmodel%', '%productquantity%',
					'%firstname%', '%lastname%', '%telephone%'
				);

			$change = array(
					$order->id, $order->reference, $product_name, $product_reference, $product_quantity,
					$Customer->firstname, $Customer->lastname, $phone_mobile_member
				);

			$message = str_replace($val, $change, $message);
			$this->sendSms($phone_mobile,$message);
		}

		// Yeni bir siparis geldiginde musteriye gidicek mesaji yolla
		$message_member      = Configuration::get('iletimerkezi_new_order_text_to_member');
		$status_member      = Configuration::get('iletimerkezi_new_order_status_to_member');

		if(isset($message_member) && !empty($message_member) && isset($status_member) && !empty($status_member)){

			$val = array(
					'%orderid%', '%orderreference%', '%productname%', '%productmodel%', '%productquantity%',
					'%firstname%', '%lastname%', '%telephone%'
				);

			$change = array(
					$order->id, $order->reference, $product_name, $product_reference, $product_quantity,
					$Customer->firstname, $Customer->lastname, $phone_mobile_member
				);

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

		$fp = fopen("/Users/ns/workspaces/ps/1609/log_adem.html", 'a');
		fwrite($fp, "<pre>Adem</pre>");
		fclose($fp);
		*/
	}

	/**
	* @desc Customer account add
	* @param array $params Parameters
	*/
	public function hookActionCustomerAccountAdd($params) {

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
		$status      = Configuration::get('iletimerkezi_new_member_status');

		if(isset($message) && !empty($message) && isset($status) && !empty($status) ){
			$phone_mobile = Configuration::get('iletimerkezi_admin_gsm');

			$message = str_replace($val, $change, $message);
			$this->sendSms($phone_mobile, $message);
		}

		//Yeni bir musteri siteye kaydolunca musteriye gidicek mesaji yolla
		if($customer_phone != false){

			$message_member      = Configuration::get('iletimerkezi_new_member_text_to_member');
			$status_member      = Configuration::get('iletimerkezi_new_member_status_to_member');

			if(isset($message_member) && !empty($message_member) && isset($status_member) && !empty($status_member) ){
				$val = array('%firstname%', '%lastname%', '%telephone%', '%email%', '%password%');
				$change = array($customer->firstname, $customer->lastname, $customer_phone, $customer->email, $customer_pass);
				$message_member = str_replace($val, $change, $message_member);
				$this->sendSms($customer_phone,$message_member);
			}
		}

	}

	/**
	* @desc Tracking Number Update
	* @param array $params Parameters
	*/
	public function hookActionAdminOrdersTrackingNumberUpdate($params) {

		$order = $params['order'];
		$carriername = $params['carrier']->name;
		$carrierurl = $params['carrier']->url;

		// $fp = fopen("/Users/ns/workspaces/ps/1609/log_adem5.txt", 'a');
		// // fwrite($fp, "<pre>".print_r($order->shipping_number,1)."</pre>");
		// fwrite($fp, "<pre>".print_r($order->id,1)."</pre>");
		// fclose($fp);

		//Siparisin durumu degistigi zaman durumu sms ile bildir
		$message = Configuration::get('iletimerkezi_tracking_number');
		$status = Configuration::get('iletimerkezi_tracking_number_status');

		//siparisin referans kodu müsteri tarafında gozukuyor

		//die(var_dump($order->reference));
		if(isset($message) && !empty($message) && isset($status) && !empty($status) ){
			$addressInvoice = new Address(intval($order->id_address_invoice));
			$Customer = new Customer(intval($order->id_customer));

			$val = array('%orderid%', '%orderreference%', '%firstname%', '%lastname%', '%telephone%', '%trackingnumber%','%kargoadı%','%url%');
			$change = array($order->id, $order->reference, $Customer->firstname, $Customer->lastname, $addressInvoice->phone_mobile, $order->shipping_number,$carriername,$carrierurl);
			$message = str_replace($val, $change, $message);

			$this->sendSms($addressInvoice->phone_mobile,$message);
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