<?php
$installer = $this;
$installer->startSetup();
$installer->run('
ALTER TABLE `'.$this->getTable('inventorymanager_purchaseorder_label').'` CHANGE `status` `status` varchar(100) NOT NULL default "";
');
$installer->endSetup();