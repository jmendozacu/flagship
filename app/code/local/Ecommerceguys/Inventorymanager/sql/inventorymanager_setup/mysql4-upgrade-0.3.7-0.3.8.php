<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE `".$this->getTable('inventorymanager_purchase_order')."` ADD COLUMN `is_seen` smallint(2) NULL DEFAULT 0;
	ALTER TABLE `".$this->getTable('inventorymanager_purchaseorder_label')."` ADD COLUMN `is_seen` smallint(2) NULL DEFAULT 0;
");
$installer->endSetup();