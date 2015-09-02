<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `".$this->getTable('inventorymanager_purchaseorder_label_history')."` CHANGE `user_id` `user_id` varchar(100) NOT NULL default '';



");

$installer->endSetup(); 