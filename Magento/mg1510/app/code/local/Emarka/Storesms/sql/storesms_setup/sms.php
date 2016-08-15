<?php
class Sms {

	public function send($api_username,$api_password,$message,$number,$orginator) {

		$number = preg_replace('/\D/','',$number);
		$number = substr($number, -10);

		$xml = <<<EOS
		<request>
	        <authentication>
	            <username>{$api_username}</username>
	            <password>{$api_password}</password>
	        </authentication>
	        <order>
	            <sender>{$orginator}</sender>
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

	}

    public function sendBulk($api_username,$api_password,$smsmessage,$orginator) {

        $xml = <<<EOS
        <request>
            <authentication>
                <username>{$api_username}</username>
                <password>{$api_password}</password>
            </authentication>
            <order>
                <sender>{$orginator}</sender>
                <sendDateTime></sendDateTime>
                {$smsmessage}
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

    }

	public function getBalance($api_username,$api_password) {


		$xml = <<<EOS
		<request>
			<authentication>
				<username>{$api_username}</username>
				<password>{$api_password}</password>
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

        $result = curl_exec($ch);
        preg_match_all('|\<sms\>.*\<\/sms\>|U', $result, $matches,PREG_PATTERN_ORDER);
        
        if(isset($matches[0])&&isset($matches[0][0])) {
        	return $matches[0][0];	
        }
        
        return '';
	}

	public function getSender($api_username,$api_password) {


		$xml = <<<EOS
		<request>
			<authentication>
				<username>{$api_username}</username>
				<password>{$api_password}</password>
			</authentication>
		</request>
EOS;

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'http://api.iletimerkezi.com/v1/get-sender');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);
        preg_match_all('|\<sender\>.*\<\/sender\>|U', $result, $matches,PREG_PATTERN_ORDER);
        
       // die('hasan'.$api_username.$api_password);

        if(isset($matches[0])&&isset($matches[0][0])) {
        	return $matches;	
        }
        
        return '';
	}

}
?>