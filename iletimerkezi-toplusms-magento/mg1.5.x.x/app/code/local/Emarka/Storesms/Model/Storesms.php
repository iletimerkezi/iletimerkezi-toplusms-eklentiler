<?php

class Emarka_Storesms_Model_Storesms extends Mage_Core_Model_Abstract {


    public function _construct() {
        // die('HOP');
        $this->_init("storesms/storesms");
    }

    /**
     * Get all phone numbers from customer address collection
     *
     *
     * @return type
     */

    public function getPhoneNumbers($group_id = -1) {
        //die(var_export($group_id));

        $phones = array();
        //tüm gruplar değilse
        if($group_id != -1){ //send customer group
            $collection = Mage::getModel('customer/customer')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('group_id', $group_id);
            //die(var_export($collection));
            foreach ($collection as $customer_data) {
                //die(var_export($customer_data->getData()));
                $customer = $customer_data->getData();
                $address = Mage::getModel('customer/address')->load($customer['entity_id']);
                $phones[] = array(
                    'firstname' => $customer['firstname'],
                    'lastname'  => $customer['lastname'],
                    'telephone' => Mage::helper('storesms')->getPhoneNumber($address->getTelephone())
                );
            }

        } else { // send all customer groups
            $col = Mage::getModel('customer/address')->getCollection()->addAttributeToSelect('*')->getItems();
            // die("<pre>".var_export($col)."</pre>");
            foreach ($col as $address) {
                //die("<pre>".var_export($address->getData())."</pre>");
                $customer = $address->getData();
                $phones[] = array(
                    'firstname' => $customer['firstname'],
                    'lastname'  => $customer['lastname'],
                    'telephone' => Mage::helper('storesms')->getPhoneNumber($address->getTelephone())
                );
            }
            //die(var_export($phones));
        }

        //$phones = array_unique($phones);
        //die(var_export($phones));
        return $phones;

    }

    public function sendBulkSMS($customer_group, $message) {

        $ApiClient = Mage::getModel('storesms/apiClient');
        //die(var_export($customer_group));
        //die(var_export($message));
        $customers = $this->getPhoneNumbers($customer_group);
        //toplu olarak musterilere gidicek mesaji yolla
        //die(var_export($customers));
        try {

            if (empty($customers))
                throw new Exception (Mage::helper('storesms')->__('Müşteri oluşturulmalı!'));

            foreach ($customers as $customer) {

                if(!empty($customer['telephone'])){

                    if(isset($message) && !empty($message)){
                        $val = array('%firstname%', '%lastname%');
                        $change = array($customer['firstname'], $customer['lastname']);
                        $message_temp = str_replace($val, $change, $message);
                        //die(var_export($message));
                        $ApiClient->sendSms($customer['telephone'],$message_temp);
                    }
                }

                unset($val);
                unset($change);
            }

            //magento warning message
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storesms')->__('Mesaj gönderimi başarılı.'));
        } catch (Exception $e) {
            //magento warning message
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

    }

    function getSmsReports($cur_page=1) {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        if(empty($cur_page))
            $cur_page = 1;

        $start_point = ($cur_page-1)*10;
//LIMIT ".$start_point.",10"
        $results = $connection->fetchAll("SELECT * FROM storesms ");
        //die(var_export($results));
        return $results;
    }

}