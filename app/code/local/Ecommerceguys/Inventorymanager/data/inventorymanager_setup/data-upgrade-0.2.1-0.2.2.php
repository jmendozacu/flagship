<?php

$installer = $this;

$installer->startSetup();

$data = array(
	array('vendor_id' => 0, 'status' => 'Arrived in warehouse'),
	array('vendor_id' => 0, 'status' => 'Ready in the factory'),
	array('vendor_id' => 0, 'status' => 'Shipped'),
	array('vendor_id' => 0, 'status' => 'Sent to client'),
);

$installer->getConnection()->insertMultiple($installer->getTable('inventorymanager_purchaseorder_label_status'), $data);

$installer->endSetup(); 