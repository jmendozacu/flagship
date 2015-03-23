<?php
/**
 * IndexController.php
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
class Mageb2bextensions_Customattributes_IndexController extends Mage_Adminhtml_Controller_action
{

	protected $_checkoutTypeId;
	protected $_type;

  public function preDispatch()
  {
        parent::preDispatch();
        Mage::getModel('customattributes/customattributes')->checkDatabaseInstall();	    
        
        $this->_checkoutTypeId = Mage::getModel('eav/entity')->setType('mb2b_checkout')->getTypeId();
        $this->_type = 'checkout';
  }
    
	protected function _initAction($ids=null) {
		$this->loadLayout($ids);
		return $this;
	}
	
	public function indexAction()
	{
    Mage::getModel('customattributes/customattributes')->checkDatabaseInstall();	    
        
		$this->_initAction()
			->_setActiveMenu('customattributes/customattributes')
			->_addContent($this->getLayout()->createBlock('customattributes/adminhtml_customattributes_grid'));
		$this->renderLayout();
	}
	
  public function editAction() {
	
    Mage::getModel('customattributes/customattributes')->checkDatabaseInstall();
         
						$id = (int)$this->getRequest()->getParam('attribute_id');
            $model = Mage::getModel('catalog/resource_eav_attribute');
#            ->setEntityTypeId($this->_entityTypeId);
#                $iProductEntityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId(); // to imitate product attribute saving 

      			$oResource = Mage::getResourceModel('eav/entity_attribute');
            $collection = Mage::getResourceModel('eav/entity_attribute_collection');
            $collection->getSelect()->join(
                array('additional_table' => $oResource->getTable('catalog/eav_attribute')),
                'additional_table.attribute_id=main_table.attribute_id'
            );
      
            $collection->getSelect()->where('main_table.attribute_id = ' . $id);  
						      
            $aAttributeList = $collection->getData();
            
            if ($aAttributeList and !empty($aAttributeList[0]))
            {
                		$model->load($id);

                $model->addData($aAttributeList[0]);
            }
            	
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('customattributes_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('customattributes/customattributes');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('customattributes/adminhtml_customattributes_edit'))
					 ->_addLeft($this->getLayout()->createBlock('customattributes/adminhtml_customattributes_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customattributes')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode  = $this->getRequest()->getParam('attribute_code');
        $attributeId    = $this->getRequest()->getParam('attribute_id');
        $this->_entityTypeId=$this->_checkoutTypeId;
        
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Attribute with the same code already exists'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->getResponse()->setBody($response->toJson());
    }
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
            #$model = Mage::getModel('catalog/entity_attribute'); // 1.3.x and pre 
			$model = Mage::getModel('catalog/resource_eav_attribute');		
            /* @var $model Mage_Catalog_Model_Entity_Attribute */

            if ($id = $this->getRequest()->getParam('attribute_id')) {

                $model->load($id);

                $data['attribute_code'] = $model->getAttributeCode();
                $data['frontend_input'] = $model->getFrontendInput();
            }
			
            $sRealInput = $data['frontend_input'];
            
            if ($data['frontend_input'] == 'checkbox')
            {
                $data['frontend_input'] = 'multiselect';
            }
            
            if ($data['frontend_input'] == 'radio')
            {
                $data['frontend_input'] = 'select';
            }
            
            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }
			
			// process website/store assign

			if (!isset($data['is_visible_in_advanced_search']))
			{
			    $data['is_visible_in_advanced_search'] = 0;
			}
			
			if (!$data['is_visible_in_advanced_search']) // is not global
			{
    			if (isset($data['assign_website']) AND $data['assign_website'])
    			{
    			    $data['apply_to'] = implode(',', array_keys($data['assign_website']));
    			}
    			else 
    			{
    			    $data['apply_to'] = '';
    			}
    			
    			if (isset($data['assign_store']) AND $data['assign_store'])
    			{
    			    $sCommonData = '';
    			    
    			    $aStoreHash = array();
    			    
    			    foreach ($data['assign_store'] as $iWebsiteKey => $aStoreData)
    			    {
    			        $aStoreHash[] = implode(',', $aStoreData);
    			    }
    			    
    			    $data['note'] = implode(',', $aStoreHash);
    			}
    			else 
    			{
    			    $data['note'] = '';
    			}
			}
			
