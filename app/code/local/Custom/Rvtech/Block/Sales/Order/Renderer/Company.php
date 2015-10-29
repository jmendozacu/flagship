<?php
class Custom_Rvtech_Block_Sales_Order_Renderer_Company extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
     $orderCollection = Mage::getModel('sales/order')->getCollection()
                      ->addFieldToFilter('increment_id', $row->getIncrementId())
                      ->addFieldToSelect('customer_id')
                      ->getFirstItem();
      $customer_info = Mage::getResourceModel('customer/customer_collection')
                      ->addFieldToFilter('entity_id', $orderCollection->getCustomerId())
                      ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left')
                      ->getFirstItem();

    	return $customer_info->getCompany();
    }
}

