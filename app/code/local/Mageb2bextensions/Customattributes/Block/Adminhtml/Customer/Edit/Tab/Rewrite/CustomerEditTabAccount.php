<?php
/**
 * CustomerEditTabAccount.php
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
class Mageb2bextensions_Customattributes_Block_Adminhtml_Customer_Edit_Tab_Rewrite_CustomerEditTabAccount extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    public function initForm()
    {
			  $valuestoset = array();
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_account');
        $form->setFieldNameSuffix('account');

        $customer = Mage::registry('current_customer');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('customer')->__('Account Information'))
        );


        $this->_setFieldset($customer->getAttributes(), $fieldset);

        if ($customer->getId()) {
            $form->getElement('website_id')->setDisabled('disabled');
            $form->getElement('created_in')->setDisabled('disabled');
        } else {
            $fieldset->removeField('created_in');
        }

        $form->getElement('email')->addClass('validate-email');

//        if (Mage::app()->isSingleStoreMode()) {
//            $fieldset->removeField('website_id');
//            $fieldset->addField('website_id', 'hidden', array(
//                'name'      => 'website_id'
//            ));
//            $customer->setWebsiteId(Mage::app()->getStore(true)->getWebsiteId());
//        }
 //START FOR CODE ADDED FOR CUSTOM ATTRIBUTES
		if ($aFieldList = Mage::getModel('customattributes/customattributes')->getCheckoutAtrributeList(Mage::helper('customattributes')->getStepId('customer'), 1, 'registerpage')) {
					$resource = Mage::getSingleton('core/resource');
					$valuestosetformulti="";
					$finalradiovalue="";
					$finaldate="";
					$finaldropdownvalue="";
					$boxischeck = false;
			    $read = $resource->getConnection('core_read');
					$newFieldset = $form->addFieldset(
							'custom_registration_fieldset',
							array('legend'=>Mage::helper('customer')->__('Custom Registration Fields At Top'))
					);
					#print_r($aFieldList);
						foreach ($aFieldList as $aFieldListValue) {
							if($aFieldListValue['frontend_input'] == "multiselect" || $aFieldListValue['frontend_input'] == "checkbox") {
									
									$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
									$valuestosetformulti="";
									$rows = $read->fetchAll($select_qry);
									foreach($rows as $data)
									 { 
									 			 $individualmultiselectvalues = explode(",",$data['value']);
												 foreach($individualmultiselectvalues as $multiselectdata)
												 { 
															$multiselectvalues[] = (array(
																	'label' => (string) $multiselectdata, 
																	'value' => $multiselectdata
															));
															if($aFieldListValue['frontend_input'] == "checkbox" && $multiselectdata!="") {
																$boxischeck = true;
															}
															$valuestosetformulti .= $multiselectdata .","; 
												 }
									$valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => substr_replace($valuestosetformulti,"",-1)); 
									#$sFieldValue = Mage::getModel('customattributes/customattributes')->getOptionValues($aFieldListValue['attribute_id']);
									$sFieldValue = Mage::getResourceModel('eav/entity_attribute_option_collection')
																			->setAttributeFilter($aFieldListValue['attribute_id'])
																			->setStoreFilter()
																			->load();
									#print_r($sFieldValue);
									  foreach($sFieldValue as $item)
									  { 
											#$finalvaluesfordisplay .= $individualvalues . ",";
											$finalvaluesfordisplay[] = (array(
																			'label' => (string) $item->getValue(), 
																			'value' => $item->getId()
																	));
									  }
										
										if($aFieldListValue['frontend_input'] == "checkbox") {
												#echo "T: " . $aFieldListValue['frontend_label'];
												$i=1;
												$field = $newFieldset->addField('label', 'label', array('label' => $aFieldListValue['frontend_label'], 'values' => '')); 
												
												foreach($sFieldValue as $item)
									 		 { 
											   #print_r($item);
												 #print_r($valuestosetformulti);
												 #echo "ID: " . (string)$item->getId();
												 $stufftoforeach = explode(",",$valuestosetformulti);
													$newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'_'.$i.'', $aFieldListValue['frontend_input'], array(
															'name' => 'mb2b_checkout_'.$aFieldListValue['attribute_id'].'_'.(string)$item->getId().'', 'label' => (string)$item->getValue(), 'values' => $finalvaluesfordisplay
													)); 
													foreach($stufftoforeach as $itemfromdb)
									 		   { 
												 		#echo "ID: " . $itemfromdb;
														if($itemfromdb == $item->getId()) {
														$form->getElement('mb2b_checkout_'.$aFieldListValue['attribute_id'].'_'.$i.'')->setIsChecked(true);
														#$form->getElement('mb2b_checkout_'.$aFieldListValue['attribute_id'].'_'.$i.'')->setValues(array('107'));
														}
													}
													$i++;
												}
										} else {
										/*
											$field = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', $aFieldListValue['frontend_input'], array(
													'name' => 'mb2b_checkout_'.$aFieldListValue['attribute_id'].'', 'label' => $aFieldListValue['frontend_label'], 'values' => $finalvaluesfordisplay
											)); 
											*/
										}
									}
									
							} else {
							
							
									
								if($aFieldListValue['frontend_input'] == "date") {
									
								$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
								$rows = $read->fetchAll($select_qry);
									foreach($rows as $data)
									 { 
									 		$finaldate = $data['value'];
									 }
										$datefield = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', $aFieldListValue['frontend_input'], array(
												'name'      => 'mb2b_checkout_'.$aFieldListValue['attribute_id'].'',
												'label'     => Mage::helper('customattributes')->__($aFieldListValue['frontend_label']),
												'title'     => Mage::helper('customattributes')->__($aFieldListValue['frontend_label']),
												'image'     => $this->getSkinUrl('images/grid-cal.gif'),
												'format'    => 'yy/d/M',
										));
										$valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => $finaldate); 
										
									} else if ($aFieldListValue['frontend_input'] == "radio") {
									
									$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
									$rowsradio = $read->fetchAll($select_qry);
									foreach($rowsradio as $dataradio)
									 { 
									  $finalradiovalue = $dataradio['value'];
									 }
											#echo "RADIO mb2b_checkout_".$aFieldListValue['attribute_id'] . " - " . $data['value'];
											$sFieldValue = Mage::getResourceModel('eav/entity_attribute_option_collection')
																		->setAttributeFilter($aFieldListValue['attribute_id'])
																		->setStoreFilter()
																		->load();
											foreach($sFieldValue as $item)
											{ 
												$finalvaluesfordisplayradio[] = (array(
																				'label' => (string) $item->getValue(), 
																				'value' => $item->getId()
																				
																		));
											}
										 #print_r($finalvaluesfordisplayradio);
										$field = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', 'radios', array(
												'name' => 'account[mb2b_checkout_'.$aFieldListValue['attribute_id'].']', 'label' => $aFieldListValue['frontend_label'], 'values' => $finalvaluesfordisplayradio
										)); 
										$valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => $finalradiovalue); 
									  #$form->getElement('mb2b_checkout_'.$aFieldListValue['attribute_id'].'')->setIsChecked(true);
									} else if ($aFieldListValue['frontend_input'] == "select") {
									
									$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
									$finaldropdownvalue="";
									$finalvaluesfordisplaydropdown=array();
									$rowsselectdropdown = $read->fetchAll($select_qry);
									foreach($rowsselectdropdown as $datadropdown)
									 { 
									  $finaldropdownvalue = $datadropdown['value'];
									 }
											#echo "RADIO mb2b_checkout_".$aFieldListValue['attribute_id'] . " - " . $data['value'];
											$sFieldValue = Mage::getResourceModel('eav/entity_attribute_option_collection')
																		->setAttributeFilter($aFieldListValue['attribute_id'])
																		->setStoreFilter()
																		->load();
											foreach($sFieldValue as $item)
											{ 
												$finalvaluesfordisplaydropdown[] = (array(
																				'label' => (string) $item->getValue(), 
																				'value' => $item->getId()
																				
																		));
											}
										 #print_r($finalvaluesfordisplayradio);
										$field = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', 'select', array(
												'name' => 'account[mb2b_checkout_'.$aFieldListValue['attribute_id'].']', 'label' => $aFieldListValue['frontend_label'], 'values' => $finalvaluesfordisplaydropdown
										)); 
										$valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => $finaldropdownvalue); 
									  #$form->getElement('mb2b_checkout_'.$aFieldListValue['attribute_id'].'')->setIsChecked(true);
									} else if ($aFieldListValue['frontend_input'] == "boolean") {
									
									
									} else {
									
										$field = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', $aFieldListValue['frontend_input'], array(
												'name' => 'mb2b_checkout_'.$aFieldListValue['attribute_id'].'', 'label' => $aFieldListValue['frontend_label'],
										)); 
										
										$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
									 
										$rows = $read->fetchAll($select_qry);
										foreach($rows as $data)
										 { 
										 $valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => $data['value']); 
										 }
									}
									 
							}
					}
		}
		//part 2
		if ($aFieldList = Mage::getModel('customattributes/customattributes')->getCheckoutAtrributeList(Mage::helper('customattributes')->getStepId('customer'), 2, 'registerpage')) {
					$resource = Mage::getSingleton('core/resource');
					$valuestosetformulti="";
					$finalradiovalue="";
					$finaldate="";
					$finaldropdownvalue="";
					$boxischeck = false;
			    $read = $resource->getConnection('core_read');
					$newFieldset = $form->addFieldset(
							'custom_registration_fieldset1',
							array('legend'=>Mage::helper('customer')->__('Custom Registration Fields At Bottom'))
					);
					#print_r($aFieldList);
						foreach ($aFieldList as $aFieldListValue) {
							if($aFieldListValue['frontend_input'] == "multiselect" || $aFieldListValue['frontend_input'] == "checkbox") {
									
									$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
									$valuestosetformulti="";
									$rows = $read->fetchAll($select_qry);
									foreach($rows as $data)
									 { 
									 			 $individualmultiselectvalues = explode(",",$data['value']);
												 foreach($individualmultiselectvalues as $multiselectdata)
												 { 
															$multiselectvalues[] = (array(
																	'label' => (string) $multiselectdata, 
																	'value' => $multiselectdata
															));
															if($aFieldListValue['frontend_input'] == "checkbox" && $multiselectdata!="") {
																$boxischeck = true;
															}
															$valuestosetformulti .= $multiselectdata .","; 
												 }
									$valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => substr_replace($valuestosetformulti,"",-1)); 
									#$sFieldValue = Mage::getModel('customattributes/customattributes')->getOptionValues($aFieldListValue['attribute_id']);
									$sFieldValue = Mage::getResourceModel('eav/entity_attribute_option_collection')
																			->setAttributeFilter($aFieldListValue['attribute_id'])
																			->setStoreFilter()
																			->load();
									#print_r($sFieldValue);
									  foreach($sFieldValue as $item)
									  { 
											#$finalvaluesfordisplay .= $individualvalues . ",";
											$finalvaluesfordisplay[] = (array(
																			'label' => (string) $item->getValue(), 
																			'value' => $item->getId()
																	));
									  }
										
										if($aFieldListValue['frontend_input'] == "checkbox") {
												#echo "T: " . $aFieldListValue['frontend_label'];
												$i=1;
												if($aFieldListValue['frontend_label']=="") {
												$field = $newFieldset->addField('label', 'label', array('label' => $aFieldListValue['frontend_label'], 'values' => '')); 
												}
												foreach($sFieldValue as $item)
									 		 { 
											   #print_r($item);
												 #print_r($valuestosetformulti);
												 #echo "ID: " . (string)$item->getId();
												 $stufftoforeach = explode(",",$valuestosetformulti);
													$newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'_'.$i.'', $aFieldListValue['frontend_input'], array(
															'name' => 'mb2b_checkout_'.$aFieldListValue['attribute_id'].'_'.(string)$item->getId().'', 'label' => (string)$item->getValue(), 'values' => $finalvaluesfordisplay
													)); 
													foreach($stufftoforeach as $itemfromdb)
									 		   { 
												 		#echo "ID: " . $itemfromdb;
														if($itemfromdb == $item->getId()) {
														$form->getElement('mb2b_checkout_'.$aFieldListValue['attribute_id'].'_'.$i.'')->setIsChecked(true);
														#$form->getElement('mb2b_checkout_'.$aFieldListValue['attribute_id'].'_'.$i.'')->setValues(array('107'));
														}
													}
													$i++;
												}
										} else {
										/*
											$field = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', $aFieldListValue['frontend_input'], array(
													'name' => 'mb2b_checkout_'.$aFieldListValue['attribute_id'].'', 'label' => $aFieldListValue['frontend_label'], 'values' => $finalvaluesfordisplay
											)); 
											*/
										}
									}
									
							} else {
							
							
									
								if($aFieldListValue['frontend_input'] == "date") {
									
								$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
								$rows = $read->fetchAll($select_qry);
									foreach($rows as $data)
									 { 
									 		$finaldate = $data['value'];
									 }
										$datefield = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', $aFieldListValue['frontend_input'], array(
												'name'      => 'mb2b_checkout_'.$aFieldListValue['attribute_id'].'',
												'label'     => Mage::helper('customattributes')->__($aFieldListValue['frontend_label']),
												'title'     => Mage::helper('customattributes')->__($aFieldListValue['frontend_label']),
												'image'     => $this->getSkinUrl('images/grid-cal.gif'),
												'format'    => 'yy/d/M',
										));
										$valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => $finaldate); 
										
									} else if ($aFieldListValue['frontend_input'] == "radio") {
									
									$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
									$rowsradio = $read->fetchAll($select_qry);
									foreach($rowsradio as $dataradio)
									 { 
									  $finalradiovalue = $dataradio['value'];
									 }
											#echo "RADIO mb2b_checkout_".$aFieldListValue['attribute_id'] . " - " . $data['value'];
											$sFieldValue = Mage::getResourceModel('eav/entity_attribute_option_collection')
																		->setAttributeFilter($aFieldListValue['attribute_id'])
																		->setStoreFilter()
																		->load();
											foreach($sFieldValue as $item)
											{ 
												$finalvaluesfordisplayradio[] = (array(
																				'label' => (string) $item->getValue(), 
																				'value' => $item->getId()
																				
																		));
											}
										 #print_r($finalvaluesfordisplayradio);
										$field = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', 'radios', array(
												'name' => 'account[mb2b_checkout_'.$aFieldListValue['attribute_id'].']', 'label' => $aFieldListValue['frontend_label'], 'values' => $finalvaluesfordisplayradio
										)); 
										$valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => $finalradiovalue); 
									  #$form->getElement('mb2b_checkout_'.$aFieldListValue['attribute_id'].'')->setIsChecked(true);
									} else if ($aFieldListValue['frontend_input'] == "select") {
									
									$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
									$finaldropdownvalue="";
									$finalvaluesfordisplaydropdown=array();
									$rowsselectdropdown = $read->fetchAll($select_qry);
									foreach($rowsselectdropdown as $datadropdown)
									 { 
									  $finaldropdownvalue = $datadropdown['value'];
									 }
											#echo "RADIO mb2b_checkout_".$aFieldListValue['attribute_id'] . " - " . $data['value'];
											$sFieldValue = Mage::getResourceModel('eav/entity_attribute_option_collection')
																		->setAttributeFilter($aFieldListValue['attribute_id'])
																		->setStoreFilter()
																		->load();
											foreach($sFieldValue as $item)
											{ 
												$finalvaluesfordisplaydropdown[] = (array(
																				'label' => (string) $item->getValue(), 
																				'value' => $item->getId()
																				
																		));
											}
										 #print_r($finalvaluesfordisplayradio);
										$field = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', 'select', array(
												'name' => 'account[mb2b_checkout_'.$aFieldListValue['attribute_id'].']', 'label' => $aFieldListValue['frontend_label'], 'values' => $finalvaluesfordisplaydropdown
										)); 
										$valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => $finaldropdownvalue); 
									  #$form->getElement('mb2b_checkout_'.$aFieldListValue['attribute_id'].'')->setIsChecked(true);
									} else if ($aFieldListValue['frontend_input'] == "boolean") {
									
									
									} else {
									
										$field = $newFieldset->addField('mb2b_checkout_'.$aFieldListValue['attribute_id'].'', $aFieldListValue['frontend_input'], array(
												'name' => 'mb2b_checkout_'.$aFieldListValue['attribute_id'].'', 'label' => $aFieldListValue['frontend_label'],
										)); 
										
										$select_qry = "select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$aFieldListValue['attribute_id']."'";
									 
										$rows = $read->fetchAll($select_qry);
										foreach($rows as $data)
										 { 
										 $valuestoset += array('mb2b_checkout_'.$aFieldListValue['attribute_id'].'' => $data['value']); 
										 }
									}
									 
							}
					}
		}
		//end part 2
 //END FOR CODE ADDED FOR CUSTOM ATTRIBUTES
				
        if ($customer->getId()) {
            if (!$customer->isReadonly()) {
                // add password management fieldset
                $newFieldset = $form->addFieldset(
                    'password_fieldset',
                    array('legend'=>Mage::helper('customer')->__('Password Management'))
                );
                // New customer password
                $field = $newFieldset->addField('new_password', 'text',
                    array(
                        'label' => Mage::helper('customer')->__('New Password'),
                        'name'  => 'new_password',
                        'class' => 'validate-new-password'
                    )
                );
                $field->setRenderer($this->getLayout()->createBlock('adminhtml/customer_edit_renderer_newpass'));

                // prepare customer confirmation control (only for existing customers)
                $confirmationKey = $customer->getConfirmation();
                if ($confirmationKey || $customer->isConfirmationRequired()) {
                    $confirmationAttribute = $customer->getAttribute('confirmation');
                    if (!$confirmationKey) {
                        $confirmationKey = $customer->getRandomConfirmationKey();
                    }
                    $element = $fieldset->addField('confirmation', 'select', array(
                        'name'  => 'confirmation',
                        'label' => Mage::helper('customer')->__($confirmationAttribute->getFrontendLabel()),
                    ))->setEntityAttribute($confirmationAttribute)
                        ->setValues(array('' => 'Confirmed', $confirmationKey => 'Not confirmed'));

                    // prepare send welcome email checkbox, if customer is not confirmed
                    // no need to add it, if website id is empty
                    if ($customer->getConfirmation() && $customer->getWebsiteId()) {
                        $fieldset->addField('sendemail', 'checkbox', array(
                            'name'  => 'sendemail',
                            'label' => Mage::helper('customer')->__('Send Welcome Email after Confirmation')
                        ));
                    }
                }
            }
        }
        else {
            $newFieldset = $form->addFieldset(
                'password_fieldset',
                array('legend'=>Mage::helper('customer')->__('Password Management'))
            );
            $field = $newFieldset->addField('password', 'text',
                array(
                    'label' => Mage::helper('customer')->__('Password'),
                    'class' => 'input-text required-entry validate-password',
                    'name'  => 'password',
                    'required' => true
                )
            );
            $field->setRenderer($this->getLayout()->createBlock('adminhtml/customer_edit_renderer_newpass'));

            // prepare send welcome email checkbox
            $fieldset->addField('sendemail', 'checkbox', array(
                'label' => Mage::helper('customer')->__('Send welcome email'),
                'name'  => 'sendemail',
                'id'    => 'sendemail',
            ));
        }

        // make sendemail disabled, if website_id has empty value
        if ($sendemail = $form->getElement('sendemail')) {
            $prefix = $form->getHtmlIdPrefix();
            $sendemail->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                $('{$prefix}website_id').disableSendemail = function() {
                    $('{$prefix}sendemail').disabled = ('' == this.value || '0' == this.value);
                }.bind($('{$prefix}website_id'));
                Event.observe('{$prefix}website_id', 'click', $('{$prefix}website_id').disableSendemail);
                $('{$prefix}website_id').disableSendemail();
                "
                . '</script>'
            );
        }

        if ($customer->isReadonly()) {
            foreach ($customer->getAttributes() as $attribute) {
                $element = $form->getElement($attribute->getAttributeCode());
                if ($element) {
                    $element->setReadonly(true, true);
                }
            }
        }
				$finalarraytoset = array_merge($customer->getData(),$valuestoset); //ADDED FOR CUSTOM ATTRIBUTES
        $form->setValues($finalarraytoset);
        $this->setForm($form);
        return $this;
    }
}
