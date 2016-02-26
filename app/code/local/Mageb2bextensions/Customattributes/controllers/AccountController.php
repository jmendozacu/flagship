<?php
/**
 * AccountController.php
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
class Mageb2bextensions_Customattributes_AccountController extends Mage_Core_Controller_Front_Action
{
    protected $_sCustomCustomerAttrTable    = 'mb2b_customer_entity_custom';

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function createPostAction()
    {
				
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
                if ($node->is('create') && ($value = $this->getRequest()->getParam($code)) !== null) {
                    $customer->setData($code, $value);
                }
            }

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                $address = Mage::getModel('customer/address')
                    ->setData($this->getRequest()->getPost())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))
                    ->setId(null);
                $customer->addAddress($address);

                $errors = $address->validate();
                if (!is_array($errors)) {
                    $errors = array();
                }
            }

            try {
                $validationCustomer = $customer->validate();
                if (is_array($validationCustomer)) {
                    $errors = array_merge($validationCustomer, $errors);
                }
                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();
										// CUSTOM CODE START
										$postData = $this->getRequest()->getPost();
										$oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
										if(isset($postData['customer'])) {
										foreach ($postData['customer'] as $postFieldName => $postValue)
										{
												if (strpos($postFieldName, 'b2b_checkout_'))
												{
														
														$postNameParts = explode('_', $postFieldName);
														$postFieldId = $postNameParts[2];
														if(is_array($postValue)) {
																$finalindividualvalue="";
																foreach($postValue as $individualvalue)
																{ 
																	$finalindividualvalue .= $individualvalue . ",";
																}
																$aDBInfo = array
																(
																		'entity_id'     => $customer->getId(),
																		'attribute_id'  => $postFieldId,
																		'value'         => $finalindividualvalue,
																);
																$oDb->insert($this->_sCustomCustomerAttrTable, $aDBInfo);
																
														} else {
																$aDBInfo = array
																(
																		'entity_id'     => $customer->getId(),
																		'attribute_id'  => $postFieldId,
																		'value'         => $postValue,
																);
																$oDb->insert($this->_sCustomCustomerAttrTable, $aDBInfo);
														}
														
														
														
												}
										}
										
									 }
                   $this->_redirect('*/account');
									 #exit;
										// CUSTOM CODE END

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail('confirmation', $this->_getSession()->getBeforeAuthUrl());
                        $this->_getSession()->addSuccess($this->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.',
                            Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())
                        ));
                        #$this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        return;
                    }
                    else {
                        $this->_getSession()->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        #$this->_redirectSuccess($url);
                        return;
                    }
                } else {
                    $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $this->_getSession()->addError($errorMessage);
                        }
                    }
                    else {
                        $this->_getSession()->addError($this->__('Invalid customer data'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setCustomerFormData($this->getRequest()->getPost());
            }
            catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Can\'t save customer'));
            }
        }
        /**
         * Protect XSS injection in user input
         */
        $this->_getSession()->setEscapeMessages(true);
        $this->_redirectError(Mage::getUrl('*/*/create', array('_secure'=>true)));
    }

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess($this->__('Thank you for registering with %s. Please check your spam or junk folder if you do not recieve a confirmation email.', Mage::app()->getStore()->getName()));

        $customer->sendNewAccountEmail($isJustConfirmed ? 'confirmed' : 'registered');

        $successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /**
     * Change customer password action
     */
    public function editPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer/customer')
                ->setId($this->_getSession()->getCustomerId())
                ->setWebsiteId($this->_getSession()->getCustomer()->getWebsiteId());

            $fields = Mage::getConfig()->getFieldset('customer_account');
            $data = $this->_filterPostData($this->getRequest()->getPost());

            foreach ($fields as $code=>$node) {
                if ($node->is('update') && isset($data[$code])) {
                    $customer->setData($code, $data[$code]);
                }
            }

            $errors = $customer->validate();
            if (!is_array($errors)) {
                $errors = array();
            }

            /**
             * we would like to preserver the existing group id
             */
            if ($this->_getSession()->getCustomerGroupId()) {
                $customer->setGroupId($this->_getSession()->getCustomerGroupId());
            }

            if ($this->getRequest()->getParam('change_password')) {
                $currPass = $this->getRequest()->getPost('current_password');
                $newPass  = $this->getRequest()->getPost('password');
                $confPass  = $this->getRequest()->getPost('confirmation');

                if (empty($currPass) || empty($newPass) || empty($confPass)) {
                    $errors[] = $this->__('Password fields can\'t be empty.');
                }

                if ($newPass != $confPass) {
                    $errors[] = $this->__('Please make sure your passwords match.');
                }

                $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                if (strpos($oldPass, ':')) {
                    list($_salt, $salt) = explode(':', $oldPass);
                } else {
                    $salt = false;
                }

                if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                    $customer->setPassword($newPass);
                } else {
                    $errors[] = $this->__('Invalid current password');
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;
            }

            try {
                $customer->save();
								// CUSTOM CODE START
								$postData = $this->getRequest()->getPost();
								#print_r($postData);
								
								$oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
								
								foreach ($postData['customer'] as $postFieldName => $postValue)
								{
										if (strpos($postFieldName, 'b2b_checkout_'))
										{
												
										$postNameParts = explode('_', $postFieldName);
										$postFieldId = $postNameParts[2];
												
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
													$select_qry=$read->query("select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$postFieldId."'");
													$row = $select_qry->fetch();
													$attributeDataValue = $row['value'];
													if($attributeDataValue !="") {
														$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $customer->getId() . ' AND attribute_id = ' . $postFieldId);
													} else {
														#if($row['value'] !="") {
															$aDBInfo = array
															(
																	'entity_id'     => $customer->getId(),
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
													if(!$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $customer->getId() . ' AND attribute_id = ' . $postFieldId)) {
														
														 #if($postValue == "") { $postValue = "empty"; }
														 
															$aDBInfo = array
															(
																	'entity_id'     => $customer->getId(),
																	'attribute_id'  => $postFieldId,
																	'value'         => $postValue,
															);
															//fix for when user was created outside of frontend reg.. then there is no IDs in db for you to "update" thus need to insert
															#$rows = $read->fetchAll($select_qry);
														  $resource = Mage::getSingleton('core/resource');
														  $read = $resource->getConnection('core_read');
							 								$select_qry=$read->query("select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$postFieldId."'");
															$row = $select_qry->fetch();
															$attributeDataValue = $row['value'];
															if($attributeDataValue !="") {
																$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $customer->getId() . ' AND attribute_id = ' . $postFieldId);
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
														$select_qry=$read->query("select value from mb2b_customer_entity_custom where entity_id = '".$customer->getId()."' and attribute_id = '".$postFieldId."'");
														$row = $select_qry->fetch();
														$attributeDataValue = $row['value'];
														if($attributeDataValue !="") {
																$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo, 'entity_id = ' . $customer->getId() . ' AND attribute_id = ' . $postFieldId);
														} else {
															#if($row['value'] !="") {
																$aDBInfo = array
																(
																		'entity_id'     => $customer->getId(),
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
							$oDb->update($this->_sCustomCustomerAttrTable, $aDBInfo2, 'entity_id = ' . $customer->getId() . ' AND attribute_id = ' . $postpart3FieldId);
							
						}
							 #$this->_redirect('*/account');
								#exit;
								// CUSTOM CODE END
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('Account information was successfully saved'));

                $this->_redirect('customer/account');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Can\'t save customer'));
            }
        }

        $this->_redirect('*/*/edit');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }
}
