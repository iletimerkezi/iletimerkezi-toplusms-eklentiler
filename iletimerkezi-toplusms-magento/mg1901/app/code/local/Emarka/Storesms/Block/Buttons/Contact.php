<?php

class Emarka_Storesms_Block_Buttons_Contact extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){

        $locale = Mage::app()->getLocale()->getLocaleCode();
        $contactUrl = Mage::getModel('storesms/config')->getContactUrl($locale);

        $this->setElement($element);

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('button')
                    ->setLabel(Mage::helper('storesms')->__('İletişim Formu'))
                    ->setOnClick("window.open('{$contactUrl}','window1','width=990, height=705, scrollbars=1, resizable=1'); return false;")
                    ->toHtml();

        return $html;

    }
}
