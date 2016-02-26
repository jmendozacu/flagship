<?php
$installer = $this;
$installer->startSetup();
$installer->run('
ALTER TABLE `'.$this->getTable('inventorymanager_purchaseorder_label').'` ADD COLUMN `main_image` varchar(255) NOT NULL default "";
');
$installer->endSetup();