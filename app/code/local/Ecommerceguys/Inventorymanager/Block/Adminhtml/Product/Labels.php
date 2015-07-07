<?php 

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Product_Labels extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('inventory_product_labels');
        $this->setDefaultSort('label_id');
        $this->setUseAjax(true);
        $this->setDefaultLimit(3);
    }
    
    public function getTabLabel(){
        return Mage::helper('inventorymanager')->__('Labeled Products');
    }

    public function getTabTitle(){
        return Mage::helper('inventorymanager')->__('Labeled Products');
    }
    
    public function canShowTab(){
	    return true;
	}
    
	public function isHidden(){
		return false;
	}
	
    public function getCurrentProductId(){
    	return $this->getRequest()->getParam('id',0);
    }
    
    public function getPurchaseorderProductId(){
    	$purchaseorderProducts = Mage::getModel('inventorymanager/product')->getCollection();
    	$purchaseorderProducts->addFieldToFilter("main_product_id", $this->getCurrentProductId());
    	$orderProductIds = array();
    	foreach ($purchaseorderProducts as $orderProduct){
    		$orderProductIds[] = $orderProduct->getId();
    	}
    	return $orderProductIds;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventorymanager/label')->getCollection();
        $collection->addFieldToFilter('product_id', array("in" => $this->getPurchaseorderProductId()));
        $collection->addFieldToFilter('location', Mage::helper('inventorymanager')->__("In Stock On Magento"));
        
        $resource = Mage::getSingleton('core/resource');
    	$orderTable = $resource->getTableName('inventorymanager_purchase_order');
    	$vendorTable = $resource->getTableName('inventorymanager_vendor');
        
        $collection->getSelect()->joinLeft(array('po' => $orderTable), "main_table.order_id = po.po_id", array('vendor_id'=>'vendor_id'))
        	->joinleft(array('vt' => $vendorTable), "po.vendor_id = vt.vendor_id", array("vendor_name" => "name"));
        
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
       
        $this->addColumn('label_id', array(
            'header'    => Mage::helper('inventorymanager')->__('ID'),
            'align'     =>'right',
          	'width'     => '50px',
            'index'     => 'label_id'
        ));
        
        $this->addColumn('main_image', array(
            'header'    => Mage::helper('inventorymanager')->__('Image'),
            'index'     => 'main_image',
            'width'     => '110',
            'renderer'	=> Ecommerceguys_Inventorymanager_Block_Adminhtml_Product_Labels_Image,
        ));
        
        $this->addColumn('serial', array(
            'header'    => Mage::helper('inventorymanager')->__('Serial'),
            'index'     => 'serial'
        ));
        
        $this->addColumn('vendor_name', array(
            'header'    => Mage::helper('inventorymanager')->__('Vendor Name'),
            'index'     => 'vendor_name'
        ));
        
        $this->addColumn('location', array(
            'header'    => Mage::helper('inventorymanager')->__('Location'),
            'index'     => 'location'
        ));
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('inventorymanager')->__('Status'),
            'index'     => 'status'
        ));
        
        
        
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('inventorymanager/adminhtml_label/serialgrid', array('_current'=>true));
    }
}