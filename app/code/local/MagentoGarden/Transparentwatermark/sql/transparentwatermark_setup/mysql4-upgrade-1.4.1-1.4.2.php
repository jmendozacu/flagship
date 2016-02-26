<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('transparentwatermark_category')} CHANGE `base_default_position_type` `base_default_position_type` VARCHAR( 20 ) NULL DEFAULT '0',
CHANGE `small_default_position_type` `small_default_position_type` VARCHAR( 20 ) NULL DEFAULT '0',
CHANGE `thumbnail_position_type` `thumbnail_position_type` VARCHAR( 20 ) NULL DEFAULT '0'

");

$installer->endSetup(); 
