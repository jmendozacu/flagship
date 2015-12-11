<?php 
class Ecommerceguys_Overrides_Model_Observer
{
	public function salesOrderGridCollectionLoadBefore($observer){
		$collection = $observer->getOrderGridCollection();
		
		$attribute = Mage::getSingleton('eav/config')
                ->getAttribute('customer', "company");
                
		$select = $collection->getSelect();
		$select->joinLeft(
			    array("cust" => $attribute->getBackendTable()),
			    'main_table.customer_id=cust.entity_id AND cust.attribute_id = '. $attribute->getId(),
			    array('value')
			);
	}
	
	public function filterCustomerCompany($collection, $column){
		$value = $column->getFilter()->getValue();
				
		$attribute = Mage::getSingleton('eav/config')
                ->getAttribute('customer', "company");

		$select = $collection->getSelect();
		$select->where('cust.value = "'.$value.'"');
	}
} 