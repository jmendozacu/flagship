<?php
$installer = $this;


$installer->startSetup();

$installer->run('
	
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `parent_id` int(11) NULL DEFAULT NULL;

');

$installer->endSetup();
