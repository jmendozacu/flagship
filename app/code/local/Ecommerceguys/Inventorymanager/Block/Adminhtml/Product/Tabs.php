<?php
class Ecommerceguys_Inventorymanager_Block_Adminhtml_Product_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
	protected function _prepareLayout()
    {
    	$product = $this->getProduct();
    	$setId = $product->getAttributeSetId();
    	if ($setId) {
    		 $this->addTab('labeled', array(
                'label'     => Mage::helper('catalog')->__('Product Labels'),
                'url'       => $this->getUrl('inventorymanager/adminhtml_label/grid', array('_current' => true)),
                'class'     => 'ajax',
            ));
    	}
    	
    	return parent::_prepareLayout();
    }
}