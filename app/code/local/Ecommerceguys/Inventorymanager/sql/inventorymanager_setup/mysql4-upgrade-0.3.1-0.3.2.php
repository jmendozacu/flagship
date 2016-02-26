<?php
$installer = $this;
$installer->startSetup();
$installer->run('
	ALTER TABLE `'.$this->getTable('inventorymanager_vendor_productdetail').'` ADD COLUMN `upc` varchar(255) NOT NULL default "";
');
$installer->endSetup();