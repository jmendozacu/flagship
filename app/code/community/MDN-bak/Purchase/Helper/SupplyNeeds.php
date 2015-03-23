<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Purchase_Helper_SupplyNeeds extends Mage_Core_Helper_Abstract {

    /**
     * Update prefered stock level
     */
    public function updatePreferedStockLevel($productId) {
        //update sales history
        mage::helper('AdvancedStock/Sales_History')->RefreshForOneProduct($productId);

        //update prefered stock level
        mage::helper('AdvancedStock/Product_PreferedStockLevel')->updateForProduct($productId);

        //execute "update supply needs" task if scheduled
        mage::helper('BackgroundTask')->forceTaskExecution('purchase', 'updateSupplyNeedsForProduct', $productId);
    }

}