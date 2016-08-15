<?php

class Emarka_Storesms_Model_Cron {

    public function getDelieveryReport() {
    	
        $apiClient = Mage::getModel('storesms/apiClient');
        $apiClient->saveDelieveryReport();

    }

}