<?php
class Angelleye_PaypalBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Configuration-controller-action map
     * @var array
     */
    public static $_codeMap = array(
        'catalog.product.view'=>'catalog_product',
        'catalog.category.view'=>'catalog_category',
        'cms.index.index'=>'homepage',
        'checkout.cart.index'=>'checkout_cart'
    );

    /**
     * Get configuration settings for current page
     * @return Varien_Object $config
     */
    public function getSectionConfig()
    {
        $config = new Varien_Object();
        $module = Mage::app()->getRequest()->getModuleName();
        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();

        if (isset(self::$_codeMap[$module.'.'.$controller.'.'.$action])){
            $pageCode = self::$_codeMap[$module.'.'.$controller.'.'.$action];
            $size = Mage::getStoreConfig('paypalbanner/'.$pageCode.'/size');
            $position = Mage::getStoreConfig('paypalbanner/'.$pageCode.'/position');
            list($positionHorizontal, $positionVertical) = explode('-',$position);
            $display = Mage::getStoreConfig('paypalbanner/'.$pageCode.'/display');
            $config->setPageCode($pageCode)
                ->setDisplay($display)
                ->setSize($size)
                ->setPositionHorizontal($positionHorizontal)
                ->setPositionVertical($positionVertical);
        }
        return $config;
    }

    /**
     * Get html code for banner snippet
     *
     * @param string $pageCode
     * @return string $snippet
     */
    public function getSnippetCode($pageCode='')
    {
        if (!Mage::getStoreConfig('paypalbanner/settings/active') || !$this->getSectionConfig()->getDisplay()) {
            return '';
        }

        $id = Mage::getStoreConfig('paypalbanner/settings/id');
        $container = Mage::getStoreConfig('paypalbanner/settings/container');
        $size = $this->getSectionConfig()->getSize();

        $snippet  = '<script type="text/javascript" data-pp-pubid="'.$id.'" data-pp-placementtype="'.$size.'" data-pp-channel = "Magento Extension" data-pp-td = \'{"d":{"segments": {"cart_price": "'.$this->getCartPrice().'", "item_price":"'.$this->getItemPrice().'","page_name": "'.$this->getPageName().'"}}}\'>
    (function (d, t) {
        "use strict";
        var s = d.getElementsByTagName(t)[0], n = d.createElement(t);
        n.src = "//paypal.adtag.where.com/merchant.js";
        s.parentNode.insertBefore(n, s);
    }(document, "script"));
</script>';
        if (!empty($container)) {
            $snippet = str_replace('{container}', $snippet, $container);
        }
        return $snippet;
    }

    /**
     * Get cart total price
     * @return string
     */
    public  function getCartPrice()
    {
        $price = (string)(Mage::helper('checkout/cart')->getQuote()->getGrandTotal()>0 ?
            Mage::app()->getStore()->formatPrice(
                Mage::helper('checkout/cart')->getQuote()->getGrandTotal(), false) : '');

         if ($price[0] == '$'){
             $price = substr($price, 1);
         }
         return $price;
    }

    /**
     * Get item price (in case customer on product view page)
     * @return string
     */
    public function getItemPrice()
    {
        $request = Mage::app()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ($module.$controller.$action == 'catalogproductview'){
            $prodId = $request->getParam('id');
            $product = Mage::getModel('catalog/product')->load($prodId);
            if ($product && $product->getPrice()){
                $price = (string)Mage::app()->getStore()->formatPrice($product->getPrice(), false);
            }
            if ($price[0] == '$'){
                $price = substr($price, 1);
            }
            return $price;
        }
        return '';
    }

    /**
     * Get page name
     * @return string
     */
    public  function getPageName()
    {
        $path = Mage::app()->getRequest()->getOriginalPathInfo();
        if (empty($path) || $path == '/'){
            return 'home';
        } else if ($path[0] == '/' || $path[strlen($path)-1]=='/'){
            if ($path[0] == '/'){
                $path = substr($path, 1);
            }
            if ($path[strlen($path)-1]=='/'){
                $path = substr($path, 0, -1);
            }
        }
        return $path;
    }
}