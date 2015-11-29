<?php

$installer = $this;

$installer->startSetup();

$installer->run("


-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_shipmanager_history')};
CREATE TABLE {$this->getTable('inventorymanager_shipmanager_history')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `transaction_detail` varchar(255) NOT NULL default '',
  `job_id` varchar(255) NOT NULL default '',
  `careercode` varchar(255) NOT NULL default '',
  `tracking_number` varchar(255) NOT NULL default '',
  `service_type` varchar(255) NOT NULL default '',
  `weight` varchar(255) NOT NULL default '',
  `order_id` varchar(255) NOT NULL default '',
  `quoted_value` varchar(255) NOT NULL default '',
  `shipping_date` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_shipmanager_history_item')};
CREATE TABLE {$this->getTable('inventorymanager_shipmanager_history_item')} (
  `item_id` int(11) unsigned NOT NULL auto_increment,
  `history_id` int(11) NOT NULL default 0,
  `serial` varchar(255) NOT NULL default '',
  `width` varchar(255) NOT NULL default '',
  `height` varchar(255) NOT NULL default '',
  `length` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_shipmanager_history_senderaddress')};
CREATE TABLE {$this->getTable('inventorymanager_shipmanager_history_senderaddress')} (
  `address_id` int(11) unsigned NOT NULL auto_increment,
  `history_id` int(11) NOT NULL default 0,
  `company` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `contact_name` varchar(255) NOT NULL default '',
  `address1` varchar(255) NOT NULL default '',
  `address2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `postcode` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_shipmanager_history_receiveraddress')};
CREATE TABLE {$this->getTable('inventorymanager_shipmanager_history_receiveraddress')} (
  `address_id` int(11) unsigned NOT NULL auto_increment,
  `history_id` int(11) NOT NULL default 0,
  `company` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `contact_name` varchar(255) NOT NULL default '',
  `address1` varchar(255) NOT NULL default '',
  `address2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `postcode` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;




");

$installer->endSetup(); 