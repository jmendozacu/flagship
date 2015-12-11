<?php
$installer = $this;


$installer->startSetup();

$installer->run('

ALTER TABLE `'.$this->getTable('inventorymanager_product').'` ADD COLUMN `main_product_id` int(11) NOT NULL default 0;


');

$installer->endSetup();