<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */
class MageWorx_Adminhtml_Model_System_Config_Backend_Orderspro_Menu extends Mage_Core_Model_Config_Data
{

    const ENABLED_MENU_ORDERS = 'mageworx_sales/orderspro/enabled_menu_orders';    

    protected function _afterSave()
    {
        $enabled = $this->getData('groups/orderspro/fields/enabled/value');
        if ($enabled) $value = 0; else $value = 1;
        try {
            Mage::getModel('core/config_data')
                ->load(self::ENABLED_MENU_ORDERS, 'path')
                ->setValue($value)
                ->setPath(self::ENABLED_MENU_ORDERS)
                ->save();
            
        } catch (Exception $e) {
            //throw new Exception(Mage::helper('cron')->__('Unable to save Cron expression'));
        }
    }

}
