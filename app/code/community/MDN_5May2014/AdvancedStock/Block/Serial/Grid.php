<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AdvancedStock_Block_Serial_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Enter description here...
	 *
	 */
    public function __construct()
    {
        parent::__construct();
        $this->setId('SerialsGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText($this->__('No items'));
        $this->setSaveParametersInSession(true);
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {		            
		$collection = Mage::getModel('AdvancedStock/ProductSerial')
					->getCollection()
					->join('catalog/product', 'pps_product_id=`catalog/product`.entity_id')
			        ->join('AdvancedStock/CatalogProductVarchar', '`catalog/product`.entity_id=`AdvancedStock/CatalogProductVarchar`.entity_id and store_id = 0 and attribute_id = '.mage::getModel('AdvancedStock/Constant')->GetProductNameAttributeId());
         // echo "<pre>";print_r($collection->getData());exit;    
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * author: Arun
     */
    protected function _orderFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
        $orderIds = array(0);  
       
        $orderCollection = Mage::getModel('Purchase/Order')->getCollection()
                                ->addFieldToSelect('po_num')
                                ->addFieldToFilter('po_order_id',array('like' => '%'.$value.'%'))
                                ->load()
                                ->getData();

        foreach ($orderCollection as $order) 
        {
            $orderIds[] = $order['po_num'];         
        }            
        
        $this->getCollection()->addFieldToFilter("pps_purchaseorder_id", array('in' => $orderIds));

        return $this;
    }
    
   /**
     * 
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
		$this->addColumn('pps_product_id', array(
            'header'=> Mage::helper('purchase')->__('Product ID'),
            'index' => 'pps_product_id'
        ));
		
    	$this->addColumn('sku', array(
            'header'=> Mage::helper('purchase')->__('Sku'),
            'index' => 'sku'
        ));
        
        $this->addColumn('value', array(
            'header'=> Mage::helper('purchase')->__('Product'),
            'index' => 'value'
        ));
        
        $this->addColumn('pps_serial', array(
            'header'=> Mage::helper('purchase')->__('Serial'),
            'index' => 'pps_serial',
            'align' => 'center'
        ));

        // BOF Arun
        $this->addColumn('av_location', array(
            'header'=> Mage::helper('purchase')->__('Location'),
            'index' => 'av_location',
            'align' => 'center'
        ));
        // EOF Arun
      
        $this->addColumn('pps_purchase_order', array(
            'header'=> Mage::helper('purchase')->__('Purchase Order'),
            'align' => 'center',
            'index' => 'pps_purchaseorder_id',
            'renderer' => 'MDN_AdvancedStock_Block_Serial_Widget_Grid_Column_Renderer_PurchaseOrder',
            'filter_condition_callback' => array($this, '_orderFilter'),
        ));
        
        $this->addColumn('pps_id_delete', array(
            'header'=> Mage::helper('purchase')->__('Delete'),
            'index' => 'pps_id',
            'align' => 'center',
            'renderer' => 'MDN_AdvancedStock_Block_Serial_Widget_Grid_Column_Renderer_DeleteSerial',
            'filter' => false
        ));
        
        return parent::_prepareColumns();
    }


    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }
    
    public function getRowUrl($row)
    {
    	//nothing
    }

}
