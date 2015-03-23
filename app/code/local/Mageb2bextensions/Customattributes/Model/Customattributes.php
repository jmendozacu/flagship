<?php
/**
 * Customattributes.php
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
class Mageb2bextensions_Customattributes_Model_Customattributes extends Mage_Core_Model_Abstract
{
    protected $_aCheckoutAtrrList;
    protected $_sEntityTypeCode     = 'mb2b_checkout';
    protected $_sCustomerAttrTable  = 'mb2b_customer_entity_custom';
    protected $_sDescAttrTable      = 'mb2b_custom_attribute_description';
    protected $_sNeedSelectTable    = 'mb2b_custom_attribute_need_select';
    
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('customattributes/customattributes');
    }
    
    public function getAtrributeLabel($iAttributeId, $iStoreId = 0)
    {
         if (!$iAttributeId) return false;
        
		$oAttribute  = Mage::getModel('eav/entity_attribute');
		$oAttribute->load($iAttributeId);

		if (!$oAttribute->getData()) return false;
		
		if (!$iStoreId)
		{
		    $iStoreId = Mage::app()->getStore()->getId();
		}
		
        $values = array();
        $values[0] = $oAttribute->getFrontend()->getLabel();
        // it can be array and cause bug
        
        $frontendLabel = $oAttribute->getFrontend()->getLabel();
        if (is_array($frontendLabel)) {
            $frontendLabel = array_shift($frontendLabel);
        }
        
        $storeLabels = $oAttribute->getStoreLabels();
        foreach ($this->getStores() as $store) {
            if ($store->getId() != 0) {
                $values[$store->getId()] = isset($storeLabels[$store->getId()]) ? $storeLabels[$store->getId()] : '';
            }
        }
        
        if (isset($values[$iStoreId]) AND $values[$iStoreId])
        {
            $sLabel = $values[$iStoreId];
        }
        else 
        {
            $sLabel = $values[0];
        }
        
        return $sLabel;
    }    
    
    
    public function getStores()
    {
        $stores = $this->getData('stores');
        if (is_null($stores)) {
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();
            $this->setData('stores', $stores);
        }
        return $stores;
    }

    public function getAttributeOptionValues($sFieldId, $iStoreId, $aOptionIdList)
    {
        if (!$sFieldId OR !$iStoreId OR !$aOptionIdList) return false;
        
        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($sFieldId)
            ->setStoreFilter($iStoreId, true)
            ->load();
            
        if (!is_array($aOptionIdList))
        {
            $aOptionIdList = array($aOptionIdList);
        }
            
        $aValueList = array();
        
        foreach ($valuesCollection as $item) 
        {
            if (in_array($item->getId(), $aOptionIdList)) 
            {
                $aValueList[] = $item->getValue();
            }
        }
        
        return $aValueList;
    }
    
    public function getAttributeHtml($aField, $sSetName, $sPageType, $iStoreId = 0)
    {
        $oView = new Zend_View();

        $iItemId = $aField['attribute_id'];
        $sPrefix = 'mb2b_checkout_';

        $sFieldId = $sSetName . ':' . $sPrefix . $iItemId;
        
        $sLabel = $this->getAtrributeLabel($iItemId, $iStoreId);
        
        $sHtml = '<label for="' . $sFieldId . '">' . $sLabel . '';
        
        if ($aField['is_required'])
        {
            $sHtml .= '<span class="required">*</span>';
        }
        
        $sHtml .= '</label><br /> ';

        $sFieldName     = $sSetName . '[' . $sPrefix . $iItemId . ']';
        $sFieldValue    = $this->getCustomValue($aField, $sPageType);
        
        $sFieldClass = '';
        
        if ($aField['frontend_class'])
        {
            $sFieldClass .= $aField['frontend_class'];
        }
        
        if ($aField['is_required'])
        {
            $sFieldClass .= ' required-entry';
        }
        
        $aParams = array
        (
            'id' => $sFieldId,
#                    'class' => 'validate-zip-international required-entry input-text', // to do - check
            'class' => $sFieldClass, // to do - check
            'title' => $sLabel,
        );
                
        $aOptionHash = $this->getOptionValues($iItemId);
        
        
        // add 'please select' value to option list
         
        if ($aField['used_in_product_listing'])
        {
            
            $aTitleHash = $this->getAttributeNeedSelect($iItemId);
            
    	    $iStoreId = Mage::app()->getStore()->getId();
            
            if ($aTitleHash AND isset($aTitleHash[$iStoreId]))
            {
                $sNeedSelectTitle = $aTitleHash[$iStoreId];
            }
            elseif ($aTitleHash) 
            {
                $sNeedSelectTitle = current($aTitleHash);
            }
            else // must not happen
            {
                $sNeedSelectTitle = '';
            }
        
            
            /*
            */
            
            if ($aOptionHash)
            {
                $aFullOptionHash = array('' => $sNeedSelectTitle);
                
                foreach ($aOptionHash as $iKey => $sOption)
                {
                    $aFullOptionHash[$iKey] = $sOption;
                }
                
                $aOptionHash = $aFullOptionHash;
            }
            else 
            {
                $aOptionHash = array('' => $sNeedSelectTitle);
            }
            
