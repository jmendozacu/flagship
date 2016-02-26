<?php

class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	protected $_proURL;

	public function __construct() {
		parent::__construct();
		$this->setId('codesGrid');
		$this->setDefaultSort('dzv_serial');
		$this->setDefaultDir('ASC');
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

		$collection->getSelect()
				->joinLeft(
						array('catalog_product_entity'), 'product_id = catalog_product_entity.entity_id', array('sku')
		);
				
         $productAttributes = array('name' => 'name','upc' => 'upc');

    	foreach ($productAttributes as $alias => $attributeCode) {

    		 $tableAlias = $attributeCode . '_table';
    		 $tableAliaId = $tableAlias.'.attribute_id';
    		$attribute = Mage::getSingleton('eav/config')
    		->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
    		$collection->getSelect()->joinInner(
    				array($tableAlias => $attribute->getBackendTable()),
    				"product_id = $tableAlias.entity_id AND $tableAlias.attribute_id={$attribute->getId()}",
    				array($alias => 'value')
    		);
    	}

    	$collection->getSelect()->group('id');
 
		if (!$this->_isExport) {

			if ($this->getRequest()->getParam('form_key') == '') {

				if ($this->getRequest()->getParam('filter')) {
					$poId = $this->getRequest()->getParam('filter');
					$poId = base64_decode($poId);
					$column = $this->getColumn('purchase_order');
					$column->getFilter()->setValue($poId);
					$collection->addFieldToFilter('purchase_order', array('eq' => $poId));
					$layoutJs = $this->getLayout();
			        $blockJs = $layoutJs->createBlock('core/text');
			        $blockJs->setText(
			        '<script type="text/javascript">
			           codesGridJsObject.doFilter();
			        </script>'
			        );        
			        $layoutJs->getBlock('js')->append($blockJs); 
				}
			}
		}




		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _nameFilter($collection, $column) {
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}

		$productIds = array(0);

		$sort = ( $this->getRequest()->getParam('sort') ) ? $this->getRequest()->getParam('sort') : 'id';
		$dir = ($this->getRequest()->getParam('dir')) ? $this->getRequest()->getParam('dir') : 'DESC';

		$productCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('entity_id')
				->addFieldToFilter('name', array('like' => '%' . $value . '%'))
				->load()
				->getData();

		foreach ($productCollection as $product) {
			$productIds[] = $product['entity_id'];
		}
		$this->getCollection()
				->addFieldToFilter("product_id", array('in' => $productIds))
				->setOrder($sort, $dir)
				->load();

		return $this;
	}

	protected function _upcFilter($collection, $column) {
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}

		$productIds = array(0);

		$sort = ( $this->getRequest()->getParam('sort') ) ? $this->getRequest()->getParam('sort') : 'id';
		$dir = ($this->getRequest()->getParam('dir')) ? $this->getRequest()->getParam('dir') : 'DESC';

		$productCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('entity_id')
				->addFieldToFilter('upc', array('like' => '%' . $value . '%'))
				->load()
				->getData();

		foreach ($productCollection as $product) {
			$productIds[] = $product['entity_id'];
		}
		$this->getCollection()
				->addFieldToFilter("product_id", array('in' => $productIds))
				->setOrder($sort, $dir)
				->load();
		return $this;
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
              if (stripos($attval['label'],$value) !== false) {
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
		$this->addColumn('id', array(
			'header' => 'ID',
			'align' => 'right',
			'width' => '50px',
			'index' => 'id',
		));
		$this->addColumn('date', array(
			'header' => 'Date Ordered',
			'align' => 'left',
			'index' => 'date',
			'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Date',
		));
        
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

		$this->addColumn('factory_id', array(
			'header' => 'Factory',
			'align' => 'left',
			'index' => 'factory_id',
			// 'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Product',
			// 'filter_condition_callback' => array($this, '_factoryFilter'),
			'type'  => 'options',
            'options' => $factIds,
		));
		if (!$this->_isExport) {
			$this->addColumn('purchase_order', array(
				'header' => 'Purchase Order',
				'align' => 'left',
				'index' => 'purchase_order',
				'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Product'
			));
		}
		if ($this->_isExport) {
			$this->addColumn('purchase_order', array(
				'header' => 'Purchase Order',
				'align' => 'left',
				'index' => 'purchase_order',
			));
		}
		$this->addColumn('product_id', array(
			'header' => 'Product ID',
			'align' => 'left',
			'index' => 'product_id',
		));
		$this->addColumn('sku', array(
			'header' => 'SKU',
			'align' => 'left',
			'index' => 'sku',
			'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Product',
		));
		if (!$this->_isExport) {
			$this->addColumn('name', array(
				'header' => 'Product Name',
				'align' => 'left',
				'index' => 'name',
				'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductRender',
				'filter_condition_callback' => array($this, '_nameFilter'),
			));
		}
		if ($this->_isExport) {
			$this->addColumn('product_name', array(
				'header' => 'Product Name',
				'align' => 'left',
				'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductName',
				'filter_condition_callback' => array($this, '_nameFilter'),
			));
		}
		$this->addColumn('upc', array(
			'header' => 'UPC',
			'align' => 'left',
			'index' => 'upc',
			//'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Product',
			'filter_condition_callback' => array($this, '_upcFilter'),
		));

		if (!$this->_isExport) {

			$this->addColumn('dzv_serial', array(
				'header' => 'DZV Serial',
				'align' => 'left',
				'index' => 'dzv_serial',
				'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Action',
			));

			$this->addColumn('location', array(
				'header' => 'Location',
				'align' => 'left',
				'index' => 'location',
			));
		}
		if ($this->_isExport) {
			$this->addColumn('dzv_serial', array(
				'header' => 'DZV Serial',
				'align' => 'left',
				'index' => 'dzv_serial',
			));
		}

		if ($this->_isExport) {

			$this->addColumn('weight', array(
				'header' => 'Weight',
				'align' => 'left',
				'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductAcWeight',
			));
			$this->addColumn('size', array(
				'header' => 'Size',
				'align' => 'left',
				'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductSize',
			));
			$this->addColumn('cfm', array(
				'header' => 'CFM',
				'align' => 'left',
				'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductCfm',
			));
			$this->addColumn('image_url', array(
				'header' => 'Main Image Url',
				'align' => 'left',
				'renderer' => 'Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductImage',
			));
		}

		$this->addExportType('*/*/exportCsv', 'CSV');
		$this->addExportType('*/*/exportXml', 'Excel XML');

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('serial_id');
		$this->getMassactionBlock()->setFormFieldName('id');
		$this->getMassactionBlock()->addItem('delete', array(
			'label' => $this->helper('catalog')->__('Delete'),
			'url' => $this->getUrl('*/*/massDelete', array('' => '')),
			'confirm' => $this->helper('catalog')->__('Are you sure you want to delete the selected serial(s)?')
		));
		return $this;
	}

	public function getRowUrl($row) {
		// return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	public function getGridUrl()
	{
	    return $this->getUrl('*/*/grid', array('_current'=>true));
	}
}