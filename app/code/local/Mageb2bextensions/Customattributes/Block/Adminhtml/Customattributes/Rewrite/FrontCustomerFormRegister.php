<?php
/**
 * FrontCustomerFormRegister.php
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
class Mageb2bextensions_Customattributes_Block_Adminhtml_Customattributes_Rewrite_FrontCustomerFormRegister extends Mage_Customer_Block_Form_Register
{
    protected function _construct()
    {
        Mage::getModel('customattributes/customattributes')->checkDatabaseInstall();
        parent::_construct();
    }
    
    public function getFieldHtml($aField)
    {
        $sSetName = 'customer';
        return Mage::getModel('customattributes/customattributes')->getAttributeHtml($aField, $sSetName, 'registerpage');
    }
    
    public function getCustomFieldList($iTplPlaceId)
    {
        $iStepId = Mage::helper('customattributes')->getStepId('customer');
        if (!$iStepId) return false;
        return Mage::getModel('customattributes/customattributes')->getCheckoutAtrributeList($iStepId, $iTplPlaceId, 'registerpage');
    } 
}