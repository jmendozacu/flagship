<?php
$installer = $this;
$installer->startSetup();
$installer->run('
ALTER TABLE `'.$this->getTable('inventorymanager_vendor_productdetail').'` ADD COLUMN `is_revision` varchar(50) NOT NULL default 0;
');
$installer->endSetup();