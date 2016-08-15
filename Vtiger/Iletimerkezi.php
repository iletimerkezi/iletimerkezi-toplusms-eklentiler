<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/


class SMSNotifier_ClickATell_Provider implements SMSNotifier_ISMSProvider_Model {
	
	private $_username;
	private $_password;
	private $_parameters = array();
	
	const SERVICE_URI = 'http://api.iletimerkezi.com';
	private static $REQUIRED_PARAMETERS = array('from');
	
	function __construct() {		
	}
	
	public function getName() {
		return 'İletimerkezi';
	}

	public function setAuthParameters($username, $password) {
		$this->_username = $username;
		$this->_password = $password;
	}
	
	public function setParameter($key, $value) {
		$this->_parameters[$key] = $value;
	}
	
	public function getParameter($key, $defvalue = false)  {
		if(isset($this->_parameters[$key])) {
			return $this->_parameters[$key];
		}
		return $defvalue;
	}
	
	public function getRequiredParams() {
		return self::$REQUIRED_PARAMETERS;
	}
	
	public function getServiceURL($type = false) {		
		if($type) {
			switch(strtoupper($type)) {				
				// case self::SERVICE_AUTH: return  self::SERVICE_URI . '/http/auth';
				case self::SERVICE_SEND: return  self::SERVICE_URI . '/v1/send-sms/';
				case self::SERVICE_QUERY: return self::SERVICE_URI . '/v1/get-report/';
			}
		}
		return false;
	}
	
	protected function prepareParameters() {
		$params = array('user' => $this->_username, 'password' => $this->_password);
		foreach (self::$REQUIRED_PARAMETERS as $key) {
			$params[$key] = $this->getParameter($key);
		}
		return $params;
	}
	
	public function send($message, $tonumbers) {
		
		if(!is_array($tonumbers)) {
			$tonumbers = array($tonumbers);
		}
		
		$params = $this->prepareParameters();
		$text   = urlencode($message);
		$sender = urlencode($params['from']);
		
        $results = array();
        foreach ($tonumbers as $number) {
			$number    = preg_replace('/\D/','',$number);
			$number    = substr($number, -10);
			$res       = file_get_contents('http://api.iletimerkezi.com/v1/send-sms/get/?username='.$params['user'].'&password='.$params['password'].'&text='.$text.'&receipents='.$number.'&sender='.$sender);
			
			preg_match_all('|\<code\>.*\<\/code\>|U', $res, $order_id,PREG_PATTERN_ORDER);
			preg_match_all('|\<message\>.*\<\/message\>|U', $res, $message,PREG_PATTERN_ORDER);
			preg_match_all('|\<id\>.*\<\/id\>|U', $res, $order_status,PREG_PATTERN_ORDER);

			$order_id     = intVal($order_id[0][0]);
			$order_status = intVal($order_status[0][0]);
			$message      = $message[0][0];

			if($order_status=='200') {
				$result = array( 'error' => false, 'statusmessage' => $message,'to'=>$number, 'id'=>$order_id,'status'=>self::MSG_STATUS_PROCESSING);
			} else {				
				$result = array( 'error' => true, 'statusmessage' => $message, 'to'=>$number);
			}
						
			$results[] = $result;
        }
		
		return $results;
	}
	
	public function query($messageid) {

		/*
		$result['error'] = true;
		$result['needlookup'] = 0;
		$result['statusmessage'] = 'Gitti';
		return $result;
		*/
		$params             = $this->prepareParameters();

		$params['data'] = '<request>
        <authentication>
                <username>'.$params['user'].'</username>
                <password>'.$params['password'].'</password>
        </authentication>
        <order>
                <id>'.$messageid.'</id>
                <page></page>
                <rowCount></rowCount>
        </order>
</request>';
		
		
		$serviceURL = $this->getServiceURL(self::SERVICE_QUERY);
		$httpClient = new Vtiger_Net_Client($serviceURL);					
		$response   = $httpClient->doPost($params);		
		$response   = trim($response);

		preg_match_all('|\<status\>.*\<\/status\>|U', $response, $order_status,PREG_PATTERN_ORDER);

		$result = array( 'error' => false, 'needlookup' => 1, 'statusmessage' => '' );
		if($order_status[0][2]=='113') {
			$result['status'] = self::MSG_STATUS_PROCESSING;
		} elseif($order_status[0][2]=='114') {
			$result['status'] = self::MSG_STATUS_DELIVERED;
		} elseif($order_status[0][2]=='115') {
			$result['error']         = true;
			$result['needlookup']    = 0;
			$result['statusmessage'] = $order_status[0][1];
		}

		return $result;		
	}
}
?>
