<?php 
 $installer = $this;
 $installer->startSetup();
 $installer->run("
 	DROP TABLE IF EXISTS `{$this->getTable('barcodes/barcodes')}`;
    CREATE TABLE `{$installer->getTable('barcodes/barcodes')}` (
      `id` int(11) NOT NULL auto_increment,
      `purchase_order` varchar(255) NOT NULL,
      `factory_id` int NOT NULL,
      `product_id` int NOT NULL,
      `factory_serial` varchar(255) NOT NULL,
      `dzv_serial` varchar(255) NOT NULL,
      `barcode` varchar(255) NOT NULL,
      `upc` varchar(255) NOT NULL,
      `sku` varchar(255) NOT NULL,
      `sequence` int NOT NULL,
      `date` date default NULL,
       PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
