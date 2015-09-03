<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE `".$this->getTable('inventorymanager_purchaseorder_label')."` ADD COLUMN `shipping_price` float(11) NULL DEFAULT NULL;;
");
$installer->endSetup();