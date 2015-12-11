<?php
$installer = $this;


$installer->startSetup();

$installer->run('
	
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `is_employer` smallint(5) NULL DEFAULT NULL;

');

$installer->endSetup();
