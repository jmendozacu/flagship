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
 * @package    MageWorx_Tweaks
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Magento Tweaks extension
 *
 * @category   MageWorx
 * @package    MageWorx_Tweaks
 * @author     MageWorx Dev Team
 */

class MageWorx_OrdersPro_Block_Sales_Order_Recent extends Mage_Sales_Block_Order_Recent
{

    public function __construct()
    {        
        parent::__construct();
        if (Mage::helper('orderspro')->isHideDeletedOrdersForCustomers()) {            
            $orders = Mage::getResourceModel('orderspro/order_collection')
                ->addAttributeToSelect('*')
                ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                ->hideDeletedGroup()
                ->addAttributeToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addAttributeToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->addAttributeToSort('created_at', 'desc')
                ->setPageSize('5')
                ->load();

            $this->setOrders($orders);            
        }
    }        
    
}
