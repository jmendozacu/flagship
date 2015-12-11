<?php
class Angelleye_PaypalBanner_Model_Config extends Mage_Paypal_Model_Config
{
    /**
     * BN code getter
     *
     * @param string $countryCode ISO 3166-1
     */
    public function getBuildNotationCode($countryCode = null)
    {
       return 'AngellEYE_PHPClass';
    }
}