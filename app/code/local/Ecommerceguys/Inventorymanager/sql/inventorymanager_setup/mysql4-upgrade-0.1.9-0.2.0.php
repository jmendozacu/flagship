<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_purchaseorder_label')};
CREATE TABLE {$this->getTable('inventorymanager_purchaseorder_label')} (
  `label_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default 0,
  `order_id` int(11) unsigned NOT NULL default 0,
  `serial` varchar(255) NOT NULL default '',
  `location` int(11) unsigned NOT NULL default 0,
  `created_time` datetime NULL,
  `updated_time` datetime NULL,
  PRIMARY KEY (`label_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_purchaseorder_label_comment')};
CREATE TABLE {$this->getTable('inventorymanager_purchaseorder_label_comment')} (
  `comment_id` int(11) unsigned NOT NULL auto_increment,
  `label_id` int(11) unsigned NOT NULL default 0,
  `comment` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;


    ");

$installer->endSetup(); 