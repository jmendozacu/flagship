<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `".$this->getTable('inventorymanager_purchase_order')."` CHANGE `date_of_po` `date_of_po` date NULL;
ALTER TABLE `".$this->getTable('inventorymanager_purchase_order')."` CHANGE `expected_date` `expected_date` date NULL;



");

$installer->endSetup(); 