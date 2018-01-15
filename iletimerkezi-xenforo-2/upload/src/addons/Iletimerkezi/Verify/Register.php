<?php
namespace Iletimerkezi\Verify;


class Register extends XFCP_Register {

    public function actionVerify()
    {
        $vcode   = rand(100000, 999999);
        $session = $this->app()->session();
        $session->set('vcode', $vcode);

        $text = \XF::phrase('iletimerkezi_message_text');
        $text = str_replace('%s', $vcode, $text);
        $res  = $this->sendSMS($_POST['gsm_number'], $text);

        if($res) {
            $session->save();
            die('success');
        } else {
            die('fail');
        }
    }

    public function actionRegister()
    {
        $session = $this->app()->session();
        $is_verified = $this->isVerified();
        if(!$is_verified) {
            return $this->error(\XF::phrase('iletimerkezi_verify_error'));
        }

        $result = parent::actionRegister();
        if(get_class($result) == 'XF\Mvc\Reply\Error') {
            return $result;
        } else {

            error_log('IS Verified :: '.var_export($is_verified,1));
            if($is_verified) {
                error_log('1 - IS Verified :: '.var_export($this->isVerified(),1));
                $options = \XF::options();
                $UGC     = $this->app()->service('XF:User\UserGroupChange');
                $UGC->addUserGroupChange($session->get('userId'), rand(100000, 999999), [$options->iletimerkezi_verify_group]);
                error_log('2 - IS Verified :: '.var_export($this->isVerified(),1));
            }

            return $result;
        }
    }

    private function isVerified()
    {

        $session = $this->app()->session();
        $vcode   = $session->get('vcode');

        if(is_null($vcode) || empty($vcode))
            return false;

        if(is_null($_POST['vcode']) || empty($_POST['vcode']))
            return false;

        if(strlen($_POST['vcode']) != 6 || strlen($vcode) != 6)
            return false;

        if($_POST['vcode'] == $vcode) {
            return true;
        } else {
            return false;
        }

    }

    private function sendSMS($to, $msg)
    {

        $options  = \XF::options();
        $username = $options->iletimerkezi_username;
        $password = $options->iletimerkezi_password;
        $sender   = $options->iletimerkezi_sender;

        $xml = '
        <request>
             <authentication>
                 <username>'.$username.'</username>
                 <password>'.$password.'</password>
             </authentication>

             <order>
                 <sender>'.$sender.'</sender>
                 <sendDateTime></sendDateTime>
                 <message>
                     <text><![CDATA['.$msg.']]></text>
                     <receipents>
                         <number>'.$to.'</number>
                     </receipents>
                 </message>
             </order>
         </request>
        ';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.iletimerkezi.com/v1/send-sms');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/xml']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);
        error_log('XML ::: '.$xml);
        // error_log('RESULT ::: '.var_export($result, 1));

        preg_match_all('|\<code\>.*\<\/code\>|U', $result, $matches, PREG_PATTERN_ORDER);
        if(isset($matches[0]) && isset($matches[0][0])) {
            if( $matches[0][0] == '<code>200</code>' ) {
                return true;
            }
        }

        return false;
    }
}