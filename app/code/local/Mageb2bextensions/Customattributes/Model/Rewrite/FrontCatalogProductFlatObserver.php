<?php
/**
 * FrontCatalogProductFlatObserver.php
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
class Mageb2bextensions_Customattributes_Model_Rewrite_FrontCatalogProductFlatObserver extends Mage_Catalog_Model_Product_Flat_Observer
{
    public function catalogEntityAttributeSaveAfter(Varien_Event_Observer $observer)
    {
        if ($observer->getEvent()->getAttribute()->getData('mb2bflag'))
        {
            return $this;
        }
        return parent::catalogEntityAttributeSaveAfter($observer);
    }
}
