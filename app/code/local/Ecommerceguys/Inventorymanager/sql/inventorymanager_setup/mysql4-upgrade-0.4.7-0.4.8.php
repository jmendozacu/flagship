<?php
$installer = $this;


$installer->startSetup();

$installer->run('
	
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `active` smallint(5) NOT NULL DEFAULT 1;

');

$installer->endSetup();
