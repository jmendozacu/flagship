<?php

class MDN_BarcodeLabel_Helper_BarcodePicture extends Mage_Core_Helper_Abstract {

    /**
     * Return barcode image
     */
    public function getImage($barcode) {

        $barcodeOptions = array('text' => $barcode); // barcode attribut (not sku!)
        $rendererOptions = array();

        $barcodeStandard = Mage::getStoreConfig('barcodelabel/general/standard');

        $factory = Zend_Barcode::factory($barcodeStandard, 'image', $barcodeOptions, $rendererOptions);

        $image = $factory->draw();
        return $image;
    }

}
