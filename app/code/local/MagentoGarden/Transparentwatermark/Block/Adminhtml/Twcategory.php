<?php 

class MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory extends Mage_Adminhtml_Block_Widget_Grid_Container {

	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function __construct() {
    	$this->_blockGroup = 'transparentwatermark';
		$this->_controller = 'adminhtml_twcategory';
		$this->_headerText = Mage::helper('transparentwatermark')->__('Manage Category Watermark');
		$this->_addButtonLabel = Mage::helper('transparentwatermark')->__('Add New Category Watermark');
        parent::__construct();
    }

	public function getStores() {
    	return Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
    }
}
