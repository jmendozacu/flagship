<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `".$this->getTable('inventorymanager_purchaseorder_label')."` CHANGE `location` `location` varchar(100) NOT NULL default '';

-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_purchaseorder_label_location')};
CREATE TABLE {$this->getTable('inventorymanager_purchaseorder_label_location')} (
  `vendor_id` int(11) NOT NULL default 0,
  `location` varchar(100) NOT NULL default ''
);

");

$installer->endSetup(); 