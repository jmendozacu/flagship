<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE `".$this->getTable('inventorymanager_vendor_productdetail')."` ADD COLUMN `box_weight` float(11,2) NULL DEFAULT NULL;
");
$installer->endSetup();