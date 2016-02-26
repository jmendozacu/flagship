<?php
$installer = $this;


$installer->startSetup();

$installer->run('

ALTER TABLE `'.$this->getTable('inventorymanager_vendor_productdetail').'` ADD COLUMN `main_image` varchar(100) NOT NULL default "";


');

$installer->endSetup();