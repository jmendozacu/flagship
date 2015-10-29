<?php
/**
 * @category    Fishpig
 * @package     Fishpig_iBanners
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_iBanners_Block_View extends Mage_Core_Block_Template
{
	/**
	 * Determine whether a valid group is set
	 *
	 * @return bool
	 */
	public function hasValidGroup()
	{
		return is_object($this->getGroup());
	}

	/**
	 * Retrieve the ID used for the wrapper div
	 *
	 * @return string
	 */
	public function getWrapperId()
	{
		return 'ibanners-' . $this->getGroupCode();
	}

	/**
	 * Set the group code
	 * The group code is validated before being set
	 *
	 * @param string $code
	 */
	public function setGroupCode($code)
	{
		$currentGroupCode = $this->getGroupCode();
		
		if ($currentGroupCode != $code) {
			$this->setGroup(null);
			$this->setData('group_code', null);

			$group = Mage::getModel('ibanners/group')->loadByCode($code);

			if ($group->getId()) {
				if (in_array($group->getStoreId(), array(0, Mage::app()->getStore()->getId()))) {
					$this->setGroup($group);
					$this->setData('group_code', $code);
				}
			}
		}
		
		return $this;
	}

	/**
	 * Retrieve a collection of banners
	 *
	 * @return Fishpig_iBanners_Model_Mysql4_Banner_Collection
	 */
	public function getBanners()
	{
		return $this->getGroup()->getBannerCollection();
	}
}
