<?php
$installer = $this;


$installer->startSetup();

$installer->run("



-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_vendorproduct')};
CREATE TABLE {$this->getTable('inventorymanager_vendorproduct')} (
  `product_id` int(11) unsigned NOT NULL default 0,
  `vendor_id` int(11) unsigned NOT NULL default 0
);


");

$installer->endSetup();