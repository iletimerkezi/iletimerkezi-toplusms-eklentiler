<?php

$this->startSetup();

$this->run("

CREATE TABLE IF NOT EXISTS `storesms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'SMS ID',
  `number` varchar(50) NOT NULL COMMENT 'Telephone',
  `message` text NOT NULL COMMENT 'Message',
  `response` int(11) DEFAULT NULL COMMENT 'Response Api',
  `status` varchar(55) DEFAULT NULL COMMENT 'Response Api',
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'First send date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Storesms API SMSes' ;

");

$this->endSetup();
