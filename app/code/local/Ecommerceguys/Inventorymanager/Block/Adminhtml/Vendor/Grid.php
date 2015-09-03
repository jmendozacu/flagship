<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Vendor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorGrid');
      $this->setDefaultSort('vendor_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('inventorymanager/vendor')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('vendor_id', array(
          'header'    => Mage::helper('inventorymanager')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'vendor_id',
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('inventorymanager')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));

      $this->addColumn('email', array(
          'header'    => Mage::helper('inventorymanager')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));
	  
      /*$this->addColumn('status', array(
          'header'    => Mage::helper('inventorymanager')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));*/
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('inventorymanager')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('inventorymanager')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		//$this->addExportType('*/*/exportCsv', Mage::helper('inventorymanager')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('inventorymanager')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('vendor_id');
        $this->getMassactionBlock()->setFormFieldName('inventorymanager');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('inventorymanager')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('inventorymanager')->__('Are you sure?')
        ));

        
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}