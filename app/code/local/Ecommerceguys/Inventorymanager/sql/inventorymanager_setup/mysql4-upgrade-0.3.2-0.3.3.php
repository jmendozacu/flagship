<?php

$installer = $this;

$installer->startSetup();

$installer->run("


-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_purchaseorder_label_history')};
CREATE TABLE {$this->getTable('inventorymanager_purchaseorder_label_history')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `label_id` int(11) unsigned NOT NULL default 0,
  `location` varchar(255) NOT NULL default '',
  `status` varchar(255) NOT NULL default '',
  `user_id` int(11) unsigned NOT NULL default 0,
  `created_time` datetime NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;


");

$installer->endSetup(); 