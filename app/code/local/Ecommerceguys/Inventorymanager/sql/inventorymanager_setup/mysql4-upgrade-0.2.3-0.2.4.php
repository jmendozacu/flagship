<?php
$installer = $this;
$installer->startSetup();
$installer->run('
ALTER TABLE `'.$this->getTable('inventorymanager_purchaseorder_label_comment').'` ADD COLUMN `image` varchar(255) NOT NULL default "";
');
$installer->endSetup();