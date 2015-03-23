<?php

class MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		$this->setId('twcategoryGrid');
		$this->setUseAjax(true);
		$this->setDefaultSort('entity_id');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _prepareCollection() {
		$collection = Mage::getResourceModel('transparentwatermark/twcategory_collection');
		$this->setCollection($collection);
		
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns() {
		$this->addColumn('entity_id', array(
			'header' => $this->__('ID'),
			'width' => '50px',
			'index' => 'entity_id',
			'type' => 'number',
		));
		
		$this->addColumn('is_active', array(
			'header' => $this->__('Is Active'),
			'width' => '70px',
			'index' => 'is_active',
			'type' => 'text',
//			'renderer' => 'MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Renderer_Categoryid',
		));
		
		$this->addColumn('disable_watermark', array(
			'header' => $this->__('Disable Watermark'),
			'width' => '70px',
			'index' => 'disable_watermark',
			'type' => 'text',
//			'renderer' => 'MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Renderer_Categoryid',
		));
		
		
		$this->addColumn('category', array(
			'header' => $this->__('Category'),
			'width' => '70px',
			'index' => 'category_id',
			'type' => 'text',
			'renderer' => 'MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Renderer_Categoryid',
		));
		
		/*$this->addColumn('watermark', array(
			'header' => $this->__('Watermark Image'),
			'width' => '100px',
			'index' => 'watermark',
			'type' => 'text',
			'renderer' => 'MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Renderer_Watermark',
		));*/
		
		$this->addColumn('store_view', array(
			'header' => $this->__('Store View'),
			'width' => '70px',
			'index' => 'store_view',
			'type' => 'text',
			'renderer' => 'MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Renderer_Storeview',
		));
		
		$this->addColumn('creation_time', array(
			'header' => $this->__('Date Created'),
			'width' => '50px',
			'index' => 'created_time',
			'type' => 'datetime',
		));
		
		$this->addColumn('update_time', array(
			'header' => $this->__('Date Updated'),
			'width' => '70px',
			'index' => 'update_time',
			'type' => 'datetime',
		));
		
		return parent::_prepareColumns();
	}
	
	public function getGridUrl() {
		return $this->getUrl('*/*/grid', array('_current'=> true));	
	}
	
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
	}
}
