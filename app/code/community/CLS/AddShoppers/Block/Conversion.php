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
 * Conversion track code block
 *
 * @package CLS_AddShoppers
 * @copyright Copyright (c) 2012 Classy Llama Studios, LLC
 * @author Nicholas Vahalik <nick@classyllama.com>
 */
class CLS_AddShoppers_Block_Conversion extends CLS_AddShoppers_Block_Abstract
{
    /**
     * Stores the order ID for the conversion block
     *
     * @var int
     */
    private $_orderId;

    /**
     * Grabs the order ID of the previously placed order.
     *
     * @return int
     * @copyright Copyright (c) 2012 Classy Llama Studios, LLC
     * @author Nicholas Vahalik <nick@classyllama.com>
     */
    public function getOrderId() {
        if ($this->_orderId == null) {
            $this->_orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        }
        return $this->_orderId;
    }

    /**
     * Gets the amount of the last placed order.
     *
     * @return float
     * @copyright Copyright (c) 2012 Classy Llama Studios, LLC
     * @author Nicholas Vahalik <nick@classyllama.com>
     */
    public function getAmount() {
        return round(Mage::getModel('sales/order')->loadByIncrementId($this->getOrderId())->subtotal, 2); 
    }
}
