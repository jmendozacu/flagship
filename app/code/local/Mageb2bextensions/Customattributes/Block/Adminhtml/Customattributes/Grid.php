<?php
/**
 * Grid.php
 * MageB2BExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageb2bextensions.com/LICENSE-M1.txt
 *
 * @package    Mageb2bextensions_Customattributes
 * @copyright  Copyright (c) 2003-2009 MageB2BExtensions @ InterSEC Solutions LLC. (http://www.mageb2bextensions.com)
 * @license    http://www.mageb2bextensions.com/LICENSE-M1.txt
 */

class Mageb2bextensions_Customattributes_Block_Adminhtml_Customattributes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      
      $this->setId('mb2bcustomfieldsgrid');
      $this->setDefaultSort('attribute_code');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      $this->setTemplate('customattributes/grid.phtml');
  }

  protected function _prepareCollection()
  {
      $type='mb2b_checkout';
      
      $oResource = Mage::getResourceModel('eav/entity_attribute');
            
      $this->type=$type;
      
            $collection = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType($type)->getTypeId() )
            ;
            $collection->getSelect()->join(
                array('additional_table' => $oResource->getTable('catalog/eav_attribute')),
                'additional_table.attribute_id=main_table.attribute_id'
            );
      
      
      $this->setCollection($collection);
      return parent::_prepareCollection();
      
  }

  protected function _prepareColumns()
  {
      $this->addColumn('attribute_code', array(
            'header'=>Mage::helper('catalog')->__('Attribute Code'),
            'sortable'=>true,
            'index'=>'attribute_code'
        ));

        $this->addColumn('frontend_label', array(
            'header'=>Mage::helper('catalog')->__('Attribute Label'),
            'sortable'=>true,
            'index'=>'frontend_label'
        ));

        $this->addColumn('frontend_input', array(
            'header'=>Mage::helper('catalog')->__('Input Type'),
            'sortable'=>true,
            'index'=>'frontend_input',
            'type' => 'options',
            'options' => array(
                'text'          => Mage::helper('catalog')->__('Text Field'),
                'textarea'      => Mage::helper('catalog')->__('Text Area'),
                'date'          => Mage::helper('catalog')->__('Date'),
                'boolean'       => Mage::helper('catalog')->__('Yes/No'),
                'multiselect'   => Mage::helper('catalog')->__('Multiple Select'),
                'select'        => Mage::helper('catalog')->__('Dropdown'),
                'checkbox'      => Mage::helper('catalog')->__('Checkbox'),
                'radio'         => Mage::helper('catalog')->__('Radiobutton'),
            ),
        ));
        $this->addColumn('is_filterable', array(
            'header'=>Mage::helper('catalog')->__('Attribute Placeholder'),
            'sortable'=>true,
            'index'=>'is_filterable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('On Top'),
                '2' => Mage::helper('catalog')->__('At the bottom'),
            ),
            'align' => 'left',
        ));
        
 /************    START CUSTOM ATTRIBUTES          ************/

				$this->addColumn('is_used_for_price_rules', array(
            'header'=>Mage::helper('catalog')->__('Step (for customer reg)'),
            'sortable'=>true,
            'index'=>'is_used_for_price_rules',
            'type' => 'options',
            'options' => Mage::helper('customattributes')->getStepData('registerpage', 'hash'),
        ));
 
 /************    FINISH CUSTOM ATTRIBUTES          ************/
        
#		$this->addExportType('*/*/exportCsv', Mage::helper('customattributes')->__('CSV'));
#		$this->addExportType('*/*/exportXml', Mage::helper('customattributes')->__('XML'));
	  
      return parent::_prepareColumns();
  }

  public function addNewButton(){
  	return $this->getButtonHtml(
  		Mage::helper('customattributes')->__('New Attribute'), //label
  		"setLocation('".$this->getUrl('*/*/new', array('attribute_id'=>0))."')", //url
  		"scalable add" //class css
  		);
  }
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('attribute_id' => $row->getAttributeId()));
  }
}