<?php

class Emarka_Storesms_Adminhtml_StoresmsController extends Mage_Adminhtml_Controller_Action
{
    /**
    * @desc StoreSMS - Summary menu linki
    */
    public function indexAction() {
        //die('StoreSMS - Summary menu linki');
        $this->loadLayout()->_setActiveMenu('storesms/items');
        $this->_addContent($this->getLayout()->createBlock('storesms/adminhtml_storesms_grid'));
        $this->renderLayout();

    }

    public function editAction() {
        //die('edit action send bulk sms menu');
        $this->loadLayout()->_setActiveMenu('storesms/items');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('storesms/adminhtml_storesms_edit'))
                ->_addLeft($this->getLayout()->createBlock('storesms/adminhtml_storesms_edit_tabs'));

        $this->renderLayout();

    }

    public function newAction() {
        //die('HOP3');
        $this->_forward('edit');

    }

    /**
    *   @desc send bulk sms
    * 
    */
    public function saveAction() {
        //die(var_export($this->getRequest()->getParam('sms_customer_group')));
        Mage::getModel('storesms/storesms')->sendBulkSMS(
            $this->getRequest()->getParam('sms_customer_group'),
            $this->getRequest()->getParam('sms_message')
        );
        
        $this->_redirect('*/*/new');

    }


}