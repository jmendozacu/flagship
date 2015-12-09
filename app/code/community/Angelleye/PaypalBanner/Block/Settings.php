<?php

class Angelleye_PaypalBanner_Block_Settings extends Mage_Core_Block_Template
{
    /**
     * Get predefined value for setting paypal name (taken from default page title)
     * @return string|null
     */
    public function getPaypalName()
    {
        return Mage::getStoreConfig('design/head/default_title');
    }

    /**
     * Get predefined value for setting paypal email (taken from paypal standard settings)
     * @return string|null
     */
    public function getPaypalEmail()
    {
        return Mage::getStoreConfig('paypal/general/business_account');
    }
}