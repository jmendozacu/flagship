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
 * Abstract class
 *
 * @package CLS_AddShoppers
 * @copyright Copyright (c) 2012 Classy Llama Studios, LLC
 * @author Nicholas Vahalik <nick@classyllama.com>
 */
class CLS_AddShoppers_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * Returns the store account ID
     *
     * @return string AddShoppers Account ID
     */
    public function getAccountId() {
        return Mage::getStoreConfig('cls_addshoppers/settings/account_id');
    }
}