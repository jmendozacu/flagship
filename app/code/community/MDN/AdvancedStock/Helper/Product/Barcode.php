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
class MDN_AdvancedStock_Helper_Product_Barcode extends Mage_Core_Helper_Abstract {

    /**
     * Return barcodes list for 1 product
     *
     * @param unknown_type $productId
     */
    public function getBarcodesForProduct($productId) {
        $collection = mage::getModel('AdvancedStock/ProductBarcode')
                        ->getCollection()
                        ->addFieldToFilter('ppb_product_id', $productId);

        return $collection;
    }

    public function saveBarcodesFromString($productId, $string) {
        $t = explode("\r\n", $string);

        $collection = $this->getBarcodesForProduct($productId);
        foreach ($collection as $item) {
            //check if we have to delete a barcode
            if (!in_array($item->getppb_barcode(), $t))
                $item->delete();
            else
                $t = array_diff($t, array($item->getppb_barcode()));
        }

        //barcodes that still in $t have to be inserted
        $hadBarcodes = (count($t) > 0);
        foreach ($t as $barcode) {
            if ($barcode == '')
                continue;

            mage::getModel('AdvancedStock/ProductBarcode')
                    ->setppb_product_id($productId)
                    ->setppb_barcode($barcode)
                    ->setppb_is_main($hadBarcodes)
                    ->save();

            $hadBarcodes = true;
        }
    }

    /**
     * Add a barcode if doesn't exists
     *
     * @param unknown_type $productId
     * @param unknown_type $barcode
     */
    public function addBarcodeIfNotExists($productId, $barcode) {
        if (!$this->barcodeExists($barcode)) {
            mage::getModel('AdvancedStock/ProductBarcode')
                    ->setppb_product_id($productId)
                    ->setppb_barcode($barcode)
                    ->setppb_is_main(1)
                    ->save();
            return true;
        }
        return false;
    }

    /**
     * Check if a barcode exists
     *
     * @param unknown_type $barcode
     */
    public function barcodeExists($barcode) {
        $collection = mage::getModel('AdvancedStock/ProductBarcode')
                        ->getCollection()
                        ->addFieldToFilter('ppb_barcode', $barcode);
        return ($collection->getSize() > 0);
    }

    /**
     * Return a product from a barcode
     *
     * @param unknown_type $barcode
     */
    public function getProductFromBarcode($barcode) {
        $product = null;
	//echo trim($barcode).'---'.strlen(trim($barcode));
        $object = mage::getModel('AdvancedStock/ProductBarcode')->load(trim($barcode), 'ppb_barcode');
	
        if ($object->getId()){
		$product = mage::getModel('catalog/product')->load($object->getppb_product_id());
	}

	return $product;
    }

    /**
     * Return main barcode for product
     *
     */
    public function getBarcodeForProduct($product) {
        $productId = null;
        if (is_object($product))
            $productId = $product->getId();
        else
            $productId = $product;

        $barcodes = mage::getModel('AdvancedStock/ProductBarcode')
                        ->getCollection()
                        ->addFieldToFilter('ppb_product_id', $productId)
                        ->addFieldToFilter('ppb_is_main', 1);

        $retour = null;
        foreach ($barcodes as $item)
            $retour = $item->getppb_barcode();

        return $retour;
    }

    /**
     * Return barcode picture
     *
     * @param unknown_type $barcode
     * @edited Second argument is Added by Arun to make the Encoded type Dynamic 
     *         Previously it was set to ANY only and was not working for $barcode of length 10 or 9. 
     */
    public function getBarcodePicture($barcode,$type = 'ANY') {
        $obj = new clsBarcode();
        return $obj->barcode_print($barcode, $type, 2, 'png',true);
    }

    /**
     * Return main barcode picture for product
     *
     * @param unknown_type $product
     * @return unknown
     */
    public function getBarcodePictureForProduct($product) {
        $barcode = $this->getBarcodeForProduct($product);
        return $this->getBarcodePicture($barcode);
    }

}
