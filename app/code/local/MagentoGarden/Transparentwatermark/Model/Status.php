<?php
/**
 * MagentoGarden
 *
 * @category    model
 * @package     magentogarden_transparentwatermark
 * @copyright   Copyright (c) 2012 MagentoGarden Inc. (http://www.magentogarden.com)
 * @version		1.3.0
 * @author		MagentoGarden (coder@magentogarden.com)
 */


class MagentoGarden_Transparentwatermark_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('transparentwatermark')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('transparentwatermark')->__('Disabled')
        );
    }
}