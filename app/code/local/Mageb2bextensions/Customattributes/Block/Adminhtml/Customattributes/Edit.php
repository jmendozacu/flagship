<?php
/**
 * Edit.php
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
class Mageb2bextensions_Customattributes_Block_Adminhtml_Customattributes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected function _prepareLayout()
    {
        $this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup . '/adminhtml_customattributes_edit_form'));
        return Mage_Adminhtml_Block_Widget_Container::_prepareLayout();
    }
    
    public function __construct()
    {
    	
        $this->_objectId = 'attribute_id';
        $this->_controller = 'index';
        $this->_blockGroup = 'customattributes';

        parent::__construct();

        if($this->getRequest()->getParam('popup')) {
            $this->_removeButton('back');
            $this->_addButton(
                'close',
                array(
                    'label'     => Mage::helper('catalog')->__('Close Window'),
                    'class'     => 'cancel',
                    'onclick'   => 'window.close()',
                    'level'     => -1
                )
            );
        }

        $this->_updateButton('save', 'label', Mage::helper('catalog')->__('Save Attribute'));

        if (! Mage::registry('customattributes_data')->getIsUserDefined()) {
            $this->_removeButton('delete');
         } else {
           $this->_updateButton('delete', 'label', Mage::helper('catalog')->__('Delete Attribute'));
           $this->_updateButton('delete', 'onclick', "deleteConfirm(
            		'".Mage::helper('adminhtml')->__('Are you sure you want to do this?')."',
            		'".$this->getUrl('*/*/delete/attribute_id/'.$this->getRequest()->getParam('attribute_id')
            		)."')");
         }
    }

    public function getHeaderText()
    {
        if (Mage::registry('customattributes_data')->getId()) {
            return Mage::helper('customattributes')->__('Edit Registration Attribute "%s"', $this->htmlEscape(Mage::registry('customattributes_data')->getFrontendLabel()));
        } else {
            return Mage::helper('customattributes')->__('New Registration Attribute');
        }
    }
	
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/'.$this->_controller.'/save', array('_current'=>true, 'back'=>null));
    }
}