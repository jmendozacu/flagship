<?php
$installer = $this;
$installer->startSetup();
$installer->run('
	ALTER TABLE `'.$this->getTable('inventorymanager_vendor_productdetail').'` ADD COLUMN `box_height` varchar(255) NOT NULL default "";
	ALTER TABLE `'.$this->getTable('inventorymanager_vendor_productdetail').'` ADD COLUMN `box_width` varchar(255) NOT NULL default "";
	ALTER TABLE `'.$this->getTable('inventorymanager_vendor_productdetail').'` ADD COLUMN `box_length` varchar(255) NOT NULL default "";
');
$installer->endSetup();