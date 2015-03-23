<?php

class MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct() {
		$this -> _objectId = 'entity_id';
		$this -> _blockGroup = 'transparentwatermark';
		$this -> _controller = 'adminhtml_twcategory';

		parent::__construct();

		$this -> _updateButton('save', 'label', Mage::helper('transparentwatermark') -> __('Save'));
		$this -> _updateButton('delete', 'label', Mage::helper('transparentwatermark') -> __('Delete'));
		
		/*$this -> _addButton('saveandcontinue', array(
			'label' => Mage::helper('transparentwatermark') -> __('Save and Continue'),
			'onclick' => 'saveAndContinueAction()',
			'class' => 'save',
		), -100);*/
	}
	
	public function getTwcategoryId() {
		return Mage::registry('current_twcategory') -> getData('entity_id');
	}
	
	public function getHeaderText()
    {
    	if (Mage::registry('current_twcategory')->getData('entity_id')) {
            return $this->htmlEscape(Mage::registry('current_twcategory')->getData('title'));
        }
        else {
            return Mage::helper('transparentwatermark')->__('New Twcategory');
        }
    }
	
	protected function _prepareLayout() {
		return parent::_prepareLayout();
	}
	
	protected function _getSaveAndContinueUrl() {
		return $this -> getUrl('*/*/save', array('_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}'));
	}
	
	public function getFormHtml()
    {
        $html = parent::getFormHtml();
        return $html;
    }
}
