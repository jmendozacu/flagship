<?php 
$installer = $this;
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('barcodes/barcodes'), 'quantity', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'default' => 1,
        'comment' => 'Quantity'
    ));
   $installer->getConnection()->addColumn($installer->getTable('barcodes/barcodes'), 'product_name', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'productname'
    )
);
$installer->endSetup();
	$installer->endSetup();
?>