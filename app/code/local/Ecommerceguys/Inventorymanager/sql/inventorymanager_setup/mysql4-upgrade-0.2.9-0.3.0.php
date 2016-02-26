<?php
$installer = $this;
$installer->startSetup();
$installer->run('
	ALTER TABLE `'.$this->getTable('inventorymanager_purchaseorder_label').'` ADD COLUMN `is_out_stock` smallint(4) NOT NULL default 0;
	ALTER TABLE `'.$this->getTable('inventorymanager_purchaseorder_label').'` ADD COLUMN `real_order_id` int(11) NOT NULL default 0;
');
$installer->endSetup();