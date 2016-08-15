<?php

class Emarka_Storesms_Block_Adminhtml_Storesms_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('storesms_form', array('legend'=>Mage::helper('storesms')->__('TOPLU SMS GÖNDERİMİ')));

        $customer_groups = Mage::helper('customer')->getGroups()->toOptionArray();
        
        array_unshift($customer_groups,  array( 'value' => '-1', 'label' => 'Tüm Gruplar', ));
        //die(var_export($customer_groups));
        $fieldset->addField('sms_customer_group', 'select', array(
            'name'  => 'sms_customer_group',
            'label' => Mage::helper('storesms')->__('Müşteri Grupları'),
            'title' => Mage::helper('storesms')->__('Müşteri Grupları'),
            'required' => true,
            'values' => $customer_groups,
        )); 

        $fieldset->addField('sms_message', 'editor', array(
            'name'      => 'sms_message',
            'label'     => Mage::helper('storesms')->__('Mesaj'),
            'title'     => Mage::helper('storesms')->__('Mesaj'),
            'style'     => 'width:500px; height:15em;',
            'after_element_html' => Mage::helper('storesms')->__('160 Karakterden fazlası mesaj sayısını artırır.'),
            'wysiwyg'   => false,
            'required'  => true,
            'class'       => 'validate-length maximum-length-160'
        ));

        $fieldset->addField('note2', 'note', array(
            'text'     => Mage::helper('storesms')->__('Mesajin içinde %firstname% %lastname% degiskenlerini kullanabilirsiniz.')
        ));
        return parent::_prepareForm();
    }
}