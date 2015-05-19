<?php
$installer = $this;


$installer->startSetup();

$installer->run('

ALTER TABLE `'.$this->getTable('inventorymanager_purchase_order').'` ADD COLUMN `status` varchar(50) NOT NULL default 0;


');

$installer->endSetup();