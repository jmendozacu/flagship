<?php 
class Ecommerceguys_Inventorymanager_Block_Adminhtml_Vendor_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
	    parent::__construct();
	    $this->setId('productsGrid');
	    $this->setUseAjax(true); 
	    $this->setDefaultSort('entity_id');
	    $this->setDefaultFilter(array('in_products'=>1)); // By default we have added a filter for the rows, that in_products value to be 1
	    $this->setSaveParametersInSession(false);  //Dont save paramters in session or else it creates problems
	}
	
	public function getCurrentVendor(){
		return Mage::registry('vendor_data');
	}
	
	protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
            	if($this->getRequest()->getParam('vendor_id') != 0){
                	$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            	}
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    public function isReadonly(){
    	return true;
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            //->setProduct($this->_getProduct())
            ->addAttributeToSelect('*');

        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = array(0);
            }
          //  $collection->addFieldToFilter('entity_id', array('in'=>$productIds));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
    	$this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'product_id',
                'values'            => $this->getSelectedProducts(),
                'align'             => 'center',
                'index'             => 'entity_id'               
		));
		
		
		$this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));
        $this->addColumn('p_name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));

    }
    
    protected function _getSelectedProducts()   // Used in grid to return selected customers values.
    {
        $customers = array_keys($this->getSelectedProducts());
        return $customers;
    }
 
    public function getSelectedProducts()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id');
     
        $vendorProductResource = Mage::getResourceModel('inventorymanager/vendor_products');
        $products = $vendorProductResource->getRecords($vendorId); // This is hard-coded right now, but should actually get values from database.
        $returnIds = array();
        $i = 1;
        foreach($products as $pid) {
        	$val = $pid['product_id'];
			$returnIds[$val] = $val;
        }
        return $returnIds;
    }
    
    public function getProductVals(){
    	$products = $this->_getSelectedProducts();
    	return $products;
    }
    
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/productsgrid', array('_current'=>true));
    }
}