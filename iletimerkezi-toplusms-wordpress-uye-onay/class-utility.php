<?php
/** miniOrange enables user to log in through mobile authentication as an additional layer of security over password.
    Copyright (C) 2015  miniOrange

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
* @package 		miniOrange OAuth
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

class MO_Validation_Utility{

	public $email;
	public $phone;
	
	private $defaultCustomerKey = "16555";
	private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
	public static $pCode = "UHJlbWl1bSBQbGFuIC0gV1AgT1RQIFZFUklGSUNBVElPTg==";
	public static $bCode = "RG8gaXQgWW91cnNlbGYgUGxhbiAtIFdQIE9UUCBWRVJJRklDQVRJT04=";
	
	public static function get_hidden_phone($phone){
		$hidden_phone = 'xxxxxxx' . substr($phone,strlen($phone) - 3);
		return $hidden_phone;
	}
	
	public static function mo_check_empty_or_null( $value ) {
		if( ! isset( $value ) || empty( $value ) ) {
			return true;
		}
		return false;
	}
	
	public static function mo_is_curl_installed() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			return 1;
		} else 
			return 0;
	}

	public static function mo_curpageurl() {
		$pageURL = 'http';
		
		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on"))
			$pageURL .= "s";
			
		$pageURL .= "://";

		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			
		else
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		
		if ( function_exists('apply_filters') ) apply_filters('wppb_curpageurl', $pageURL);

        return $pageURL;
	}
	
	public static function mo_check_number_length($token){
		if(is_numeric($token)){
			if(strlen($token) >= 4 && strlen($token) <= 8){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public static function mo_get_hiden_email($email){
        if(!isset($email) || trim($email)===''){
			return "";
		}
		$emailsize = strlen($email);
		$partialemail = substr($email,0,1);
		$temp = strrpos($email,"@");
		$endemail = substr($email,$temp-1,$emailsize);
		for($i=1;$i<$temp;$i++){
			$partialemail = $partialemail . 'x';
		}
		$hiddenemail = $partialemail . $endemail;
               
        return $hiddenemail;
    }
	
	public static function check_if_request_is_from_mobile_device($useragent){
		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
			return true;
		}else{
			return false;
		}
	}

	public static function mo_customer_validation_is_customer_registered(){
		
		$get_balance = MO_Validation_Utility::get_balance();
		if( !empty($get_balance) && $get_balance[1] == true ) {
			return 1;
		} else {
			return 0;
		}
	}

	public static function mo_is_customer_validated(){
		$email 			= get_option('mo_customer_validation_admin_email');
		$customerKey 	= get_option('mo_customer_validation_admin_customer_key');
		if( ! $email || ! $customerKey || ! is_numeric( trim( $customerKey ) ) ) {
			return 0;
		} else {
			return get_option('mo_customer_check_ln') ? get_option('mo_customer_check_ln') :  0;
		}
	}



	function create_customer(){

		$url = get_option('mo_customer_validation_host_name') . '/moas/rest/customer/add';
		$ch = curl_init( $url );
		$this->email = get_option('mo_customer_validation_admin_email');
		$this->phone = get_option('mo_customer_validation_admin_phone');
		$password = get_option('mo_customer_validation_admin_password');
		$company = get_option('mo_customer_validation_company_name');
		$first_name = get_option('mo_customer_validation_first_name');
		$last_name = get_option('mo_customer_validation_last_name');

		$fields = array(
			'companyName' => $company,
			'areaOfInterest' => 'WP OTP Verification Plugin',
			'firstname'	=> $first_name,
			'lastname'	=> $last_name,
			'email'		=> $this->email,
			'phone'		=> $this->phone,
			'password'	=> $password
		);
		$field_string = json_encode($fields);

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic' ) );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string );
		$content = curl_exec( $ch );

		if( curl_errno( $ch ) ){
			echo 'Request Error:' . curl_error( $ch );
			exit();
		}

		curl_close( $ch );
		return $content;
	}

	function get_customer_key() {
		$url 	= get_option('mo_customer_validation_host_name') . "/moas/rest/customer/key";
		$ch 	= curl_init( $url );
		$email 	= get_option("mo_customer_validation_admin_email");

		$password = get_option("mo_customer_validation_admin_password");

		$fields = array(
			'email' 	=> $email,
			'password' 	=> $password
		);
		$field_string = json_encode( $fields );

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic' ) );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		$content = curl_exec( $ch );
		if( curl_errno( $ch ) ){
			echo 'Request Error:' . curl_error( $ch );
			exit();
		}
		curl_close( $ch );

		return $content;
	}

	function check_customer() {
			$url 	= get_option('mo_customer_validation_host_name') . "/moas/rest/customer/check-if-exists";
			$ch 	= curl_init( $url );
			$email 	= get_option("mo_customer_validation_admin_email");

			$fields = array(
				'email' 	=> $email,
			);
			$field_string = json_encode( $fields );

			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $ch, CURLOPT_ENCODING, "" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

			curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic' ) );
			curl_setopt( $ch, CURLOPT_POST, true);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
			$content = curl_exec( $ch );
			if( curl_errno( $ch ) ){
				echo 'Request Error:' . curl_error( $ch );
				exit();
			}
			curl_close( $ch );

			return $content;
	}

	function send_otp_token($authType,$email='',$phone=''){

			global $wpdb;
			$otp_sms   = rand('100000' , '999999');

			$phone = preg_replace('/\D/','',$phone);
			$phone = substr($phone, -10);

			$username = get_option('iletimerkezi_username');
			$password = get_option('iletimerkezi_password');
			$sender   = get_option('iletimerkzi_sender');
			 
			//$url www= get_option('mo_customer_validation_host_name') . '/moas/api/auth/challenge';

			//$ch = curl_init($url);
			/*
			if($this->mo_check_empty_or_null(get_option('mo_customer_validation_admin_customer_key')))
				$customerKey =  $this->defaultCustomerKey;
			else
				$customerKey = get_option('mo_customer_validation_admin_customer_key');
			if($this->mo_check_empty_or_null(get_option('mo_customer_validation_admin_api_key')))
				$apiKey =  $this->defaultApiKey;
			else
				$apiKey =  get_option('mo_customer_validation_admin_api_key');
			*/
			//$username = get_option('mo_customer_validation_admin_email');
			//$phone = get_option('mo_customer_validation_admin_phone');
			
			/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
			/*$currentTimeInMillis = round(microtime(true) * 1000); */
			

			/* Creating the Hash using SHA-512 algorithm */
			/*$stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
			
			$hashValue = hash("sha512", $stringToHash);

			$customerKeyHeader = "Customer-Key: " . $customerKey;
			$timestampHeader = "Timestamp: " . $currentTimeInMillis;
			$authorizationHeader = "Authorization: " . $hashValue;
			if($authType == 'EMAIL') {
				$fields = array(
				'customerKey' => $customerKey,
				'email' => $email,
				'authType' => 'EMAIL',
				'transactionName' => 'WordPress miniOrange OTP Verification'
				);
			}else if($authType == 'SMS'){
				$fields = array(
				'customerKey' => $customerKey,
				'phone' => $phone,
				'authType' => 'SMS',
				'transactionName' => 'WordPress miniOrange OTP Verification'
			);
			}*/
			
			$xml = <<<EOS
					<request>
				        <authentication>
				            <username>{$username}</username>
				            <password>{$password}</password>
				        </authentication>
				        <order>
				            <sender>{$sender}</sender>
				            <sendDateTime></sendDateTime>
				            <message>
				            	<text><![CDATA[Tek kullanımlık şifre : {$otp_sms}]]></text>
				                <receipents>
				                	<number>{$phone}</number>
				                </receipents>
				            </message>
				        </order>
					</request>
