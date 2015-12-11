<?php
$installer = $this;
$installer->startSetup();
$installer->run('
	ALTER TABLE `'.$this->getTable('inventorymanager_purchaseorder_label').'` ADD COLUMN `is_in_stock` smallint(4) NOT NULL default 0;
');
$installer->endSetup();