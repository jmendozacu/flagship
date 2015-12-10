<?php
$installer = $this;


$installer->startSetup();

$installer->run('

ALTER TABLE `'.$this->getTable('inventorymanager_purchase_order').'` ADD COLUMN `order_qty` int(11) NOT NULL default 0;
ALTER TABLE `'.$this->getTable('inventorymanager_purchase_order').'` ADD COLUMN `received_qty` int(11) NOT NULL default 0;

');

$installer->endSetup();