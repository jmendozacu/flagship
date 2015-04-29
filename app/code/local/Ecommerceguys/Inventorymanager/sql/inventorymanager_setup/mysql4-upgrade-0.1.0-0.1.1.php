<?php
$installer = $this;


$installer->startSetup();

$installer->run('

ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `vendor_code` varchar(255) NOT NULL default "";

');

$installer->endSetup();