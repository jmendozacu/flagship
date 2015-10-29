<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('transparentwatermark_category')} ADD `disable_watermark` INT NOT NULL; 

");

$installer->endSetup(); 
