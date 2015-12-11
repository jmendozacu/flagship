<?php

class Rvtech_Purchaseorder_Block_Adminhtml_Purchaseorder_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	protected $_proURL;

	public function __construct() {
		parent::__construct();
		$this->setId('codesGrid1');
		$this->setDefaultSort('po_date');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('filter');
	}

	protected function _getStore() {
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}

	protected function _prepareCollection() {

		$collection = Mage::getSingleton('barcodes/barcodes')->getCollection();
        //$resource = Mage::getSingleton('core/resource');
	 //  echo $tableName = $resource->getTableName('Purchase/Order');exit;
		$collection->getSelect()
				->joinLeft(
						array('purchase_order'), 'purchase_order = purchase_order.po_order_id', array('po_num','po_date')
		)->group('purchase_order');
				
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _factoryFilter($collection, $column) {
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}

		$factIds = array(0);
		$sort = ( $this->getRequest()->getParam('sort') ) ? $this->getRequest()->getParam('sort') : 'id';
		$dir = ($this->getRequest()->getParam('dir')) ? $this->getRequest()->getParam('dir') : 'DESC';

		$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->addFieldToFilter('attribute_code', 'factory') 
                            ->load();
          	$attribute = $attributes->getFirstItem();

          	$attr = $attribute->getSource()->getAllOptions(true);
          	foreach ($attr as $attval) {
           //if($attval['label']==ucfirst($value))
          		if (stripos($attval['label'],$value) !== false) 
               {
                   $factIds[] = $attval['value'];
                }
            }

		$this->getCollection()
				->addFieldToFilter("factory_id", array('in' => $factIds))
				->setOrder($sort, $dir)
				->load();

		return $this;
	}
	
	protected function _prepareColumns() {
		$this->addColumn('po_num', array(
			'header' => 'ID',
			'align' => 'right',
			'width' => '50px',
			'index' => 'po_num',
		));

		/* create factory drodown starts */
        $attributes_option = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->addFieldToFilter('attribute_code', 'factory') 
                            ->load();
        $factory_option = $attributes_option->getFirstItem();

      	$factory_dropdowns = $factory_option->getSource()->getAllOptions(true);
      	
      	foreach ($factory_dropdowns as $factory_dropdown) {
	         if ($factory_dropdown['label'] != '') {
	              $factIds[$factory_dropdown['value']] = $factory_dropdown['label'];
	           }
        }
        /* create factory drodown ends */
        
		$this->addColumn('factory_id', array(
			'header' => 'Factory',
			'align' => 'left',
			'index' => 'factory_id',
			'type'  => 'options',
            'options' => $factIds,
		));
		$this->addColumn('purchase_order', array(
				'header' => 'Purchase Order',
				'align' => 'left',
				'index' => 'purchase_order',
				'renderer' => 'Rvtech_Purchaseorder_Block_Adminhtml_Purchaseorder_Renderer_Product'
		));
	
		
		$this->addColumn('po_date', array(
			'header' => 'Date Ordered',
			'align' => 'left',
			'index' => 'po_date',
			'renderer' => 'Rvtech_Purchaseorder_Block_Adminhtml_Purchaseorder_Renderer_Date',
		));

		$this->addColumn('product_id', array(
			'header' => 'No. of Products',
			'align' => 'left',
			'index' => 'product_id',
			'renderer' => 'Rvtech_Purchaseorder_Block_Adminhtml_Purchaseorder_Renderer_Product',
		));
		$this->addColumn('dzv_serial', array(
			'header' => 'No. of Serials',
			'align' => 'left',
			'index' => 'dzv_serial',
			'renderer' => 'Rvtech_Purchaseorder_Block_Adminhtml_Purchaseorder_Renderer_Product',
		));
		$this->addColumn('action', array(
			'header' => 'Action',
			'align' => 'left',
			'renderer' => 'Rvtech_Purchaseorder_Block_Adminhtml_Purchaseorder_Renderer_Action',
		));
		//$this->addExportType('*/*/exportCsv', 'CSV');
		//$this->addExportType('*/*/exportXml', 'Excel XML');

		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
    {
        // return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    public function getGridUrl()
	{
	    return $this->getUrl('*/*/grid', array('_current'=>true));
	}
}