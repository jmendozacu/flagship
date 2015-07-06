<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_vendor_product_material')};
CREATE TABLE {$this->getTable('inventorymanager_vendor_product_material')} (
  `vendor_id` int(11) NOT NULL default 0,
  `material` varchar(100) NOT NULL default ''
);


-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_vendor_product_lighting')};
CREATE TABLE {$this->getTable('inventorymanager_vendor_product_lighting')} (
  `vendor_id` int(11) NOT NULL default 0,
  `lighting` varchar(100) NOT NULL default ''
);

ALTER TABLE `".$this->getTable('inventorymanager_vendor_productdetail')."` CHANGE `material` `material` varchar(100) NOT NULL default '';
ALTER TABLE `".$this->getTable('inventorymanager_vendor_productdetail')."` CHANGE `lighting` `lighting` varchar(100) NOT NULL default '';

");

$installer->endSetup(); 