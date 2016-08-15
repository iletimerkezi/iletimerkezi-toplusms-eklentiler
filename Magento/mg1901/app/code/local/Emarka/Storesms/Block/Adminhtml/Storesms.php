<?php

class Emarka_Storesms_Block_Adminhtml_Storesms extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
    	// die('BLOCK');
        $this->_controller = 'adminhtml_storesms';
        $this->_blockGroup = 'storesms';
        $this->_headerText = Mage::helper('storesms')->__('StoreSMS');
        parent::__construct();
        $this->removeButton('add');

       
    }

}