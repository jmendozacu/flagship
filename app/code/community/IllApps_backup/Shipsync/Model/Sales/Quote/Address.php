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
 * IllApps_Shipsync_Model_Sales_Quote_Address
 */
class IllApps_Shipsync_Model_Sales_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    

    /**
     * validate
     *
     * @return mixed
     */
    public function validate()
    {
	// Get address validation model
	$fedexAddress = Mage::getModel('usa/shipping_carrier_fedex_address');

	// If shipping address
	if ($this->getAddressType() == 'shipping')
	{
	    // If street is present
	    if ($this->getStreet(-1))
	    {
		// If PO Box filtering is enabled and street is a PO Box
		if (Mage::getStoreConfig('carriers/fedex/filter_po_boxes') && $fedexAddress->isPostOfficeBox($this->getStreet(1)))
		{
		    return array(Mage::helper('customer')->__("We're sorry, we do not ship to PO Boxes."));
		}

		// If country is US and address validation is enabled
		if ($this->getCountryId() == 'US' && Mage::getStoreConfig('carriers/fedex/address_validation'))
		{
		    // Validate address
		    $fedexAddress->validate($this);                    

		    // Check for error
		    if ($fedexAddress->getAddressResultError())
		    { 
			return $fedexAddress->getAddressResultError();
		    }
		}
	    }
        }

	// Return true for all other addresses
	return true;
    }    
}