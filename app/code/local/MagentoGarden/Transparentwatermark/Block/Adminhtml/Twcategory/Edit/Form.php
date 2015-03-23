<?php 

class MagentoGarden_Transparentwatermark_Block_Adminhtml_Twcategory_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		$_twcategory = Mage::registry('current_twcategory');
		
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/twcategory/save'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));
		
		$_fieldset = $form->addFieldset('general_fieldset', array('legend'=>$this->__('General')));
		
		if ($_twcategory->getData('entity_id'))
			$isElementDisabled = true;
		else
			$isElementDisabled = false;
		
		$_fieldset -> addField('is_active', 'select', array(
			'label' => $this->__('Is Active'),
			'title' => $this->__('Is Active'),
			'name' => 'is_active',
			'required' => true,
			'values' => Mage::helper('transparentwatermark')->getIsActiveList(),
		));
		
		$_fieldset -> addField('disable_watermark', 'select', array(
			'label' => $this->__('Disable Watermark'),
			'title' => $this->__('Disable Watermark'),
			'name' => 'disable_watermark',
			'required' => true,
			'values' => Mage::helper('transparentwatermark')->getYesNoList(),
		));
		
		$_fieldset -> addField('category_id', 'select', array(
			'label' => $this->__('Category'),
			'title' => $this->__('Category'),
			'name' => 'category_id',
			'required' => true,
			'values' => Mage::helper('transparentwatermark')->getCategoryList(),
			'disabled' => $isElementDisabled,
		));
		
		if (!Mage::app()->isSingleStoreMode()) {
            $_fieldset->addField('storeview', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('transparentwatermark')->__('Store View'),
                'title'     => Mage::helper('transparentwatermark')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
        else {
            $_fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
        }
		
		$_set_info = array(
			'base' => 'Base Image',
			'small' => 'Small Image',
			'thumbnail' => 'Thumbnail Image', 
		);
		
		foreach ($_set_info as $_field => $_name) {
			$_fieldset = $form->addFieldset($_field.'_fieldset', array('legend'=>$this->__($_name)));
			
			$_fieldset -> addField(
				$_field.'watermark', 'image',
				array(
					'name' => $_field.'_watermark',
					'label' => $this->__($_name.' Watermark'),
					'title' => $this->__($_name.' Watermark'),
				)
			);
			
			$_fieldset -> addField(
				$_field.'_position_type', 'select',
				array(
					'name' => $_field.'_position_type',
					'class' => 'position-type',
					'label' => $this->__($_name.' Position Type'),
					'title' => $this->__($_name.' Position Type'),
					'values' => Mage::helper('transparentwatermark')->getPositionType(),
				)
			);
			
			$_fieldset -> addField(
				$_field.'_default_position_type', 'select',
				array(
					'name' => $_field.'_default_position_type',
					'label' => $this->__($_name.' Default Position Type'),
					'title' => $this->__($_name.' Default Position Type'),
					'values' => Mage::helper('transparentwatermark')->getDefaultPositionType(),
				)
			);
			
			$_fieldset -> addField(
				$_field.'_custom_position_x', 'text',
				array(
					'name' => $_field.'_custom_position_x',
					'label' => $this->__($_name.' Custom Position X'),
					'title' => $this->__($_name.' Custom Position X'),
				)
			);
			
			$_fieldset -> addField(
				$_field.'_custom_position_y', 'text',
				array(
					'name' => $_field.'_custom_position_y',
					'label' => $this->__($_name.' Custom Position Y'),
					'title' => $this->__($_name.' Custom Position Y'),
				)
			);
		}
		
		if ($_twcategory->getData('entity_id')) {
            $form->addField('entity_id', 'hidden', array(
                'name' => 'twcategory_id',
            ));
			$_twcategory_data = $_twcategory->getData();
			$form->setValues($_twcategory_data);
        }
		
		$_old = $this->_getSession('old');
		if (isset($_old)) {
			unset($_old['key']);
			unset($_old['form_key']);
			$form->setValues($_old);
			$this->_unsetSession();
		}

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
	
	protected function _getSession($_label) {
		return Mage::getSingleton('transparentwatermark/session')->getData($_label);
	}
	
	protected function _unsetSession() {
		Mage::getSingleton('transparentwatermark/session')->clear();
	}
}
