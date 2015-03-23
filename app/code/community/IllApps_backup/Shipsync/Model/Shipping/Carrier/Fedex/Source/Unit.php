<?php

/**
 * ShipSync Community
 *
 * @category   IllApps
 * @package    IllApps_Shipsync
 * @author     David Kirby (d@kernelhack.com)
 * @copyright  Copyright (c) 2012 EcoMATICS, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * IllApps_Shipsync_Model_Shipping_Carrier_Fedex_Source_Unit
 */
class IllApps_Shipsync_Model_Shipping_Carrier_Fedex_Source_Unit
{

    /**
     * toOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
	return array
	(
	    array('value' => 'LB_IN', 'label' => Mage::helper('usa')->__('Pounds / Inches')),
	    array('value' => 'KG_CM', 'label' => Mage::helper('usa')->__('Kilograms / Centimeters')),
	    array('value' =>  'G_CM', 'label' => Mage::helper('usa')->__('Grams / Centimeters'))
	);
    }

}