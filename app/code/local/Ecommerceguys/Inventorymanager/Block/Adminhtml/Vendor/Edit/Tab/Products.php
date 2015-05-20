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
	
	protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        //if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        /*} else {
            parent::_addColumnFilterToCollection($column);
        }*/
        return $this;
    }
    
    public function isReadonly(){
    	return null;
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product_link')->useUpSellLinks()
            ->getProductCollection()
            //->setProduct($this->_getProduct())
            ->addAttributeToSelect('*');

        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            $collection->addFieldToFilter('entity_id', array('in'=>$productIds));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
    	$this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'customer',
                'values'            => $this->_getSelectedProducts(),
                'align'             => 'center',
                'index'             => 'entity_id'               
		));
		
		
		$this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
		
		/*$this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('ID'),
            'name'              => 'position',
            'width'             => 60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => true,
            'edit_only'         => true
            ));*/

    }
    
    protected function _getSelectedProducts()   // Used in grid to return selected customers values.
    {
        $customers = array_keys($this->getSelectedProducts());
        return $customers;
    }
 
    public function getSelectedProducts()
    {
        // Customer Data
        $tm_id = $this->getRequest()->getParam('id');
        if(!isset($tm_id)) {
            $tm_id = 0;
        }
        $customers = array(1,2); // This is hard-coded right now, but should actually get values from database.
        $custIds = array();
 
        foreach($customers as $customer) {
            foreach($customer as $cust) {
                $custIds[$cust] = array('position'=>$cust);
            }
        }
        return $custIds;
    }
    
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/productsgrid', array('_current'=>true));
    }
}