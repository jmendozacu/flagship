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
 * IllApps_Shipsync_Model_Shipping_Carrier_Fedex_Source_Label_Orientation
 */
class IllApps_Shipsync_Model_Shipping_Carrier_Fedex_Source_Label_Orientation
{

    
    /**
     * toOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        $fedex = Mage::getSingleton('usa/shipping_carrier_fedex');

        $arr = array();

        foreach ($fedex->getCode('label_orientation') as $key => $value)
	{
            $arr[] = array('value' => $key, 'label' => $value);
        }

	return $arr;
    }

}