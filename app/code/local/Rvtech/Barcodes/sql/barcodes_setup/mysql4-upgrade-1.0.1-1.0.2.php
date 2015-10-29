<?php 
$installer = $this;
$installer->startSetup();
   $installer->getConnection()->addColumn($installer->getTable('barcodes/barcodes'), 'factory_name', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Factory Name'
    )
);
$installer->endSetup();
	$installer->endSetup();
?>