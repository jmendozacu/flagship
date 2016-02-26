<?php
$installer = $this;


$installer->startSetup();

$installer->run('

ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `company_name` varchar(255) NOT NULL default "";
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `company_phone` varchar(20) NOT NULL default "";
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `telephone_extension` varchar(20) NOT NULL default "";
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `street_address` varchar(400) NOT NULL default "";
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `city` varchar(100) NOT NULL default "";
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `state` varchar(100) NOT NULL default "";
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `zip_code` varchar(20) NOT NULL default "";
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `main_rep_name` varchar(100) NOT NULL default "";
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `industry` varchar(100) NOT NULL default "";
ALTER TABLE `'.$this->getTable('inventorymanager_vendor').'` ADD COLUMN `number_of_employees` int(11) NULL DEFAULT NULL;

');

$installer->endSetup();
