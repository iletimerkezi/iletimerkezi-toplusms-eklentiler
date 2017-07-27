<?php

class Emarka_Storesms_Block_Helper_Sms extends Mage_Adminhtml_Block_System_Config_Form_Field
{

protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
	$this->getDomain();
    return $this->getBalance();
}

    public function getDomain(){
        $domain = $_SERVER['HTTP_HOST'];
        $config = Mage::getModel('storesms/config');
        $iletimerkezi_username = $config->getLogin();
        $iletimerkezi_password = $config->getPassword();
        $balance_xml = '<?xml version="1.0" encoding="UTF-8" ?>
            <request>
                <authentication>
                    <username>'.$iletimerkezi_username.'</username>
                    <password>'.$iletimerkezi_password.'</password>
                </authentication>
                 <pluginUser>
                        <site><![CDATA['.$domain.']]></site>
                        <name>magento</name>
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

    public function getBalance(){
         $config = Mage::getModel('storesms/config');
        $iletimerkezi_username = $config->getLogin();
        $iletimerkezi_password = $config->getPassword();
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

        preg_match_all('|\<sms\>.*\<\/sms\>|U', $result, $matches,PREG_PATTERN_ORDER);

        if(isset($matches[0])&&isset($matches[0][0])) {
            $result = '<b>'.$matches[0][0].'</b> <a style=" background: none;
    border: none;color: red;display: inline;margin: 0;padding: 0 10px;text-decoration: none;" href="https://www.iletimerkezi.com/index.php?function=default&obj1=signinViaGet&gsm='.$iletimerkezi_username.'&password='.$iletimerkezi_password.'"> SMS Satın Al</a>';
            return $result;
        }

        return 'Kullanıcı adı ya da şifreniz hatalı olabilir.';
    }
}