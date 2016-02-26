<?php

$installer = $this;

$installer->startSetup();

$installer->run("


-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_vendor_deleted_location')};
CREATE TABLE {$this->getTable('inventorymanager_vendor_deleted_location')} (
  `deleted_location_id` int(11) unsigned NOT NULL auto_increment,
  `location` varchar(255) NOT NULL default '',
  `vendor_id` int(11)  NOT NULL default 0,
  PRIMARY KEY (`deleted_location_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;


");

$installer->endSetup(); 