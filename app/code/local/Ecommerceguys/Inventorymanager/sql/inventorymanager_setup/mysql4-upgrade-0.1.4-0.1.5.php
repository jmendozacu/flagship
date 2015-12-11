<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('inventorymanager_pohistory')};
CREATE TABLE {$this->getTable('inventorymanager_pohistory')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `po_id` int(11) unsigned NOT NULL default 0,
  `comment` text NOT NULL default '',
  `attachement` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=nuInnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 