<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_purchase_order')};
CREATE TABLE {$this->getTable('inventorymanager_purchase_order')} (
  `po_id` int(11) unsigned NOT NULL auto_increment,
  `vendor_id` int(11) unsigned NOT NULL default 0,
  `shipping_method` varchar(100) NOT NULL default '',
  `payment_terms` int(11) NOT NULL default 0,
  `po_notes`	text NULL,
  `date_of_po` datetime NULL,
  `expected_date` datetime NULL,
  PRIMARY KEY (`po_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_product')};
CREATE TABLE {$this->getTable('inventorymanager_product')} (
  `product_id` int(11) unsigned NOT NULL auto_increment,
  `po_id` int(11) unsigned NOT NULL default 0,
  `price` float(13,2) NOT NULL default 0,
  `qty` int(11) NOT NULL default 0,
  `tax`	float(13,2) NOT NULL default 0,
  `total` float(13,2) NOT NULL default 0,
  PRIMARY KEY (`product_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 