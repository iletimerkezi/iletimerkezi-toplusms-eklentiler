<?php

class Emarka_Storesms_Block_Adminhtml_Storesms_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('storesms_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('storesms')->__('SMS İçerik'));
  }

  protected function _beforeToHtml()
  {

      $this->addTab('form_section', array(
          'label'     => Mage::helper('storesms')->__('SMS İçerik'),
          'title'     => Mage::helper('storesms')->__('SMS İçerik'),
          'content'   => $this->getLayout()->createBlock('storesms/adminhtml_storesms_edit_tab_form')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}