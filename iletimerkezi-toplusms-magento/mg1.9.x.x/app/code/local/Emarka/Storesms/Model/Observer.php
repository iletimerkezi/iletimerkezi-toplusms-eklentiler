<?php
//File:  app/code/local/eMarka/Storesms/Model/Observer.php

/**
 * @category   eMarka
 * @package    Storesms API
 * @copyright  Copyright (c) 2014 eMarka (https://www.iletimerkezi.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Adem ARAS <adem@emarka.com.tr>
* ...
*/

/* for DEBUG
$ApiClient->getLogs($customer);

Mage::log("This is a developer log", null, Emarka_SMS.log);
*/
class Emarka_Storesms_Model_Observer {

    /**
    *
    * @param type $observer
    * @return type
    */
    public function orderStatusHistorySave($observer) {

        $config =   Mage::getModel('storesms/config');
        if ($config->isApiEnabled()==0) return; //do nothing if api is disabled

        $history = $observer->getEvent()->getStatusHistory();

        //only for new status
        if (!$history->getId()) {

            $order = $history->getOrder();
            $newStatus =  $order->getData('status');
            $origStatus =  $order->getOrigData('status');


           // if (time()-self::$lastExecutionTime<=2)
                //return;

            //self::$lastExecutionTime = time();

            //if status has changed run action
            if ($newStatus!=$origStatus) {

                $message = $config->getMessageTemplate($newStatus); //get template for new status (if active and exists)
                if (!$message)  //return if no active message template
                return;


                //getting last tracking number
                $tracking = Mage::getResourceModel('sales/order_shipment_track_collection')->setOrderFilter($order)->getData();

                if (!empty($tracking)) {
                    $last = count($tracking)-1;
                    $last_tracking_number = $tracking[$last]['track_number'];
                }
                else
                    $last_tracking_number = 'no_tracking'; //if no tracking number set "no_tracking" message for {TRACKINGNUMBER} template


                //getting order data to generate message template
                $firstname = $order->getShippingAddress()->getData('firstname');
                $lastname = $order->getShippingAddress()->getData('lastname');
                $order_id = $order->getIncrement_id();
                $order_status  = $newStatus;
                $trackingnumber = $last_tracking_number;

                //sending sms and getting API response

                try {

                    $ApiClient = Mage::getModel('storesms/apiClient');

                    $customer_phone = $order->getShippingAddress()->getData('telephone');

                    //Yeni bir musteri siteye kaydolunca musteriye gidicek mesaji yolla
                    if($customer_phone != false){
                       
                        //$message = Mage::getStoreConfig('storesms/templates/new_customer_to_member');
                        if(isset($message) && !empty($message) ){
                            $val = array('%firstname%', '%lastname%', '%orderid%', '%orderstatus%', '%trackingnumber%');
                            $change = array($firstname, $lastname, $order_id, $order_status, $trackingnumber);
                            $message = str_replace($val, $change, $message);
                            $ApiClient->sendSms($customer_phone,$message);
                        }
                    }

                    //@successs add comment to order
                    $newComment = Mage::helper('storesms')->__('SMS Gönderimi');
                    $history->setComment($newComment);
                    //Mage::getSingleton('core/session')->addSuccess($newComment);
                    //$this->checkCreditLimit();

                } catch (Exception $e) {
                    $newComment = Mage::helper('storesms')->__('SMS notification sending error:').' "'.$e->getMessage().'"';
                    $history->setComment($newComment);
                    //Mage::getSingleton('core/session')->addError($newComment);
                }

            }
        }
    }

