<?php

class Emarka_Storesms_Block_Buttons_NewAccount extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){

        $this->setElement($element);

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('button')
                    ->setLabel(Mage::helper('storesms')->__('Üyelik Formu'))
                    ->setOnClick("window.open('https://www.iletimerkezi.com/uye-kayidi/?ref=menu_uyekayit','window1','width=990, height=705, scrollbars=1, resizable=1'); return false;")
                    ->toHtml();

        return $html;

    }
}
