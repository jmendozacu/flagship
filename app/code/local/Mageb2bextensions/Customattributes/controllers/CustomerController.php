<?php
/**
 * CustomerController.php
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
class Mageb2bextensions_Customattributes_CustomerController extends Mage_Adminhtml_Controller_Action
{
    protected $_sCustomCustomerAttrTable    = 'mb2b_customer_entity_custom';
		
		protected function _initCustomer($idFieldName = 'id')
    {
        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    /**
     * Save customer action
     */
    public function saveAction()
    {
				
       
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            $redirectBack   = $this->getRequest()->getParam('back', false);
            $this->_initCustomer('customer_id');
            /** @var Mage_Customer_Model_Customer */
            $customer = Mage::registry('current_customer');
            // Prepare customer saving data
            if (isset($data['account'])) {
                if (isset($data['account']['email'])) {
                    $data['account']['email'] = trim($data['account']['email']);
                }
                $customer->addData($data['account']);
            }
            // unset template data
            if (isset($data['address']['_template_'])) {
                unset($data['address']['_template_']);
            }

            $modifiedAddresses = array();

            if (! empty($data['address'])) {
                foreach ($data['address'] as $index => $addressData) {
                    if (($address = $customer->getAddressItemById($index))) {
                        $addressId           = $index;
                        $modifiedAddresses[] = $index;
                    } else {
                        $address   = Mage::getModel('customer/address');
                        $addressId = null;
                        $customer->addAddress($address);
                    }

										 #NEED BUG FIX HERE MAYBE
										 #a:5:{i:0;s:69:"Item (Mage_Customer_Model_Address) with the same id "1" already exist";i:1;s:1284
										 #2 C:\website\bagitgorgeous\app\code\local\Mageb2bextensions\Customattributes\controllers\CustomerController.php(67): 
                    $address->setData($addressData)
                            ->setId($addressId)
                            ->setPostIndex($index); // We need set post_index for detect default addresses
                }
            }
            // not modified customer addresses mark for delete
            foreach ($customer->getAddressesCollection() as $customerAddress) {
                if ($customerAddress->getId() && ! in_array($customerAddress->getId(), $modifiedAddresses)) {
                    $customerAddress->setData('_deleted', true);
                }
            }

            if(isset($data['subscription'])) {
                $customer->setIsSubscribed(true);
            } else {
                $customer->setIsSubscribed(false);
            }

            $isNewCustomer = !$customer->getId();
            try {
                if ($customer->getPassword() == 'auto') {
                    $sendPassToEmail = true;
                    $customer->setPassword($customer->generatePassword());
                }

                // force new customer active
                if ($isNewCustomer) {
                    $customer->setForceConfirmed(true);
                }

                Mage::dispatchEvent('adminhtml_customer_prepare_save',
                    array('customer' => $customer, 'request' => $this->getRequest())
                );

				 $customer->save();

				 		// CUSTOM CODE START
						$postData = $this->getRequest()->getPost();
						$oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
           
						#print_r($postData);
           if (!$isNewCustomer) {
					 	$finalpart3value="";
						$postpart3FieldId="";
            foreach ($postData['account'] as $postFieldName => $postValue)
            {
                if (strpos($postFieldName, 'b2b_checkout_'))
                {
                    $postNameParts = explode('_', $postFieldName);
                    $postFieldId = $postNameParts[2];
										#echo "T: " . $postValue;
										
										if(isset($postNameParts[3]) && $postNameParts[3] != "") {
												//buildup selected values
												$postpart3FieldId = $postNameParts[2];
												$finalpart3value .= $postNameParts[3]. ",";
										} else {
										
											if(is_array($postValue)) {
													#print_r($postValue);
													$finalindividualvalue="";
													foreach($postValue as $individualvalue)
													{ 
														$finalindividualvalue .= $individualvalue . ",";
													}
													$aDBInfo = array
													(
															'value'         => substr_replace($finalindividualvalue,"",-1),
													);
													$resource = Mage::getSingleton('core/resource');
													$read = $resource->getConnection('core_read');
													$select_qry=$read->query("select value from mb2b_customer_entity_custom where entity_id = '".$postData['customer_id']."' and attribute_id = '".$postFieldId."'");
													$row = $select_qry->fetch();
													$attributeDataValue = $row['value'];
													if($attributeDataValue !="") {
														$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $postData['customer_id'] . ' AND attribute_id = ' . $postFieldId);
													} else {
														#if($row['value'] !="") {
															$aDBInfo = array
															(
																	'entity_id'     => $postData['customer_id'],
																	'attribute_id'  => $postFieldId,
																	'value'         => $postValue,
															);
															$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
														#}
													}
															
											} else {
													/*
													$aDBInfo = array
													(
															'entity_id'     => $postData['customer_id'],
															'attribute_id'  => $postFieldId,
															'value'         => $postValue,
													);
													*/
													$aDBInfo = array
													(
															'value'         => $postValue,
													);
													#$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
													if(!$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $postData['customer_id'] . ' AND attribute_id = ' . $postFieldId)) {
														
														 #if($postValue == "") { $postValue = "empty"; }
														 
															$aDBInfo = array
															(
																	'entity_id'     => $postData['customer_id'],
																	'attribute_id'  => $postFieldId,
																	'value'         => $postValue,
															);
															//fix for when user was created outside of frontend reg.. then there is no IDs in db for you to "update" thus need to insert
															#$rows = $read->fetchAll($select_qry);
														  $resource = Mage::getSingleton('core/resource');
														  $read = $resource->getConnection('core_read');
							 								$select_qry=$read->query("select value from mb2b_customer_entity_custom where entity_id = '".$postData['customer_id']."' and attribute_id = '".$postFieldId."'");
															$row = $select_qry->fetch();
															$attributeDataValue = $row['value'];
															if($attributeDataValue !="") {
																$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $postData['customer_id'] . ' AND attribute_id = ' . $postFieldId);
															} else {
																#echo "VALUE: " . $row['value'];
																#echo "VALUE: " . $postValue;
																#print_r($aDBInfo);
																if($row['value'] !="" || $postValue !="") {
																	$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
																}
															}
															#$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
														  #$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $postData['customer_id'] . ' AND attribute_id = ' . $postFieldId);
													}
													#exit;
											}
										}
                } else {
								
									if(is_array($postValue)) {
													#print_r($postValue);
													#echo "T: " . $postData['customer_id'];
													$finalindividualvalue="";
													foreach($postValue as $iKeyName => $individualvalue)
													{ 
														#echo "D: " . $iKeyName;
														$postNameParts = explode('_', $iKeyName);
														$postFieldId = $postNameParts[2];
														$finalindividualvalue .= $individualvalue . ",";
														$aDBInfo = array
														(
																'value'         => substr_replace($finalindividualvalue,"",-1),
														);
														$resource = Mage::getSingleton('core/resource');
														$read = $resource->getConnection('core_read');
														$select_qry=$read->query("select value from mb2b_customer_entity_custom where entity_id = '".$postData['customer_id']."' and attribute_id = '".$postFieldId."'");
														$row = $select_qry->fetch();
														$attributeDataValue = $row['value'];
														if($attributeDataValue !="") {
																$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $postData['customer_id'] . ' AND attribute_id = ' . $postFieldId);
														} else {
															#if($row['value'] !="") {
																$aDBInfo = array
																(
																		'entity_id'     => $postData['customer_id'],
																		'attribute_id'  => $postFieldId,
																		'value'         => $postValue,
																);
																$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
															#}
														}
													}
													#exit;
													
									}
								
								}
            }
						if($finalpart3value !="") {
							#echo "T: " .$finalpart3value;
							$aDBInfo2 = array
							(
									'value'         => $finalpart3value,
							);
							$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo2, 'entity_id = ' . $postData['customer_id'] . ' AND attribute_id = ' . $postpart3FieldId);
							
						}
					}else{
						$customerBottom =  $customer->getData('entity_id');
						$finalpart3value="";
						$postpart3FieldId="";

            foreach ($postData['account'] as $postFieldName => $postValue)
            {
                if (strpos($postFieldName, 'b2b_checkout_'))
                {
                    $postNameParts = explode('_', $postFieldName);
                     $postFieldId = $postNameParts[2];
                    //print_r($postData['account']);exit;
										#echo "T: " . $postValue;
										
										if(isset($postNameParts[3]) && $postNameParts[3] != "") {
												//buildup selected values
												$postpart3FieldId = $postNameParts[2];
												$finalpart3value .= $postNameParts[3]. ",";
										} else {
											if(is_array($postValue)) {

													#print_r($postValue);
													$finalindividualvalue="";
													foreach($postValue as $individualvalue)
													{ 
														$finalindividualvalue .= $individualvalue . ",";
													}
													$aDBInfo = array
													(
															'value'         => substr_replace($finalindividualvalue,"",-1),
													);
													$resource = Mage::getSingleton('core/resource');
													$read = $resource->getConnection('core_read');
												    $select_qry=$read->query("select value from mb2b_customer_entity_custom where entity_id = '".$customerBottom."' and attribute_id = '".$postFieldId."'");
													$row = $select_qry->fetch();
													
													$attributeDataValue = $row['value'];
													if($attributeDataValue !="") {
														$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $customerBottom . ' AND attribute_id = ' . $postFieldId);
													} else {
														#if($row['value'] !="") {
															$aDBInfo = array
															(
																	'entity_id'     => $customerBottom,
																	'attribute_id'  => $postFieldId,
																	'value'         => $postValue,
															);
															$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
														#}
													}
															
											} else {
													/*
													$aDBInfo = array
													(
															'entity_id'     => $customerBottom,
															'attribute_id'  => $postFieldId,
															'value'         => $postValue,
													);
													*/
													$aDBInfo = array
													(
															'value'         => $postValue,
													);
													#$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
													if(!$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $customerBottom . ' AND attribute_id = ' . $postFieldId)) {
														
														 #if($postValue == "") { $postValue = "empty"; }
														 
															$aDBInfo = array
															(
																	'entity_id'     => $customerBottom,
																	'attribute_id'  => $postFieldId,
																	'value'         => $postValue,
															);
															//fix for when user was created outside of frontend reg.. then there is no IDs in db for you to "update" thus need to insert
															#$rows = $read->fetchAll($select_qry);
														  $resource = Mage::getSingleton('core/resource');
														  $read = $resource->getConnection('core_read');
							 								$select_qry=$read->query("select value from mb2b_customer_entity_custom where entity_id = '".$customerBottom."' and attribute_id = '".$postFieldId."'");
															$row = $select_qry->fetch();
															$attributeDataValue = $row['value'];
															if($attributeDataValue !="") {
																$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $customerBottom . ' AND attribute_id = ' . $postFieldId);
															} else {
																#echo "VALUE: " . $row['value'];
																#echo "VALUE: " . $postValue;
																#print_r($aDBInfo);
																if($row['value'] !="" || $postValue !="") {
																	$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
																}
															}
															#$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
														  #$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $customerBottom . ' AND attribute_id = ' . $postFieldId);
													}
													#exit;
											}
										}
                } else {
								
									if(is_array($postValue)) {

													#print_r($postValue);
													#echo "T: " . $customerBottom;
													$finalindividualvalue="";
													foreach($postValue as $iKeyName => $individualvalue)
													{ 
														#echo "D: " . $iKeyName;
														$postNameParts = explode('_', $iKeyName);
														$postFieldId = $postNameParts[2];
														$finalindividualvalue .= $individualvalue . ",";
														$aDBInfo = array
														(
																'value'         => substr_replace($finalindividualvalue,"",-1),
														);
														$resource = Mage::getSingleton('core/resource');
														$read = $resource->getConnection('core_read');
														$select_qry=$read->query("select value from mb2b_customer_entity_custom where entity_id = '".$customerBottom."' and attribute_id = '".$postFieldId."'");
														$row = $select_qry->fetch();

														$attributeDataValue = $row['value'];
														if($attributeDataValue !="") {
																$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $customerBottom . ' AND attribute_id = ' . $postFieldId);
														} else {
															#if($row['value'] !="") {
																$aDBInfo = array
																(
																		'entity_id'     => $customerBottom,
																		'attribute_id'  => $postFieldId,
																		'value'         => $postValue,
																);
																$oDb->insert('mb2b_customer_entity_custom', $aDBInfo);
															#}
														}
													}
													#exit;
													
									}
								
								}
            }
						if($finalpart3value !="") {
							#echo "T: " .$finalpart3value;
							$aDBInfo2 = array
							(
									'value'         => $finalpart3value,
							);
							$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo2, 'entity_id = ' . $customerBottom . ' AND attribute_id = ' . $postpart3FieldId);
							
						}

					} //end check for if new customer
						#exit;
						// CUSTOM CODE END
               
                // send welcome email
                if ($customer->getWebsiteId() && ($customer->hasData('sendemail') || isset($sendPassToEmail))) {
                    $storeId = $customer->getSendemailStoreId();
                    if ($isNewCustomer) {
                        $customer->sendNewAccountEmail('registered', '', $storeId);
                    }
                    // confirm not confirmed customer
                    elseif ((!$customer->getConfirmation())) {
                        $customer->sendNewAccountEmail('confirmed', '', $storeId);
                    }
                }

                // TODO? Send confirmation link, if deactivating account

                if ($newPassword = $customer->getNewPassword()) {
                    if ($newPassword == 'auto') {
                        $newPassword = $customer->generatePassword();
                    }
                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The customer has been saved.'));
                Mage::dispatchEvent('adminhtml_customer_save_after',
                    array('customer' => $customer, 'request' => $this->getRequest())
                );

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id'    => $customer->getId(),
                        '_current'=>true
                    ));
                    return;
                }
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id'=>$customer->getId())));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/customer'));
				
		}


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/manage');
    }
		
    protected function _filterPostData($data)
    {
        $data['account'] = $this->_filterDates($data['account'], array('dob'));
        return $data;
    }
}
