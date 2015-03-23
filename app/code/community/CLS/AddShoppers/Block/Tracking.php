<?php
/**
 * CLS_AddShoppers
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@classyllama.com so we can send you a copy immediately.
 *
 * @category    Code
 * @package     CLS_AddShoppers
 * @copyright   Copyright (c) 2012 Classy Llama Studios, LLC (classyllama.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tracking code block
 *
 * @package CLS_AddShoppers
 * @copyright Copyright (c) 2012 Classy Llama Studios, LLC
 * @author Nicholas Vahalik <nick@classyllama.com>
 */
class CLS_AddShoppers_Block_Tracking extends CLS_AddShoppers_Block_Abstract
{
    /**
     * If enabled, output the tracking code.
     *
     * @return string HTML Code
     */
    public function _toHtml() {
        if (Mage::getStoreConfigFlag('cls_addshoppers/settings/enabled')) {
            return parent::_toHtml();
        }
    }

    /**
     * Returns TRUE if using the schema for finding the image.
     *
     * @return boolean
     */
    public function isUsingSchema() {
        return Mage::getStoreConfigFlag('cls_addshoppers/settings/use_schema');
    }

    /**
     * Returns the image url of the current product.
     *
     * @return string Image URL of current product.
     */
    public function getProductImage() {
        $currentProduct = Mage::registry('current_product');
        if ($currentProduct) {
            return $currentProduct->getImageUrl();
        }
        return '';
    }

    /**
     * Returns the product's canonical URL
     *
     * @return string Canonical URL page
     **/
    public function getProductUrl() {
        return Mage::registry(‘product’)->getProductUrl();
    }

    /**
     * Returns the product's name.
     *
     * @return string Canonical URL page
     **/
    public function getProductDescription() {
        return Mage::registry(‘product’)->getProductUrl();
    }

    /**
     * Returns the JSON-encoded configuration for the Tracking system.
     *
     * @return string
     **/
    public function getJSONConfig() {
        $data = array();

        $product = Mage::registry('product');

        if (!$this->isUsingSchema()) {
            $data['image'] = $this->getProductImage();
            $data['url'] = $product->getProductUrl();
            $data['product'] = $product->getName();
            $data['description'] = $product->getDescription();
            $data['stock'] = $product->getIsInStock() ? 'In stock' : 'Out of stock';
            $data['price'] = round($product->getPrice(), 2);

            $ratingData = Mage::getModel('rating/rating')->getEntitySummary($product->getId());
            if ($ratingData->getCount() > 0) {
                $ratingAverage = ($ratingData->getSum() / $ratingData->getCount()) / 20 ;
                $data['rating'] = round($ratingAverage);
            }
        }

        return Mage::helper('core')->jsonEncode((object)$data);
    }
}
