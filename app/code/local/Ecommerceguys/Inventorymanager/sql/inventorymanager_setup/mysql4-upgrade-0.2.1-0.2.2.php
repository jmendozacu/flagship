<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_purchaseorder_label_status')};
CREATE TABLE {$this->getTable('inventorymanager_purchaseorder_label_status')} (
  `vendor_id` int(11) NOT NULL default 0,
  `status` varchar(100) NOT NULL default ''
);


    ");

$installer->endSetup(); 