#            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) 
            {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }
			
            if(!isset($data['apply_to'])) {
                $data['apply_to'] = array();
            }
            
            if (!isset($data['is_global'])) {
                $data['is_global'] = 0;
            }
            
            if (!isset($data['is_unique'])) {
                $data['is_unique'] = 0;
            }
            
            if (!isset($data['is_wysiwyg_enabled'])) {
                $data['is_wysiwyg_enabled'] = 0;
            }
            
            if (!isset($data['is_html_allowed_on_front'])) {
                $data['is_html_allowed_on_front'] = 1;
            }
            
            if (!isset($data['is_visible_on_front'])) {
                $data['is_visible_on_front'] = 1;
            }
            
            if (!isset($data['used_for_sort_by'])) {
                $data['used_for_sort_by'] = 1;
            }
            
			$model->addData($data);
			
#            if (!$id) 
            {
                $iProductEntityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId(); // to imitate product attribute saving 

                $model->setEntityTypeId($iProductEntityTypeId);
                $model->setIsUserDefined(1);
            }
			
			try {


                if (in_array($data['frontend_input'], array('multiselect', 'select', 'radio', 'checkbox')))
                {
                    if (!empty($data['default']))
                    {
                        $sDefValue = $data['default'];
                    }
                    else 
                    {
                        $sDefValue = '';
                    }
                }
                else 
                {
        			$sDefValue = $model->getDefaultValue();
                }
    			


				$model->save();

				$id=$model->getId();
				
				// save descs
				
				$aDescription = array();
				
				if (isset($data['frontend_desc']) AND $data['frontend_desc'])
				{
				    $aDescription = $data['frontend_desc'];
				}
				
				Mage::getModel('customattributes/customattributes')->saveAttributeDescription($id, $aDescription);

			    #$oUpdateModel = Mage::getModel('catalog/entity_attribute');
				$oUpdateModel = Mage::getModel('catalog/resource_eav_attribute');
			    $oUpdateModel->load($id);
				
				if ($data['frontend_input'] != $sRealInput)
				{
    				$oUpdateModel->setFrontendInput($sRealInput);
				}
				
				if ($sDefValue AND is_array($sDefValue))
				{
				    $sDefValue = implode(',', $sDefValue);
				}
#				d($sDefValue, 1);
				$oUpdateModel->setDefaultValue($sDefValue);
				$oUpdateModel->setAitocflag(true);
                $oUpdateModel->setEntityTypeId($this->_checkoutTypeId);
			    $oUpdateModel->save();

				
				// save need select
				
				$aNeedSelect = array();
				
				if (isset($data['frontend_need_sel']) AND $data['frontend_need_sel'])
				{
				    $aNeedSelect = $data['frontend_need_sel'];
				}
				
				Mage::getModel('customattributes/customattributes')->saveAttributeNeedSelect($id, $aNeedSelect);
				
				
                /**
                 * Clear translation cache because attribute labels are stored in translation
                 */
                Mage::getSingleton('adminhtml/session')->setAttributeData(false);
				
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customattributes')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('attribute_id' => $id));
					return;
				}
				
				$this->_redirect('*/*/index/filter//');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitcheckoutfields')->__('Unable to find item to save'));
        $this->_redirect('*/*/index/filter//');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('attribute_id') > 0 ) {
			try {
				$model = Mage::getModel('eav/entity_attribute');
				 
				$model->setId($this->getRequest()->getParam('attribute_id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/index/filter//');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
			}
		}
		$this->_redirect('*/*/index/filter//');
	}

    public function massDeleteAction() {
        $categoriesattributesIds = $this->getRequest()->getParam('customattributes');
        if(!is_array($categoriesattributesIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoriesattributesIds as $categoriesattributesId) {
                    $categoriesattributes = Mage::getModel('eav/entity_attribute')->load($categoriesattributesId);
                    $categoriesattributes->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($categoriesattributesIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index/filter//');
    }
    
	
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customattributes/customattributes');
    }
}