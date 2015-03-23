<?php
class Custom_Rvtech_Block_Customer_Renderer_Company extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
     
      $customer_info = Mage::getResourceModel('customer/customer_collection')
                      ->addFieldToFilter('entity_id', $row->getEntityId())
                      ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left')
                      ->getFirstItem();

    	return $customer_info->getCompany();
    }
}

