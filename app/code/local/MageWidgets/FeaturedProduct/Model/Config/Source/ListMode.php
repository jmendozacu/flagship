<?php
/**
 * ListMode.php
 * MageWidgets @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magewidgets.com/LICENSE-M1.txt
 *
 * @category   Catalog
 * @package    Model_Config_Source_ListMode
 * @copyright  Copyright (c) 2003-2009 MageWidgets @ InterSEC Solutions LLC. (http://www.magewidgets.com)
 * @license    http://www.magewidgets.com/LICENSE-M1.txt
 */ 
class MageWidgets_FeaturedProduct_Model_Config_Source_ListMode
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'grid', 'label'=>Mage::helper('adminhtml')->__('Grid')),
            array('value'=>'list', 'label'=>Mage::helper('adminhtml')->__('List')),
        );
    }
}
