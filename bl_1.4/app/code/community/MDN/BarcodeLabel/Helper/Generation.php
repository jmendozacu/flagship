<?php

class MDN_BarcodeLabel_Helper_Generation extends Mage_Core_Helper_Abstract {

    /**
     * Generate barcodes for all products
     */
    public function generateForAllProducts() {
        //config check
        Mage::helper('BarcodeLabel')->checkConfiguration();
        
         // select all products with attribute 'barcode' empty
         $productWithoutEan = mage::getmodel('catalog/product')
                ->getCollection()
                ->addAttributeToFilter(Mage::helper('BarcodeLabel')->getBarcodeAttribute(), array('null' => ''));  
        
        //generate barcodes
        foreach ($productWithoutEan as $product) {

             $this->storeBarcode($product->getId());
        }
        
    }

    /**
     * Generate (and save) barcode for one product
     * @param <type> $productId
     */
    public function storeBarcode($productId) {

        //generate barcode
        $barcode = $this->getBarcodeForProduct($productId);

        //save into product
        Mage::getSingleton('catalog/product_action')
                ->updateAttributes(array($productId), array(Mage::helper('BarcodeLabel')->getBarcodeAttribute() => $barcode), 0);

        return $barcode;
    }

    /**
     * Generate barcode for product
     */
    protected function getBarcodeForProduct($productId) {
        $barcode = str_pad($productId, 12, '0', STR_PAD_LEFT);
        $barcode .= $this->getControlKey($barcode);
        return $barcode;
    }

    /**
     * Return control key for barcode
     * @param <type> $ean13
     * @return <type>
     */
    protected function getControlKey($ean13) {

        $sum = 0;

        for ($index = 0; $index < 12; $index++) {
            $number = (int) $ean13[$index];
            if (($index % 2) != 0)
                $number *= 3;
            $sum += $number;
        }

        $resteDivision = $sum % 10;

        if ($resteDivision == 0)
            return 0;
        else
            return 10 - $resteDivision;
    }

}