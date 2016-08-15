<?php
//File:  app/code/local/eMarka/Storesms/Model/Config.php

/**
* Storesms API config class
*
*
 * @category   eMarka
 * @package    StoresmsApi
 * @copyright  Copyright (c) 2014 eMarka (https://www.iletimerkezi.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Adem ARAS <adem@emarka.com.tr>
* ...
*/

class Emarka_Storesms_Model_Config {

    const LOW_CREDITS_WARNING_MESSAGE = 'Düşük kredi, kredi satın almalısınız.';
    const API_HOST = 'api.iletimerkezi.com';
    const WRONG_AUTH_DATA = 'Yanlış kullanıcı adı / Şifre' ;

    public $contacts = array(
        'en_US'=>'http://www.iletimerkezi.com/iletisim',//Turkey
        'tr_TR'=>'http://www.iletimerkezi.com/iletisim'//Turkey
        );



    public function getContactUrl($localeCode) {

        return (array_key_exists($localeCode, $this->contacts)) ? $this->contacts[$localeCode] : $this->contacts['en_US'];

    }



    /**
     * getting API login from main configuration
     * @return string
     */
    public function getLogin() {
        return Mage::getStoreConfig('storesms/main_conf/apilogin');
    }


    /**
     * getting API password from main configuration
     * @return string
     */
    public function getPassword() {
        $encrypted_pass = Mage::getStoreConfig('storesms/main_conf/apipassword');
        return Mage::helper('core')->decrypt($encrypted_pass);
    }


    /**
     * getting message sender from main configuration
     * @return string
     */
    public function getSender() {
        return Mage::getStoreConfig('storesms/main_conf/sender');
    }

    public function isPro() {
        return Mage::getStoreConfig('storesms/main_conf/sender_active');
    }

    /**
    * Checks if allowed only single message
    * @return int
    */
    public function isSingle() {
        $confRule = Mage::getStoreConfig('storesms/main_conf/allow_long_sms');

        return ($confRule == 1) ? 0:1;
    }

    public function getCountryPrefix() {
        return Mage::getStoreConfig('storesms/main_conf/country_prefix');
    }

    public function getAdminGsm() {
        return Mage::getStoreConfig('storesms/main_conf/storename');
    }

     /**
     * checks if Storesms API module is enabled
     * @return boolean
     */
    public function isApiEnabled() {
        return (Mage::getStoreConfig('storesms/main_conf/active')==0) ? 0:1;
    }

    public function creditAllertLimit() {
        return floatval(str_replace(',','.',Mage::getStoreConfig('storesms/main_conf/credit_alert_limit')));
    }

     /**
     * getting SMS templates from config
     * @return string
     */
    public function getMessageTemplate($template) {

        $templateContent = Mage::getStoreConfig('storesms/templates/status_'.$template);

        if (Mage::getStoreConfig('storesms/templates/status_'. $template .'_active') && !empty($templateContent))
            return $templateContent;

    }

    public function getMessageStatuses() {
       $statuses = array(   'SEND_OK'       =>'SEND_OK',
                            'AUTH_FAILED'   =>'AUTH_FAILED',
                            'XML_ERROR'     =>'XML_ERROR',
                            'NOT_ENOUGH_CREDITS'    =>'NOT_ENOUGH_CREDITS',
                            'NO_RECIPIENTS' =>'NO_RECIPIENTS',
                            'GENERAL_ERROR' =>'GENERAL_ERROR',
                            'WAITING_FOR_DR' =>'WAITING_FOR_DR',
                            'NOT_SENT'=>'NOT_SENT',
                            'SENT'=>'SENT',
                            'NOT_DELIVERED'=>'NOT_DELIVERED',
                            'DELIVERED'=>'DELIVERED',
                            'NOT_ALLOWED'=>'NOT_ALLOWED',
                            'INVALID_DESTINATION_ADDRESS'=>'INVALID_DESTINATION_ADDRESS',
                            'INVALID_SOURCE_ADDRESS'=>'INVALID_SOURCE_ADDRESS',
                            'ROUTE_NOT_AVAILABLE'=>'ROUTE_NOT_AVAILABLE',
                            'NOT_ENOUGH_CREDITS'=>'NOT_ENOUGH_CREDITS',
                            'INVALID_MESSAGE_FORMAT'=>'INVALID_MESSAGE_FORMAT');

       return $statuses;
   }

}