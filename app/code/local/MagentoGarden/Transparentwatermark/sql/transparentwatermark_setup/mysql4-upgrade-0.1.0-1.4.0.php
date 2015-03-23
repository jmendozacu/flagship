<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('transparentwatermark_category')};
CREATE TABLE {$this->getTable('transparentwatermark_category')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `is_active` int(11) unsigned NOT NULL default 1,
  `category_id` int(11) unsigned NOT NULL,
  `store_view` varchar(255) NOT NULL, 
  
  `base_watermark` varchar(255) NULL default '', 
  `base_position_type` int(11) NULL default 0, 
  `base_default_position_type` int(11) NULL default 0, 
  `base_custom_position_x` int(11) NULL default 0, 
  `base_custom_position_y` int(11) NULL default 0,
  
  `small_watermark` varchar(255) NULL default '', 
  `small_position_type` int(11) NULL default 0, 
  `small_default_position_type` int(11) NULL default 0, 
  `small_custom_position_x` int(11) NULL default 0, 
  `small_custom_position_y` int(11) NULL default 0,
  
  `thumbnail_watermark` varchar(255) NULL default '', 
  `thumbnail_position_type` int(11) NULL default 0, 
  `thumbnail_default_position_type` int(11) NULL default 0, 
  `thumbnail_custom_position_x` int(11) NULL default 0, 
  `thumbnail_custom_position_y` int(11) NULL default 0,
  
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 
