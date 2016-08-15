<?php

class Emarka_Storesms_Block_Adminhtml_Storesms_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'storesms';
        $this->_controller = 'adminhtml_storesms';

        $this->_updateButton('save', 'label', Mage::helper('storesms')->__('Gönder'));
    }

    public function getHeaderText()
    {
        return Mage::helper('storesms')->__('Toplu SMS Gönderimi');
    }
}