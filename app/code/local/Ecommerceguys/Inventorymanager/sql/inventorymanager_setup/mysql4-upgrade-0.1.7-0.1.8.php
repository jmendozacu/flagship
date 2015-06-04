<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_vendor_productdetail')};
CREATE TABLE {$this->getTable('inventorymanager_vendor_productdetail')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default 0,
  `vendor_id` int(11) unsigned NOT NULL default 0,
  `description` text NOT NULL default '',
  `cost` float(13,2) NOT NULL default 0,
  `length` varchar(255) NOT NULL default '',
  `width` varchar(255) NOT NULL default '',
  `height` varchar(255) NOT NULL default '',
  `fun_spec` varchar(255) NOT NULL default '',
  `material` smallint(6) NOT NULL default 0,
  `lighting` smallint(6) NOT NULL default 0,
  `created_time` datetime NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 