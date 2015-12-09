<?php
/**
 * MagentoGarden
 *
 * @category    helper
 * @package     magentogarden_transparentwatermark
 * @copyright   Copyright (c) 2012 MagentoGarden Inc. (http://www.magentogarden.com)
 * @version		1.3.0
 * @author		MagentoGarden (coder@magentogarden.com)
 */


class MagentoGarden_Transparentwatermark_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * @return bool
	 */
	public function isEnabled() {
		return Mage::getStoreConfig('magentogarden_transparentwatermark/general/enabled_transparentwatermark') == 1;
	}
	
	/**
	 * @return bool
	 */
	public function isUseCustomPosition() {
		return Mage::getStoreConfig('magentogarden_transparentwatermark/general/enabled_custom_position') == 1;
	}
	
	/**
	 * @return string
	 */
	public function getSampleProductImage() {
		$_image = Mage::getStoreConfig('magentogarden_transparentwatermark/general/transparentwatermark_sample_product');
		$_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'MagentoGarden'.'/'.'transparentwatermark'.'/'.$_image;
		return $_url;
	}
	
	/**
	 * @return string
	 */
	public function getWatermark() {
		$_image = Mage::getStoreConfig("design/watermark/image_image");
		$_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog'.'/'.'product'.'/'.'watermark'.'/'.$_image;
		return $_url;
	}
	
	/**
	 * @return int
	 */
	public function getSampleProductImageWidth() {
		return Mage::getStoreConfig('magentogarden_transparentwatermark/general/transparentwatermark_sample_image_width');
	}
	
	/**
	 * @return int
	 */
	public function getSampleProductImageHeight() {
		return Mage::getStoreConfig('magentogarden_transparentwatermark/general/transparentwatermark_sample_image_height');
	}
	
	/**
	 * @return int
	 */
	public function getCustomWatermarkX() {
		return Mage::getStoreConfig('magentogarden_transparentwatermark/general/transparentwatermark_custom_position_x');
	}

	/** 
	 * @return int
	 */
	public function getCustomWatermarkY() {
		return Mage::getStoreConfig('magentogarden_transparentwatermark/general/transparentwatermark_custom_position_y');
	}
}