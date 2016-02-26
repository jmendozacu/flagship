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
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

class MageWorx_OrdersPro_Model_System_Config_Source_Orders_Customer_Grid
{   

    public function toOptionArray($isMultiselect=false)
    {                
        $options = array(            
            array('value'=>'increment_id', 'label'=> Mage::helper('customer')->__('Order #')),            
            array('value'=>'created_at', 'label'=> Mage::helper('customer')->__('Purchase On')),
            
            array('value'=>'product_names', 'label'=> Mage::helper('orderspro')->__('Product(s) Name(s)')),
            array('value'=>'product_skus', 'label'=> Mage::helper('orderspro')->__('SKU(s)')),
            
            array('value'=>'qnty', 'label'=> Mage::helper('orderspro')->__('Qnty')),
            array('value'=>'billing_name', 'label'=> Mage::helper('customer')->__('Bill to Name')),
            array('value'=>'shipping_name', 'label'=> Mage::helper('customer')->__('Shipped to Name')),
            array('value'=>'shipping_method', 'label'=> Mage::helper('orderspro')->__('Shipping Method')),
            array('value'=>'shipped', 'label'=> Mage::helper('orderspro')->__('Shipped')),
            array('value'=>'customer_email', 'label'=> Mage::helper('orderspro')->__('Customer Email')),
            array('value'=>'customer_group', 'label'=> Mage::helper('orderspro')->__('Customer Group')),
            array('value'=>'payment_method', 'label'=> Mage::helper('orderspro')->__('Payment Method')),
            array('value'=>'tax_amount', 'label'=> Mage::helper('orderspro')->__('Tax Amount')),
            //array('value'=>'tax_percent', 'label'=> Mage::helper('orderspro')->__('Tax Percent')),
            
            array('value'=>'coupon_code', 'label'=> Mage::helper('orderspro')->__('Coupon Code')),
            array('value'=>'discount_amount', 'label'=> Mage::helper('orderspro')->__('Discount')),
            
            array('value'=>'internal_credit', 'label'=> Mage::helper('orderspro')->__('Internal Credit')), // 15
            array('value'=>'total_refunded', 'label'=> Mage::helper('orderspro')->__('Total Refunded')),            
            array('value'=>'grand_total', 'label'=> Mage::helper('customer')->__('Order Total')),
            array('value'=>'order_group', 'label'=> Mage::helper('orderspro')->__('Group')),
            array('value'=>'store_id', 'label'=> Mage::helper('customer')->__('Bought From')),
            array('value'=>'is_edited', 'label'=> Mage::helper('orderspro')->__('Edited')),
            array('value'=>'status', 'label'=> Mage::helper('sales')->__('Status')),
            array('value'=>'action', 'label'=> Mage::helper('customer')->__('Action'))
        );
        
        if (!Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
            unset($options[15]); // Internal Credit
        }                
        //if (!$isMultiselect) array_pop($options);

        return $options;
    }
}