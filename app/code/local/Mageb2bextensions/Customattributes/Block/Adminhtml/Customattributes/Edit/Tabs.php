<?php
/**
 * Tabs.php
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
class Mageb2bextensions_Customattributes_Block_Adminhtml_Customattributes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
     		parent::__construct();
        $this->setId('customattributes_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('catalog')->__('Attribute Information'));
  }
	protected function _beforeToHtml()
    {
        $this->addTab('main', array(
            'label'     => Mage::helper('catalog')->__('Properties'),
            'title'     => Mage::helper('catalog')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('customattributes/adminhtml_customattributes_edit_tab_main')->toHtml(),
            'active'    => true
        ));


        $this->addTab('labels', array(
            'label'     => Mage::helper('catalog')->__('Manage Label / Options'),
            'title'     => Mage::helper('catalog')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('customattributes/adminhtml_customattributes_edit_tab_options')->toHtml(),
        ));
        
        $this->addTab('websites', array(
            'label'     => Mage::helper('catalog')->__('Websites / Store Views'),
            'title'     => Mage::helper('catalog')->__('Websites / Store Views'),
            'content'   => $this->getLayout()->createBlock('customattributes/adminhtml_customattributes_edit_tab_websites')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }

}