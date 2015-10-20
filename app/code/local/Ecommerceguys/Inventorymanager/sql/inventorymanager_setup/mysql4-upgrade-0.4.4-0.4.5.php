<?php
$installer = $this;


$installer->startSetup();

$installer->run('
	
ALTER TABLE `'.$this->getTable('inventorymanager_purchaseorder_label_location').'` ADD COLUMN `visible_area` smallint(5) NULL DEFAULT NULL;

');

$installer->endSetup();