    /**
    * @desc send new customer sms
    */
    public function hookNewCustomer($observer){

        $ApiClient = Mage::getModel('storesms/apiClient');

        //yönetici modulu kapatmıssa
        $config =   Mage::getModel('storesms/config');
        if ($config->isApiEnabled()==0) return; //do nothing if api is disabled

        $customer = $observer->getCustomer()->getData();

        $customer_phone = false;
        $params = array();//hack yaptım if icin

        if(array_key_exists('phone_mobile', $params['_POST']) ){
            $customer_phone = $params['_POST']['phone_mobile'];

            $val = array('%firstname%', '%lastname%', '%telephone%');
            $change = array($customer['firstname'], $customer['lastname'], $customer_phone);
        } else {

            $val = array('%firstname%', '%lastname%');
            $change = array($customer['firstname'], $customer['lastname']);
        }

        //Yeni bir musteri siteye kaydolunca yoneticiye gidicek mesaji yolla
        $status = Mage::getStoreConfig('storesms/templates/new_customer_active');
        $message = Mage::getStoreConfig('storesms/templates/new_customer');

        if(isset($message) && !empty($message) && $status){
            $phone_mobile = Mage::getStoreConfig('storesms/main_conf/storename');
            
            $message = str_replace($val, $change, $message);
            $ApiClient->sendSms($phone_mobile, $message);
        }

        //Yeni bir musteri siteye kaydolunca musteriye gidicek mesaji yolla
        if($customer_phone != false){
            $status_member = Mage::getStoreConfig('storesms/templates/new_customer_to_member_active');
            $message_member = Mage::getStoreConfig('storesms/templates/new_customer_to_member');
            if(isset($message_member) && !empty($message_member) && $status_member){
                $val = array('%firstname%', '%lastname%', '%telephone%', '%email%', '%password%');
                $change = array($customer['firstname'], $customer['lastname'], $customer_phone, $customer['email'], $customer['password']);
                $message_member = str_replace($val, $change, $message_member);
                $ApiClient->sendSms($customer_phone,$message_member);
            }
        }
    }

    public function hookNewOrder($observer) {

        $ApiClient = Mage::getModel('storesms/apiClient');

        //yönetici modulu kapatmıssa
        $config =   Mage::getModel('storesms/config');
        if ($config->isApiEnabled()==0) return; //do nothing if api is disabled

        $order_data = $observer->getEvent()->getData();
        $order_id = $order_data['order_ids'][0];
        
        $order = Mage::getModel('sales/order')->load($order_id);
        $order_id = $order->getIncrementId();//1000000 gibi
        $products = $order->getAllItems();

        //If they have no customer id, they're a guest.
        if($order->getCustomerId() === NULL) {
            //guest customer
            $customer['firstname'] = $order->getBillingAddress()->getFirstname();
            $customer['lastname'] = $order->getBillingAddress()->getLastname();
        } else { //else, they're a normal registered user.
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            $customer = $customer->getData();
            //$ApiClient->getLogs($customer);
        }

        // bir siparisde birden fazla urun icin dongude
        $product_name      = "";            
        $product_reference = "";            
        $product_quantity  = "";    
        
        foreach ($products as $key => $product) {

            if($key==0)
                $parser = "";
            else
                $parser = ",";

            $product_name .= $parser.$product->getName();          
            $product_reference .= $parser.$product->getSku();      
            $product_quantity .= $parser.$product->getQtyToInvoice();          
        }

        //Yeni bir siparis geldiginde yoneticiye haber ver
        $status = Mage::getStoreConfig('storesms/templates/new_order_active');
        $message = Mage::getStoreConfig('storesms/templates/new_order');
        $phone_mobile = Mage::getStoreConfig('storesms/main_conf/storename');

        if(isset($message) && !empty($message) && $status && !empty($phone_mobile)){

            $val = array('%firstname%', '%lastname%', '%orderid%', '%productname%', '%productmodel%', '%productquantity%');
            $change = array($customer['firstname'], $customer['lastname'], $order_id, $product_name, $product_reference, $product_quantity);
            $message = str_replace($val, $change, $message);
            $ApiClient->sendSms($phone_mobile,$message);
        }

        //Yeni bir siparis geldiginde musteriye gidicek mesaji yolla
        $status_member = Mage::getStoreConfig('storesms/templates/new_order_to_member_active');
        $message_member = Mage::getStoreConfig('storesms/templates/new_order_to_member');
        // adresteki telefon no
        $phone_mobile_member = $order->getShippingAddress()->getTelephone();
        // error_log('Shipping : '.$phone_mobile_member);

        // faturadaki telefon no
        // $phone_mobile_member = $order->getBillingAddress()->getTelephone();
        // error_log('Billing : '.$phone_mobile_member);

        if(isset($message_member) && !empty($message_member) && $status_member && !empty($phone_mobile_member)){
            
            $val = array('%firstname%', '%lastname%', '%orderid%', '%productname%', '%productmodel%', '%productquantity%');
            $change = array($customer['firstname'], $customer['lastname'], $order_id, $product_name, $product_reference, $product_quantity);
            $message_member = str_replace($val, $change, $message_member);
            $ApiClient->sendSms($phone_mobile_member,$message_member);
        }
        
    }

}