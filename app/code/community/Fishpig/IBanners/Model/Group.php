<?php
/**
 * @category    Fishpig
 * @package     Fishpig_iBanners
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_iBanners_Model_Group extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		$this->_init('ibanners/group');
	}
	
	/**
	 * Load the model based on the code field
	 *
	 * @param string $code
	 * @return Fishpig_iBanners_Model_Group
	 */
	public function loadByCode($code)
	{
		return $this->load($code, 'code');
	}
	
	/**
	 * Retrieve a collection of banners associated with this group
	 *
	 * @return Fishpig_iBanners_Model_Mysql4_Banner_Group
	 */
	public function getBannerCollection()
	{
		if (!$this->hasBannerCollection()) {
			$this->setBannerCollection($this->getResource()->getBannerCollection($this));
		}
		
		return $this->getData('banner_collection');
	}
	
	/**
	 * Retrieve the amount of banners in this group
	 *
	 * @return int
	 */
	public function getBannerCount()
	{
		if (!$this->hasBannerCount()) {
			$this->setBannerCount($this->getBannerCollection()->count());
		}
		
		return $this->getData('banner_count');
	}
	
	/**
	 * Determine whether animation is enabled for this group
	 *
	 * @return bool
	 */
	public function isAnimationEnabled()
	{
		return $this->getCarouselAnimate() ? true : false;
	}
	
	/**
	 * Retrieve the carousel duration for this group
	 *
	 * @return int
	 */
	public function getCarouselDuration()
	{
		if (!$this->getData('carousel_duration')) {
			$duration = (int)Mage::getStoreConfig('ibanners/carousel/duration');
			$this->setCarouselDuration($duration > 0 ? $duration : 1);
		}

		return (int)$this->getData('carousel_duration');
	}
	
	/**
	 * Retrieve the carousel duration for this group
	 *
	 * @return int
	 */
	public function getCarouselAuto()
	{
		if ($this->getData('carousel_auto') == '') {
			$duration = (int)Mage::getStoreConfig('ibanners/carousel/auto');
			$this->setCarouselAuto($duration ? 1 : 0);
		}
		
		return (int)$this->getData('carousel_auto');
	}
	
	/**
	 * Retrieve the carousel duration for this group
	 *
	 * @return int
	 */
	public function getCarouselFrequency()
	{
		if (!$this->getData('carousel_frequency')) {
			$frequency = (int)Mage::getStoreConfig('ibanners/carousel/frequency');
			$this->setCarouselFrequency($frequency > 1 ? $frequency : 8);
		}
		
		return (int)$this->getData('carousel_frequency');
	}
	
	/**
	 * Retrieve the carousel duration for this group
	 *
	 * @return int
	 */
	public function getCarouselVisibleSlides()
	{
		if (!$this->getData('carousel_visible_slides')) {
			$visibleSlides = (int)Mage::getStoreConfig('ibanners/carousel/visible_slides');
			$this->setCarouselVisibleSlides($visibleSlides > 0 ? $visibleSlides : 1);
		}
		
		return (int)$this->getData('carousel_visible_slides');
	}
	
	/**
	 * Retrieve the carousel effect for this group
	 * If no carousel effect is set, get the carousel effect from the config
	 *
	 * @return string
	 */
	public function getCarouselEffect()
	{
		if (!$this->getData('carousel_effect')) {
			$this->setCarouselEffect(Mage::getStoreConfig('ibanners/carousel/effect'));
		}
		
		return $this->getData('carousel_effect');
	}

	/**
	 * Retrieve the carousel transition for this group
	 * If no carousel transition is set, get the carousel transition from the config
	 *
	 * @return string
	 */
	public function getCarouselTransition()
	{
		if (!$this->getData('carousel_transition')) {
			$this->setCarouselTransition(Mage::getStoreConfig('ibanners/carousel/transition'));
		}
		
		return $this->getData('carousel_transition');
	}
}
