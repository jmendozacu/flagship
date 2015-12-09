<?php
class Angelleye_PaypalBanner_Block_Banner extends Mage_Core_Block_Template
{
    /**
     * Build an banners html code output
     * @return mixed
     */
    public function getBannerCode()
    {
        return Mage::helper('paypalbanner')->getSnippetCode();
    }

}