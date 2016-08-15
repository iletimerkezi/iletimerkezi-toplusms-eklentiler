<?php

class Emarka_Storesms_Helper_Data extends Mage_Core_Helper_Abstract
{



    public function  getPhoneNumber($phoneNumber) {

        $config = Mage::getModel('storesms/config');
        $prefix = $config->getCountryPrefix();

        if ($prefix) {
            $toStrip = '+,'. $prefix .',0';
        }
        else {
            $toStrip = '+,0';
        }


        return $prefix . ltrim($phoneNumber,$toStrip);

    }


    public function getStatusVerbally($responseErrorNumber) {

        $responseErrorNumber = ($responseErrorNumber>0) ? 1:$responseErrorNumber;

        $statuses = array(
            '1'     =>'SEND_OK',
            '-1'    =>'AUTH_FAILED',
            '-2'    =>'XML_ERROR',
            '-3'    =>'NOT_ENOUGH_CREDITS',
            '-4'    =>'NO_RECIPIENTS',
            '-5'    =>'GENERAL_ERROR'
        );

        return $statuses[$responseErrorNumber];

    }

}
