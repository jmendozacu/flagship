<?php
/**
 * ListMode.php
 * CommerceThemes @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commercethemes.com/LICENSE-M1.txt
 *
 * @category   Listmode
 * @package    Hide Product Price For Non Registered Users
 * @copyright  Copyright (c) 2003-2009 CommerceThemes @ InterSEC Solutions LLC. (http://www.commercethemes.com)
 * @license    http://www.commercethemes.com/LICENSE-M1.txt
 */
class CommerceThemes_HideProductPrice_Model_Config_Source_ListMode
{
    public function toOptionArray()
    {
				$groups = array();
				$groups = Mage::helper('customer')->getGroups()->toOptionArray();
				return $groups;
        #return array(
            #array('value'=>'grid', 'label'=>Mage::helper('adminhtml')->__('Grid')),
            #array('value'=>'list', 'label'=>Mage::helper('adminhtml')->__('List')),
        #);
    }
}
