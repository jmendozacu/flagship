<?php
/**
 * Toolbar.php
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
 * @package    Block_Product_List_Toolbar
 * @copyright  Copyright (c) 2003-2009 MageWidgets @ InterSEC Solutions LLC. (http://www.magewidgets.com)
 * @license    http://www.magewidgets.com/LICENSE-M1.txt
 */ 
 
class MageWidgets_FeaturedProduct_Block_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    public function _toHtml()
    {
        #$mode = (bool)Mage::getStoreConfig('featuredproduct/featuredproduct/has_toolbar');  //system - global level
				$mode = (bool)$this->getParentBlock()->getData('has_toolbar'); // widget level
				
				 if ($mode) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    public function getCurrentMode()
    {
        $mode = $this->getParentBlock()->getDisplayMode();
        if ($mode) {
            return $mode;
        } else {
            return Mage::getStoreConfig('featuredproduct/featuredproduct/list_mode');
        }
    }

    public function getLimit()
    {
        return intval(Mage::getStoreConfig('featuredproduct/featuredproduct/num_displayed_products'));
    }

}
