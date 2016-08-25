<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()->newTable($installer->getTable('storesms'))
      ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
          'identity'  => true,
          'unsigned'  => true,
          'nullable'  => false,
          'primary'   => true
        ), 'SMS ID')
      ->addColumn('number', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
          'nullable'  => false,
        ), 'Telephone')
      ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, 1000, array(
          'nullable'  => false,
        ), 'Message')
      ->addColumn('response', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
          'nullable'  => true,
        ), 'Response Api')
      ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 55, array(
          'nullable'  => true,
        ), 'Status')
      ->addColumn('created', Varien_Db_Ddl_Table::TYPE_TIMESTAMP , null, array(
          'nullable'  => false,
        ), 'First send date')
      ->setComment('Storesms API SMSes');
$installer->getConnection()->createTable($table);
$installer->endSetup();