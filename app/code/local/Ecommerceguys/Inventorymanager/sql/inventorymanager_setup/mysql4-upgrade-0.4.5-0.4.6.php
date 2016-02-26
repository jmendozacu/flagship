<?php

$installer = $this;

$installer->startSetup();

$installer->run("


-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_vendor_employee')};
CREATE TABLE {$this->getTable('inventorymanager_vendor_employee')} (
  `employee_id` int(11) unsigned NOT NULL auto_increment,
  `logo` varchar(255) NOT NULL default '',
  `photo` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `username` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `parent_id` int(11)  NOT NULL default 0,
  `barcode_id` varchar(255)  NOT NULL default '',
  `employee_display_id` varchar(255)  NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`employee_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;


");

$installer->endSetup(); 