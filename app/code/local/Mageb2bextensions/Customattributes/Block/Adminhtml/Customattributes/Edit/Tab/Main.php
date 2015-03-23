<?php
/**
 * Main.php
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
class Mageb2bextensions_Customattributes_Block_Adminhtml_Customattributes_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        Mage::getModel('customattributes/customattributes')->checkDatabaseInstall();
        
        $iTypeId = Mage::getModel('eav/entity')->setType('mb2b_checkout')->getTypeId();
        
        $model = Mage::registry('customattributes_data');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('catalog')->__('Attribute Properties'))
        );
        if ($model->getId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
            ));

        $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => Mage::helper('catalog')->__('Attribute Code'),
            'title' => Mage::helper('catalog')->__('Attribute Code'),
            'note'  => Mage::helper('catalog')->__('For internal use. Must be unique with no spaces'),
            'class' => 'validate-code',
            'required' => true,
        ));
        $inputTypes = array(
            array(
                'value' => 'text',
                'label' => Mage::helper('catalog')->__('Text Field')
            ),
            array(
                'value' => 'textarea',
                'label' => Mage::helper('catalog')->__('Text Area')
            ),
            array(
                'value' => 'date',
                'label' => Mage::helper('catalog')->__('Date')
            ),
            array(
                'value' => 'boolean',
                'label' => Mage::helper('catalog')->__('Yes/No')
            ),
            array(
                'value' => 'multiselect',
                'label' => Mage::helper('catalog')->__('Multiple Select')
            ),
            array(
                'value' => 'select',
                'label' => Mage::helper('catalog')->__('Dropdown')
            ),
            array(
                'value' => 'checkbox',
                'label' => Mage::helper('catalog')->__('Checkbox')
            ),
            array(
                'value' => 'radio',
                'label' => Mage::helper('catalog')->__('Radiobutton')
            ),
        );

        $response = new Varien_Object();
        $response->setTypes(array());
        //Mage::dispatchEvent('adminhtml_product_attribute_types', array('response'=>$response));

        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $iTypeId) {
            $inputTypes[] = $iTypeId;
            if (isset($iTypeId['hide_fields'])) {
                $_hiddenFields[$iTypeId['value']] = $iTypeId['hide_fields'];
            }
            if (isset($iTypeId['disabled_types'])) {
                $_disabledTypes[$iTypeId['value']] = $iTypeId['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);


        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
#            'label' => Mage::helper('catalog')->__('Catalog Input Type for Store Owner'),
            'label' => Mage::helper('catalog')->__('Input Type'),
            'title' => Mage::helper('catalog')->__('Input Type'),
            'value' => 'text',
            'values'=> $inputTypes
        ));
        
        $fieldset->addField('frontend_class', 'select', array(
            'name'  => 'frontend_class',
#            'label' => Mage::helper('catalog')->__('Input Validation for Store Owner'),
            'label' => Mage::helper('catalog')->__('Input Validation'),
            'title' => Mage::helper('catalog')->__('Input Validation'),
            'values'=>  array(
                array(
                    'value' => '',
                    'label' => Mage::helper('catalog')->__('None')
                ),
                array(
                    'value' => 'validate-number',
                    'label' => Mage::helper('catalog')->__('Decimal Number')
                ),
                array(
                    'value' => 'validate-digits',
                    'label' => Mage::helper('catalog')->__('Integer Number')
                ),
                array(
                    'value' => 'validate-email',
                    'label' => Mage::helper('catalog')->__('Email')
                ),
                array(
                    'value' => 'validate-url',
                    'label' => Mage::helper('catalog')->__('Url')
                ),
                array(
                    'value' => 'validate-alpha',
                    'label' => Mage::helper('catalog')->__('Letters')
                ),
                array(
                    'value' => 'validate-alphanum',
                    'label' => Mage::helper('catalog')->__('Letters(a-zA-Z) or Numbers(0-9)')
                ),
            )
        ));

        $fieldset->addField('is_filterable', 'select', array(
            'name'  => 'is_filterable',
            'label' => Mage::helper('catalog')->__('Attribute Placeholder'),
            'title' => Mage::helper('catalog')->__('Attribute Placeholder'),
            'note'  => Mage::helper('catalog')->__('If you choose "On Top", the attribute will be displayed in the top placeholder of the checkout step and vice versa if you choose "At the Bottom"'),
            'required' => true,
            'values'=>  array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('catalog')->__('On Top')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('catalog')->__('At the bottom')
                ),
            )
        ));

        $fieldset->addField('position', 'text', array(
            'name'  => 'position',
            'label' => Mage::helper('catalog')->__('Position in Placeholder'),
            'title' => Mage::helper('catalog')->__('Position in Placeholder'),
            'note' => Mage::helper('catalog')->__('Can be used to manage attributes\' positions when there are more than one attribute in one placeholder'),
            'class' => 'validate-digits',
        ));
        
        $fieldset->addField('entity_type_id', 'hidden', array(
            'name' => 'entity_type_id',
            'value' => $iTypeId
        ));
        
        
        
        $fieldset->addField('is_user_defined', 'hidden', array(
            'name' => 'is_user_defined',
            'value' => 1
        ));
 
 /************    START CUSTOM ATTRIBUTES          ************/

        $fieldset->addField('default_value_text', 'text', array(
            'name' => 'default_value_text',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'value' => $model->getDefaultValue(),
        ));

        $fieldset->addField('default_value_yesno', 'select', array(
            'name' => 'default_value_yesno',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'values' => $yesno,
            'value' => $model->getDefaultValue(),
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('default_value_date', 'date', array(
            'name'   => 'default_value_date',
            'label'  => Mage::helper('catalog')->__('Default value'),
            'title'  => Mage::helper('catalog')->__('Default value'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'value'  => $model->getDefaultValue(),
            'format'       => $dateFormatIso
        ));

        $fieldset->addField('default_value_textarea', 'textarea', array(
            'name' => 'default_value_textarea',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'value' => $model->getDefaultValue(),
        ));
        
        
        // for customer registration
        
        $fieldset->addField('is_used_for_price_rules', 'select', array(
            'name'  => 'is_used_for_price_rules',
            'label' => Mage::helper('catalog')->__('Step (for customer reg)'),
            'title' => Mage::helper('catalog')->__('Step (for customer reg)'),
            'note'  => Mage::helper('catalog')->__('Add the attribute to the customer registration'),
            'values'=> Mage::helper('customattributes')->getStepData('registerpage'),
        ));
				
 /************    FINISH CUSTOM ATTRIBUTES          ************/

        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => Mage::helper('catalog')->__('Values Required'),
            'title' => Mage::helper('catalog')->__('Values Required'),
            'values' => $yesno,
        ));

        if ($model->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);

            if (isset($disableAttributeFields[$model->getAttributeCode()])) {
                foreach ($disableAttributeFields[$model->getAttributeCode()] as $field) {
                    $form->getElement($field)->setDisabled(1);
                }
            }
        }

        //var_dump($model->getData());exit;
        $form->addValues($model->getData());
        
                

        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'apply' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_apply')
        );
    }

}