EOS;

					$ch = curl_init();
			        curl_setopt($ch, CURLOPT_URL,'http://api.iletimerkezi.com/v1/send-sms');
			        curl_setopt($ch, CURLOPT_POST, 1);
			        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
			        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
			        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
			        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
			        curl_setopt($ch, CURLOPT_HEADER, 0);
			        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			        curl_setopt($ch,CURLOPT_VERBOSE, FALSE);
			        $result = curl_exec($ch);
			        //die(var_dump($result));
				preg_match_all('|\<code\>.*\<\/code\>|U', $result, $matches,PREG_PATTERN_ORDER);
				if(isset($matches[0])&&isset($matches[0][0])) {
		        		
		        
			        if(strcasecmp($matches[0][0], '<code>200</code>') == 0){
			        	preg_match_all('|\<id\>.*\<\/id\>|U', $result, $reportid,PREG_PATTERN_ORDER);
			        	
			        	$reportid = preg_replace('/\D/','',$reportid[0][0]);
			        	$table_name = $wpdb->prefix.'iletimerkeziotp';
			        	$sql = $wpdb->insert( 
								$table_name, 
								array( 
									'Telephone' => $phone, 
									'reportid' => $reportid,
									'reportstatus' => 'Gönderiliyor',
									'otp'	=> $otp_sms 
								), 
								array( 
									'%s', 
									'%d',
									'%s',
									'%d', 
								) 
							);
			        	
			        }
			        return $matches[0][0];
		        }
			return '';
		}

		function validate_otp_token($phone_number,$otpToken){
					
			global $wpdb;

			$number = preg_replace('/\D/','',$phone_number);
			$number = substr($number, -10);

			$table_name = $wpdb->prefix.'iletimerkeziotp';
			$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE telephone = $number ORDER BY id DESC LIMIT 4");
			
			$lastsenddate = explode( ' ' , $results[0]->date);
			
			if ($results[3]) {
				$senddate = explode(' ', $results[3]->date);
				$lastsendtime = explode(':', $lastsenddate[1]);
				$lastsendtime = $lastsendtime[0].$lastsendtime[1].$lastsendtime[2] ;
				$sendtime = explode(':', $senddate[1]);
				$sendtime = $sendtime[0].$sendtime[1].$sendtime[2];
				if (($lastsenddate[0] == $senddate[0]) && ($lastsendtime - $sendtime < '500')) {
					
					return array('Arka arkaya çok fazla deneme yaptınız kayıt olma geçici olarak devre dışı kalmıştır.','warning');
				}
			}
			
			if ($otpToken == $results[0]->otp) {
				
				return array('success','success');
			}
			
			return array('Telefon doğrulama kodunu yanlış girdiniz lütfen tekrar deneyin.' , 'fail');
		/*	$url = get_option('mo_customer_validation_host_name') . '/moas/api/auth/validate';
			$ch = curl_init($url);

			if($this->mo_check_empty_or_null(get_option('mo_customer_validation_admin_customer_key')))
				$customerKey =  $this->defaultCustomerKey;
			else
				$customerKey = get_option('mo_customer_validation_admin_customer_key');
			if($this->mo_check_empty_or_null(get_option('mo_customer_validation_admin_api_key')))
				$apiKey =  $this->defaultApiKey;
			else
				$apiKey =  get_option('mo_customer_validation_admin_api_key');

			$username = get_option('mo_customer_validation_admin_email'); */

			/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
			//$currentTimeInMillis = round(microtime(true) * 1000);

			/* Creating the Hash using SHA-512 algorithm */
			/*$stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
			$hashValue = hash("sha512", $stringToHash);

			$customerKeyHeader = "Customer-Key: " . $customerKey;
			$timestampHeader = "Timestamp: " . $currentTimeInMillis;
			$authorizationHeader = "Authorization: " . $hashValue;

			$fields = '';

				//*check for otp over sms/email
				$fields = array(
					'txId' => $transactionId,
					'token' => $otpToken,
				);

			$field_string = json_encode($fields);

			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $ch, CURLOPT_ENCODING, "" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

			curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
												$timestampHeader, $authorizationHeader));
			curl_setopt( $ch, CURLOPT_POST, true);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
			$content = curl_exec($ch);

			if(curl_errno($ch)){
				echo 'Request Error:' . curl_error($ch);
			   exit();
			}
			curl_close($ch);
			return $content; */
	}

	public function get_balance(){

		$username = get_option('iletimerkezi_username');
		$password = get_option('iletimerkezi_password');
		$xml = <<<EOS
		<request>
	        <authentication>
	            <username>{$username}</username>
	            <password>{$password}</password>
	        </authentication>
		</request>
EOS;

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'http://api.iletimerkezi.com/v1/get-balance');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch,CURLOPT_VERBOSE, FALSE);
        $result = curl_exec($ch);
        //die(var_dump($result));
        preg_match_all('|\<code\>.*\<\/code\>|U', $result, $matches,PREG_PATTERN_ORDER);
        
        if ($matches[0][0] == '<code>200</code>') {
        	
        	preg_match_all('|\<sms\>.*\<\/sms\>|U', $result, $matches,PREG_PATTERN_ORDER);

			return array($matches[0][0], true);
        }else{
        	
			preg_match_all('|\<message\>.*\<\/message\>|U', $result, $matches,PREG_PATTERN_ORDER);

			return array($matches[0][0] , false);
		}
	}

	function submit_contact_us( $email, $phone, $query ) {
			global $current_user;
			get_currentuserinfo();
			$query = '[WP OTP Verification Plugin] ' . $query;
			$fields = array(
				'firstName'			=> $current_user->user_firstname,
				'lastName'	 		=> $current_user->user_lastname,
				'company' 			=> $_SERVER['SERVER_NAME'],
				'email' 			=> $email,
				'phone'				=> $phone,
				'query'				=> $query
			);
			$field_string = json_encode( $fields );

			$url = get_option('mo_customer_validation_host_name') . '/moas/rest/customer/contact-us';

			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $ch, CURLOPT_ENCODING, "" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

			curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF-8', 'Authorization: Basic' ) );
			curl_setopt( $ch, CURLOPT_POST, true);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
			$content = curl_exec( $ch );

			if( curl_errno( $ch ) ){
				echo 'Request Error:' . curl_error( $ch );
				return false;
			}
			

			curl_close( $ch );

			return true;
	}
	
	function forgot_password($email){
		
		$url = get_option('mo_customer_validation_host_name') . '/moas/rest/customer/password-reset';
		$ch = curl_init($url);
		
		$fields = array(
			'email' => $email
		);
		
		$field_string = json_encode($fields);
		
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
		
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic' ) );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt( $ch, CURLOPT_TIMEOUT, 20);
		$content = curl_exec($ch);
		
		if(curl_errno($ch)){
			return null;
		}
		curl_close($ch);
		return $content;
	}


	function check_customer_ln(){
		
		$url = get_option('mo_customer_validation_host_name') . '/moas/rest/customer/license';
		$ch = curl_init($url);
		
		/* The customer Key provided to you */
		$customerKey = get_option('mo_customer_validation_admin_customer_key');
	
		/* The customer API Key provided to you */
		$apiKey = get_option('mo_customer_validation_admin_api_key');
	
		/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
		$currentTimeInMillis = round(microtime(true) * 1000);
	
		/* Creating the Hash using SHA-512 algorithm */
		$stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
		$hashValue = hash("sha512", $stringToHash);
	
		$customerKeyHeader = "Customer-Key: " . $customerKey;
		$timestampHeader = "Timestamp: " . $currentTimeInMillis;
		$authorizationHeader = "Authorization: " . $hashValue;
		
		$fields = '';
	
			//*check for otp over sms/email
			$fields = array(
				'customerId' => $customerKey,
				'applicationName' => 'wp_otp_verification'
			);
		
		$field_string = json_encode($fields);
		
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
		
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader, 
											$timestampHeader, $authorizationHeader));
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt( $ch, CURLOPT_TIMEOUT, 20);
		$content = curl_exec($ch);
		
		if(curl_errno($ch)){
			return null;
		}
		curl_close($ch);
		return $content;
	}
}
?>