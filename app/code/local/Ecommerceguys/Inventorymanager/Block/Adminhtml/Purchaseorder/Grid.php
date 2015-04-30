<?php

class Ecommerceguys_Inventorymanager_Block_Adminhtml_Purchaseorder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('purchaseorderGrid');
      $this->setDefaultSort('po_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('inventorymanager/purchaseorder')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('po_id', array(
          'header'    => Mage::helper('inventorymanager')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'po_id',
      ));

      $this->addColumn('vendor_id', array(
          'header'    => Mage::helper('inventorymanager')->__('Vendor'),
          'align'     =>'left',
          'index'     => 'vendor_id',
      ));

      $this->addColumn('shipping_method', array(
          'header'    => Mage::helper('inventorymanager')->__('Shipping Method'),
          'align'     =>'left',
          'index'     => 'shipping_method',
      ));
      
      $this->addColumn('payment_terms', array(
          'header'    => Mage::helper('inventorymanager')->__('Payment Terms'),
          'align'     =>'left',
          'index'     => 'payment_terms',
      ));
      
      $this->addColumn('date_of_po', array(
          'header'    => Mage::helper('inventorymanager')->__('Date of Purchase order'),
          'align'     =>'left',
          'index'     => 'date_of_po',
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