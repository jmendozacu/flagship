<?php
$installer = $this;
$installer->startSetup();
$installer->run('
ALTER TABLE `'.$this->getTable('inventorymanager_purchaseorder_label').'` ADD COLUMN `status` smallint(6) NOT NULL default 0;
');
$installer->endSetup();