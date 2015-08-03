<?php
class Ecommerceguys_Inventorymanager_Block_Adminhtml_Sales_Order_View_Tabs_Serial extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function __construct()
    {
    	parent::__construct();
		$this->setId('sales_order_label_grid');
      	//$this->setDefaultSort('label_id');
      	//$this->setDefaultDir('ASC');
      	$this->setSaveParametersInSession(false);
      	$this->setUseAjax(true);
      	$this->setDefaultLimit(2);
    }
    
    protected function _prepareCollection()
	{
		$collection = Mage::getModel('inventorymanager/label')->getCollection();
		//$collection->addFieldToFilter('real_order_id', $this->getOrder()->getId());
		$collection->addFieldToFilter('order_id', 12) ;
		
		$resource = Mage::getSingleton('core/resource');
		$tableName = $resource->getTableName('inventorymanager_product');
		$collection
			->getSelect()
			->joinLeft(
				array('invent_prod'=>$tableName),
				'main_table.product_id = invent_prod.product_id',
				array('po_id','main_product_id')
			);
		/*echo $collection
			->getSelect(); exit;*/
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns(){
		$this->addColumn('label_id', array(
			'header'    => Mage::helper('inventorymanager')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'label_id',
		));
		
		$this->addColumn('serial', array(
			'header'    => Mage::helper('inventorymanager')->__('Serial'),
			'align'     =>'left',
			'index'     => 'serial',
      	));
      	
      	$this->addColumn('main_product_id', array(
			'header'    => Mage::helper('inventorymanager')->__('Product'),
			'align'     =>'left',
			'index'     => 'main_product_id',
			'renderer'  => 'Ecommerceguys_Inventorymanager_Block_Adminhtml_Sales_Order_View_Tabs_Renderer_Product',
      	));
      	return parent::_prepareColumns();
	}
    
    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Retrieve source model instance
     *
     * @return Mage_Sales_Model_Order
     */
    /*public function getSource()
    {
        return $this->getOrder();
    }*/


    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Serials');
    }

    public function getTabTitle()
    {
    	return Mage::helper('sales')->__('Serials');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
    
    
    public function getGridUrl()
    {
        return $this->getUrl('inventorymanager/adminhtml_label/ordergrid', array('_current' => true));
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order_invoice/view',
            array(
                'invoice_id'=> $row->getId()
            )
        );
    }
}