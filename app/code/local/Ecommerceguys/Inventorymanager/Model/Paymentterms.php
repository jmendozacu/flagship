<?php 
class Ecommerceguys_Inventorymanager_Model_Paymentterms
{
	public function toOptionArray()
	{
	    
	    $options = array();
	    
	    $options[] = array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Credit Card'));
	    $options[] = array('value' => 2, 'label' => Mage::helper('adminhtml')->__('COD'));
	    $options[] = array('value' => 3, 'label' => Mage::helper('adminhtml')->__('Cash'));
	    $options[] = array('value' => 4, 'label' => Mage::helper('adminhtml')->__('Net30'));
	    $options[] = array('value' => 5, 'label' => Mage::helper('adminhtml')->__('Net60'));
	    
	    return $options;
	}
	
	public function getArray(){
		return array(
			1 => Mage::helper('adminhtml')->__('Credit Card'),
			2 => Mage::helper('adminhtml')->__('COD'),
			3 => Mage::helper('adminhtml')->__('Cash'),
			4 => Mage::helper('adminhtml')->__('Net30'),
			5 => Mage::helper('adminhtml')->__('Net60')
		);
	}
}