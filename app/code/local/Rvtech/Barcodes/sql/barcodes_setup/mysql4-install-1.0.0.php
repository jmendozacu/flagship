<?php 
$installer = $this;
	$installer->startSetup();
	$table = $installer->getConnection()->newTable($installer->getTable('barcodes/barcodes'))
	    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	        'unsigned' => true,
	        'nullable' => false,
	        'primary' => true,
	        'identity' => true,
	        ), 'ID')
	    ->addColumn('purchase_order', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
	        'nullable' => false,
	        ), 'Purchase Order')
	    ->addColumn('date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
	        ), 'Date')
	    ->addColumn('factory', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
	        'nullable' => true,
	        ), 'Factory')
	    ->addColumn('product', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
	        ), 'Product')
	    ->addColumn('barcode', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
	        ), 'Barcode')
	    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
	        ), 'ProductName')
	    ->addColumn('quantity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    	'default' => 1,
	        ), 'Quantity')
	    ->setComment('Rvtech barcodes/barcodes entity table');
	$installer->getConnection()->createTable($table);

	$installer->endSetup();
?>