#            $aOptionHash = array_merge_recursive(, $aOptionHash);
        }
        
        switch ($aField['frontend_input'])
        {
            case 'text':
                $aParams['class'] .= ' input-text';
                $sHtml .= $oView->formText($sFieldName, $sFieldValue, $aParams);
            break;    
            
            case 'textarea':
                $aParams['class'] .= ' input-text';
//                $aParams['style'] = 'height:50px; width:100%';
                $aParams['style'] = 'height:50px;';
                $sHtml .= $oView->formTextarea($sFieldName, $sFieldValue, $aParams);
            break;    
            
            case 'select':
                $select = Mage::getModel('core/layout')->createBlock('core/html_select')
                    ->setName($sFieldName)
                    ->setId($sFieldId)
                    ->setTitle($sLabel)
#                    ->setClass('validate-select')
                    ->setClass($sFieldClass)
                    ->setValue($sFieldValue)
                    ->setOptions($aOptionHash);
                
                    $sHtml .= $select->getHtml();
            break;    
            
            case 'multiselect':
                $select = Mage::getModel('core/layout')->createBlock('core/html_select')
                    ->setName($sFieldName . '[]')
                    ->setId($sFieldId)
                    ->setTitle($sLabel)
//                    ->setClass('validate-select')
                    ->setClass($sFieldClass)
                    ->setValue($sFieldValue)
                    ->setExtraParams('multiple')
                    ->setOptions($aOptionHash);
                
                    $sHtml .= $select->getHtml();
            break;    
            
            case 'checkbox':
                
#            $selectHtml = '<ul id="options-'.$_option->getId().'-list" class="options-list">';
            $selectHtml = '<ul id="options-'.$sFieldId.'-list" class="options-list">';
            $require = ($aField['is_required']) ? ' validate-one-required-by-name' : '';
            $arraySign = '';
                    $type = 'checkbox';
                    $class = 'checkbox';
                    $arraySign = '[]';
                    
            $count = 0;
            
            if ($aOptionHash)
            {
                foreach ($aOptionHash as $iKey => $sValue) 
                {
                    $count++;
                    
                    $schecked = '';
                    
                    if ($sFieldValue AND in_array($iKey, $sFieldValue))
                    {
                        $schecked = 'checked';
                    }
                    
                    $selectHtml .= '<li>' .
                                   '<input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option" name="'.$sFieldName.''.$arraySign.'" id="'.$sFieldId.'_'.$count.'" value="'.$iKey.'" '.$schecked.' />' .
                                   '<span class="label"><label for="'.$sFieldId.'_'.$count.'">'.$sValue.'</label></span>';
                    $selectHtml .= '</li>';
                }
            }
            $selectHtml .= '</ul>';
                
                $sHidden = '<input type="hidden" name="'.$sFieldName.'"  value="" />';                
            
                $sHtml .= $sHidden . $selectHtml;
            break;    
            
            case 'radio':
                
            $selectHtml = '<ul id="options-'.$sFieldId.'-list" class="options-list">';
            $require = ($aField['is_required']) ? ' validate-one-required-by-name' : '';
            
                    $type = 'radio';
                    $class = 'radio';
                    if (!$aField['is_required']) {
                        $selectHtml .= '<li><input type="radio" id="'.$sFieldId.'" class="'.$class.' product-custom-option" name="'.$sFieldName.'" value="" checked="checked" /><span class="label"><label for="options_'.$sFieldId.'">' . Mage::helper('catalog')->__('None') . '</label></span></li>';
                    }
                    
            $count = 0;
            
            if ($aOptionHash)
            {
                foreach ($aOptionHash as $iKey => $sValue) 
                {
                    $count++;
                    
                    $schecked = '';
                    
                    if ($iKey == $sFieldValue)
                    {
                        $schecked = 'checked';
                    }
                    
                    $selectHtml .= '<li>' .
                                   '<input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option" name="'.$sFieldName.''.'" id="'.$sFieldId.'_'.$count.'" value="'.$iKey.'" '.$schecked.' />' .
                                   '<span class="label"><label for="'.$sFieldId.'_'.$count.'">'.$sValue.'</label></span>';
                                   
                    $selectHtml .= '</li>';
                }
            }
            $selectHtml .= '</ul>';
                
                $sHidden = '<input type="hidden" name="'.$sFieldName.'"  value="" />';                
            
                $sHtml .= $sHidden . $selectHtml;
            break;    
            
            case 'boolean':
                
                $yesno = array(
                    array(
                        'value' => 0,
                        'label' => Mage::helper('catalog')->__('No')
                    ),
                    array(
                        'value' => 1,
                        'label' => Mage::helper('catalog')->__('Yes')
                    ));
                
                $select = Mage::getModel('core/layout')->createBlock('core/html_select')
                    ->setName($sFieldName)
                    ->setId($sFieldId)
                    ->setTitle($sLabel) 
                    ->setClass('validate-select')
                    ->setValue($sFieldValue)
                    ->setOptions($yesno);
                
                    $sHtml .= $select->getHtml();
            break;    
            
            case 'date':
                $calendar = Mage::getModel('core/layout')
                    ->createBlock('core/html_date')
                    ->setName($sFieldName)
                    ->setId($sFieldId)
                    ->setTitle($sLabel) 
                    ->setClass($sFieldClass)
                    ->setValue($sFieldValue)
#                    ->setClass('input-text'.$require)
                    ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
                    ->setFormat(Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        
                $sHtml .= $calendar->getHtml();
            break;    
        }

        $aDescHash = $this->getAttributeDescription($iItemId);
        
	    $iStoreId = Mage::app()->getStore()->getId();
        
        if ($aDescHash AND isset($aDescHash[$iStoreId]))
        {
            $sHtml .= '<br>' . $aDescHash[$iStoreId];
        }
        
        return $sHtml;        
    }


    public function getAttributeHtmlwithValue($aField, $sSetName, $sPageType, $attributeValue, $iStoreId = 0)
    {
        $oView = new Zend_View();

        $iItemId = $aField['attribute_id'];
        $sPrefix = 'mb2b_checkout_';

        $sFieldId = $sSetName . ':' . $sPrefix . $iItemId;
        
        $sLabel = $this->getAtrributeLabel($iItemId, $iStoreId);
        
        $sHtml = '<label for="' . $sFieldId . '">' . $sLabel . '';
        
        if ($aField['is_required'])
        {
            $sHtml .= '<span class="required">*</span>';
        }
        
        $sHtml .= '</label><br /> ';

        $sFieldName     = $sSetName . '[' . $sPrefix . $iItemId . ']';
        $sFieldValue    = $this->getCustomValue($aField, $sPageType);
        
        $sFieldClass = '';
        
        if ($aField['frontend_class'])
        {
            $sFieldClass .= $aField['frontend_class'];
        }
        
        if ($aField['is_required'])
        {
            $sFieldClass .= ' required-entry';
        }
        
        $aParams = array
        (
            'id' => $sFieldId,
#                    'class' => 'validate-zip-international required-entry input-text', // to do - check
            'class' => $sFieldClass, // to do - check
            'title' => $sLabel,
        );
                
        $aOptionHash = $this->getOptionValues($iItemId);
        
        
        // add 'please select' value to option list
         
        if ($aField['used_in_product_listing'])
        {
            
            $aTitleHash = $this->getAttributeNeedSelect($iItemId);
            
    	    $iStoreId = Mage::app()->getStore()->getId();
            
            if ($aTitleHash AND isset($aTitleHash[$iStoreId]))
            {
                $sNeedSelectTitle = $aTitleHash[$iStoreId];
            }
            elseif ($aTitleHash) 
            {
                $sNeedSelectTitle = current($aTitleHash);
            }
            else // must not happen
            {
                $sNeedSelectTitle = '';
            }
        
            
            /*
            */
            
            if ($aOptionHash)
            {
                $aFullOptionHash = array('' => $sNeedSelectTitle);
                
                foreach ($aOptionHash as $iKey => $sOption)
                {
                    $aFullOptionHash[$iKey] = $sOption;
                }
                
                $aOptionHash = $aFullOptionHash;
            }
            else 
            {
                $aOptionHash = array('' => $sNeedSelectTitle);
            }
            
#            $aOptionHash = array_merge_recursive(, $aOptionHash);
        }
        
        switch ($aField['frontend_input'])
        {
            case 'text':
                $aParams['class'] .= ' input-text';
                $sHtml .= $oView->formText($sFieldName, $attributeValue, $aParams);
            break;    
            
            case 'textarea':
                $aParams['class'] .= ' input-text';
//                $aParams['style'] = 'height:50px; width:100%';
                $aParams['style'] = 'height:50px;';
                $sHtml .= $oView->formTextarea($sFieldName, $attributeValue, $aParams);
            break;    
            
            case 'select':
                $select = Mage::getModel('core/layout')->createBlock('core/html_select')
                    ->setName($sFieldName)
                    ->setId($sFieldId)
                    ->setTitle($sLabel)
#                    ->setClass('validate-select')
                    ->setClass($sFieldClass)
                    ->setValue($attributeValue)
                    ->setOptions($aOptionHash);
                
                    $sHtml .= $select->getHtml();
            break;    
            
            case 'multiselect':
                $select = Mage::getModel('core/layout')->createBlock('core/html_select')
                    ->setName($sFieldName . '[]')
                    ->setId($sFieldId)
                    ->setTitle($sLabel)
//                    ->setClass('validate-select')
                    ->setClass($sFieldClass)
                    ->setValue($attributeValue)
                    ->setExtraParams('multiple')
                    ->setOptions($aOptionHash);
                
                    $sHtml .= $select->getHtml();
            break;    
            
            case 'checkbox':
                
#            $selectHtml = '<ul id="options-'.$_option->getId().'-list" class="options-list">';
            $selectHtml = '<ul id="options-'.$sFieldId.'-list" class="options-list">';
            $require = ($aField['is_required']) ? ' validate-one-required-by-name' : '';
            $arraySign = '';
                    $type = 'checkbox';
                    $class = 'checkbox';
                    $arraySign = '[]';
                    
            $count = 0;
            
            if ($aOptionHash)
            {
                foreach ($aOptionHash as $iKey => $sValue) 
                {
                    $count++;
                    
                    $schecked = '';
										$eachboxthatischecked = explode(",",$attributeValue);
										foreach ($eachboxthatischecked as $attributedcheckedvalue) 
                		{
											if ($iKey == $attributedcheckedvalue)
											{
													$schecked = 'checked';
													break;
											}
										}
                    /*
                    if ($sFieldValue AND in_array($iKey, $sFieldValue))
                    {
                        $schecked = 'checked';
                    }
                    */
                    $selectHtml .= '<li>' .
                                   '<input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option" name="'.$sFieldName.''.$arraySign.'" id="'.$sFieldId.'_'.$count.'" value="'.$iKey.'" '.$schecked.' />' .
                                   '<span class="label"><label for="'.$sFieldId.'_'.$count.'">'.$sValue.'</label></span>';
                    $selectHtml .= '</li>';
                }
            }
            $selectHtml .= '</ul>';
                
                $sHidden = '<input type="hidden" name="'.$sFieldName.'"  value="" />';                
            
                $sHtml .= $sHidden . $selectHtml;
            break;    
            
            case 'radio':
                
            $selectHtml = '<ul id="options-'.$sFieldId.'-list" class="options-list">';
            $require = ($aField['is_required']) ? ' validate-one-required-by-name' : '';
            
                    $type = 'radio';
                    $class = 'radio';
                    if (!$aField['is_required']) {
                        $selectHtml .= '<li><input type="radio" id="'.$sFieldId.'" class="'.$class.' product-custom-option" name="'.$sFieldName.'" value="" checked="checked" /><span class="label"><label for="options_'.$sFieldId.'">' . Mage::helper('catalog')->__('None') . '</label></span></li>';
                    }
                    
            $count = 0;
            
            if ($aOptionHash)
            {
                foreach ($aOptionHash as $iKey => $sValue) 
                {
                    $count++;
                    
                    $schecked = '';
										$eachboxthatischecked = explode(",",$attributeValue);
										foreach ($eachboxthatischecked as $attributedcheckedvalue) 
                		{
											if ($iKey == $attributedcheckedvalue)
											{
													$schecked = 'checked';
													break;
											}
										}
                    /*
                    if ($iKey == $sFieldValue)
                    {
                        $schecked = 'checked';
                    }
                    */
                    $selectHtml .= '<li>' .
                                   '<input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option" name="'.$sFieldName.''.'" id="'.$sFieldId.'_'.$count.'" value="'.$iKey.'" '.$schecked.' />' .
                                   '<span class="label"><label for="'.$sFieldId.'_'.$count.'">'.$sValue.'</label></span>';
                                   
                    $selectHtml .= '</li>';
                }
            }
            $selectHtml .= '</ul>';
                
                $sHidden = '<input type="hidden" name="'.$sFieldName.'"  value="" />';                
            
                $sHtml .= $sHidden . $selectHtml;
            break;    
            
            case 'boolean':
                
                $yesno = array(
                    array(
                        'value' => 0,
                        'label' => Mage::helper('catalog')->__('No')
                    ),
                    array(
                        'value' => 1,
                        'label' => Mage::helper('catalog')->__('Yes')
                    ));
                
                $select = Mage::getModel('core/layout')->createBlock('core/html_select')
                    ->setName($sFieldName)
                    ->setId($sFieldId)
                    ->setTitle($sLabel) 
                    ->setClass('validate-select')
                    ->setValue($attributeValue)
                    ->setOptions($yesno);
                
                    $sHtml .= $select->getHtml();
            break;    
            
            case 'date':
                $calendar = Mage::getModel('core/layout')
                    ->createBlock('core/html_date')
                    ->setName($sFieldName)
                    ->setId($sFieldId)
                    ->setTitle($sLabel) 
                    ->setClass($sFieldClass)
                    ->setValue($attributeValue)
#                    ->setClass('input-text'.$require)
                    ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
                    ->setFormat(Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        
                $sHtml .= $calendar->getHtml();
            break;    
        }

        $aDescHash = $this->getAttributeDescription($iItemId);
        
	    $iStoreId = Mage::app()->getStore()->getId();
        
        if ($aDescHash AND isset($aDescHash[$iStoreId]))
        {
            $sHtml .= '<br>' . $aDescHash[$iStoreId];
        }
        
        return $sHtml;        
    }
		
    public function getAttributeEnableHtml($aField, $sSetName)
    {
        $iItemId = $aField['attribute_id'];
        $sPrefix = 'mb2b_checkout_';
        
        $sFieldId = $sSetName . ':' . $sPrefix . $iItemId;
            
        if ($aField['frontend_input'] == 'radio' OR $aField['frontend_input'] == 'checkbox')
        {
            $sHtml = ''; 
            
            $aOptionHash = $this->getOptionValues($iItemId);            
            
            $count = 0;
            
            if ($aOptionHash)
            {
                foreach ($aOptionHash as $sVal)
                {
                    $count++;
                    $sHtml .= ' $("' . $sFieldId.'_'.$count . '").disabled = false; ';
                }
            }
        }
        else 
        {
            $sHtml = ' $("' . $sFieldId . '").disabled = false; ';
        }
        
        return $sHtml;        
    }

    public function getCustomValue($aField, $sPageType)
    {
        if (!$aField) return false;
        
        if ($aField['frontend_input'] == 'multiselect' OR $aField['frontend_input'] == 'checkbox')
        {
            $sValue = explode(',', $aField['default_value']);
        }
        else 
        {
            $sValue = $aField['default_value'];            
        }
        
        if (isset($_SESSION['mb2b_checkout_used'][$sPageType][$aField['attribute_id']]))
        {
            return $_SESSION['mb2b_checkout_used'][$sPageType][$aField['attribute_id']];
        }
        
        return $sValue;
    }
    
    public function getOptionValues($sFieldId, $iStoreId = 0)
    {
        if (!$sFieldId) return false;
        
        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($sFieldId)
            ->setStoreFilter($iStoreId)
            ->load();
            
        $aOptionHash    = array();
        $aRawOptionHash = array();
        $aSortHash      = array();
        
        foreach ($valuesCollection as $item) 
        {
            $aSortHash[$item->getId()] = $item->getData('sort_order');
            $aRawOptionHash[$item->getId()] = $item->getValue();
        }
        
        if ($aSortHash)
        {
            asort($aSortHash);
            
            foreach ($aSortHash as $iKey => $sVal)
            {
                $aOptionHash[$iKey] = $aRawOptionHash[$iKey];
            }
        }
        
        return $aOptionHash;
    }    
    
    public function getCheckoutAtrributeList($iStepId, $iTplPlaceId, $sPageType)
    {
        if ($this->_aCheckoutAtrrList === NULL)
        {
            if (!isset($_SESSION['mb2b_checkout_used']))
            {
                $_SESSION['mb2b_checkout_used'] = array();
            }
            
            switch ($sPageType)
            {
								case 'customereditpage':
                    $sStepField = 'is_used_for_price_rules'; // hook for input source (customer reg)
								break;
								
								case 'registerpage':
                    $sStepField = 'is_used_for_price_rules'; // hook for input source (customer reg)
                break;   
            }
            
		    $iStoreId = Mage::app()->getStore()->getId();
		    $iSiteId  = Mage::app()->getWebsite()->getId();
			//added check for in Magento 1.5 the Admin store id is 0 and site id is 0 
			
			
				$oResource = Mage::getResourceModel('eav/entity_attribute');
				
				$collection = Mage::getResourceModel('eav/entity_attribute_collection')
							->setEntityTypeFilter( Mage::getModel('eav/entity')->setType($this->_sEntityTypeCode)->getTypeId() );
				$collection->getSelect()->join(
					array('additional_table' => $oResource->getTable('catalog/eav_attribute')),
					'additional_table.attribute_id=main_table.attribute_id'
				);
						 
		     if ($iStoreId != 0 && $iSiteId != 0) {
				$sWhereScope = '(additional_table.is_visible_in_advanced_search = 1 OR (find_in_set("' . $iStoreId . '", main_table.note) OR find_in_set("' . $iSiteId . '", additional_table.apply_to)))';
				#$collection->getSelect()->where($sWhereScope);
				$collection->getSelect()->where('additional_table.' . $sStepField . ' > 0');                
				$collection->getSelect()->where($sWhereScope);                
				$collection->getSelect()->order('additional_table.position ASC');    
			 }
			 /*
                $sWhereScope = '(additional_table.is_visible_in_advanced_search = 1 OR (find_in_set("' . $iStoreId . '", main_table.note) OR find_in_set("' . $iSiteId . '", additional_table.apply_to)))';
            */           
  
            $aAttributeList = $collection->getData();
            
            $this->_aCheckoutAtrrList = array();
       
            if ($aAttributeList)
            {
                foreach ($aAttributeList as $aItem)
                {
                    $this->_aCheckoutAtrrList[$aItem[$sStepField]][$aItem['is_filterable']][$aItem['attribute_id']] = $aItem;
                }
            }
        }
        
        if (isset($this->_aCheckoutAtrrList[$iStepId][$iTplPlaceId]))
        {
            return $this->_aCheckoutAtrrList[$iStepId][$iTplPlaceId];
        }
        else 
        {
            return false;
        }
    }    
    
    
    public function setCustomValue($sFieldName, $sFieldValue, $sPageType)
    {
        if (!$sFieldName OR !$sPageType) return false;

        if (strpos($sFieldName, 'b2b_checkout_'))
        {
            $aNameParts = explode('_', $sFieldName);
            
            $sFieldId = $aNameParts[2];
            
            $_SESSION['mb2b_checkout_used'][$sPageType][$sFieldId] = $sFieldValue;
        }
        
        return true;
    }       
    
    public function clearCheckoutSession($sPageType)
    {
        $_SESSION['mb2b_checkout_used'][$sPageType] = array();
    }
        
    public function checkDatabaseInstall()
    {
        if (isset($_SESSION['mb2b_checkout_database_install']))
        {
            return true;
        }        
        else 
        {
            // check customer data table install

            $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
            
            $sSql = 'show tables like "' . $this->_sCustomerAttrTable . '" ';
            
            if (!$oDb->fetchAll($sSql)) // table does not exist
            {
                $sSql = '
CREATE TABLE IF NOT EXISTS `' . $this->_sCustomerAttrTable . '` (
  `value_id` int(11) NOT NULL auto_increment,
  `attribute_id` smallint(5) unsigned NOT NULL default "0",
  `entity_id` int(10) unsigned NOT NULL default "0",
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `UNQ_MB2B_ENTITY_ATTRIBUTE` (`entity_id`,`attribute_id`),
  KEY `FK_mb2b_customer_entity_custom_attribute` (`attribute_id`),
  KEY `FK_mb2b_customer_entity_custom` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;                
                ';
                
                $oDb->query($sSql);
            }

            // check desc table install

            $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
            
            $sSql = 'show tables like "' . $this->_sDescAttrTable . '" ';
            
            if (!$oDb->fetchAll($sSql)) // table does not exist
            {
                $sSql = '
CREATE TABLE IF NOT EXISTS `' . $this->_sDescAttrTable . '` (
  `attribute_id` int(10) unsigned NOT NULL default "0",
  `store_id` int(10) unsigned NOT NULL default "0",
  `value` text NOT NULL,
  UNIQUE KEY `UNQ_MB2B_DESC_ATTRIBUTE` (`store_id`,`attribute_id`),
  KEY `FK_mb2b_attr_custom_attribute` (`attribute_id`),
  KEY `FK_mb2b_desc_custom_attribute` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;                
                ';
                
                $oDb->query($sSql);
            }
            
            // check need select table install

            $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
            
            $sSql = 'show tables like "' . $this->_sNeedSelectTable . '" ';
            
            if (!$oDb->fetchAll($sSql)) // table does not exist
            {
                $sSql = '
CREATE TABLE IF NOT EXISTS `' . $this->_sNeedSelectTable . '` (
  `attribute_id` int(10) unsigned NOT NULL default "0",
  `store_id` int(10) unsigned NOT NULL default "0",
  `value` text NOT NULL,
  UNIQUE KEY `UNQ_MB2B_NEED_SEL_ATTRIBUTE` (`store_id`,`attribute_id`),
  KEY `FK_mb2b_need_sel_custom_attribute_attr` (`attribute_id`),
  KEY `FK_mb2b_need_sel_custom_attribute_store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;                
                ';
                
                $oDb->query($sSql);
            }
            
            
            // check atribute group field install

            $oE = Mage::getModel('eav/mysql4_entity_type');
            
            $oSelect = $oDb->select()  ->from($oE->getMainTable())
                                        ->where('entity_type_code = ?', $this->_sEntityTypeCode);
            
            if (!$oDb->fetchAll($oSelect)) // record does not exist
            {
                $aDBInfo = array
                (
                    'entity_type_code' => $this->_sEntityTypeCode,
                );
                
                $oDb->insert($oE->getMainTable(), $aDBInfo);
            }
                
            $_SESSION['mb2b_checkout_database_install'] = true;
                       
            return true;
        }
        
    }
    
    public function getSessionCustomData($sPageType, $iStoreId, $bForAdmin)
    {
        if (!$sPageType) return false;
        
        $aCustomAtrrList = array();
        
        $oAttribute = Mage::getModel('eav/entity_attribute');
        
        if (isset($_SESSION['mb2b_checkout_used'][$sPageType]) AND $_SESSION['mb2b_checkout_used'][$sPageType])
        {
            
            foreach ($_SESSION['mb2b_checkout_used'][$sPageType] as $sFieldId => $sValue)
            {
                
                $oAttribute->load($sFieldId);
                
                $aAttrData = $oAttribute->getData();
                
                if ($aAttrData)
                {
                    $bShowAttribute = true;
                    
                    if ($bForAdmin)
                    {
#                        $bShowAttribute = $aAttrData['is_used_for_price_rules']; // fix for admin
                    }
                    else 
                    {
#                        $bShowAttribute = $aAttrData['is_filterable_in_search']; // fix for member
                    }
                }
                else 
                {
                    $bShowAttribute = false;
                }
                
                if ($bShowAttribute)
                {
#                    $sValue = '';
                    
                    switch ($aAttrData['frontend_input'])
                    {
                        case 'text':
                        case 'date': // to check?
                        case 'textarea':
                            $sValue = $sValue;
                        break;
                            
                        case 'boolean':
                            
                            if ($sValue == 1)
                            {
                                $sValue = Mage::helper('catalog')->__('Yes');
                            }
                            elseif ($sValue) 
                            {
                                $sValue = '';
                            }
                            else 
                            {
                                $sValue = Mage::helper('catalog')->__('No');
                            }
                            
                        break;
                            
                        case 'select':
                        case 'radio':
                            
                            $aValueList = $this->getAttributeOptionValues($sFieldId, $iStoreId, $sValue);
                            if ($aValueList)
                            {
                                $sValue = $aValueList[0];
                            }
                        break;    
                        
                        case 'multiselect':
                        case 'checkbox':
                            $aValueList = $this->getAttributeOptionValues($sFieldId, $iStoreId, $sValue);
                            if ($aValueList)
                            {
                                $sValue = implode(', ', $aValueList);
                            }
                        break;    
                    }
                    
                    $aCustomData = array
                    (
                        'label' => $this->getAtrributeLabel($sFieldId, $iStoreId),
                        'value' => $sValue,
                    );
                            
                    $aCustomAtrrList[] = $aCustomData;
                }
            }
        }
        
        return $aCustomAtrrList;
    }    
    
    public function saveAttributeDescription($iAttributeId, $aDescriptionData)
    {
        $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $oDb->delete($this->_sDescAttrTable, 'attribute_id = ' . $iAttributeId);
        
        if ($aDescriptionData)
        {
            foreach ($aDescriptionData as $iStoreId => $sValue)
            {
                $aDBInfo = array
                (
                    'attribute_id'  => $iAttributeId,
                    'store_id'     => $iStoreId,
                    'value'         => $sValue,
                );
        
                $oDb->insert($this->_sDescAttrTable, $aDBInfo);
            }
        }
        
        return true;
    }
    
    public function getAttributeDescription($iAttributeId)
    {
        if (!$iAttributeId) return false;
        
        $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');

        $select = $oDb->select()
            ->from(array('c' => $this->_sDescAttrTable), array('store_id', 'value'))
#            ->joinInner(array('p' => $this->getTable('catalog/product')), 'o.product_id=p.entity_id', array())
            ->where('c.attribute_id=?', $iAttributeId)
        ;
        
        $aItemList = $oDb->fetchPairs($select);
        
        return $aItemList;
    }
    
    // new functions
    
    public function saveAttributeNeedSelect($iAttributeId, $aNeedSelectData)
    {
        $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $oDb->delete($this->_sNeedSelectTable, 'attribute_id = ' . $iAttributeId);
        
        if ($aNeedSelectData)
        {
            foreach ($aNeedSelectData as $iStoreId => $sValue)
            {
                $aDBInfo = array
                (
                    'attribute_id'  => $iAttributeId,
                    'store_id'      => $iStoreId,
                    'value'         => $sValue,
                );
        
                $oDb->insert($this->_sNeedSelectTable, $aDBInfo);
            }
        }
        
        return true;
    }
    
    public function getAttributeNeedSelect($iAttributeId)
    {
        if (!$iAttributeId) return false;
        
        $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');

        $select = $oDb->select()
            ->from(array('c' => $this->_sNeedSelectTable), array('store_id', 'value'))
#            ->joinInner(array('p' => $this->getTable('catalog/product')), 'o.product_id=p.entity_id', array())
            ->where('c.attribute_id=?', $iAttributeId)
            ->order('c.store_id ASC')
        ;
        
        $aItemList = $oDb->fetchPairs($select);
        
        return $aItemList;
    }
    
}