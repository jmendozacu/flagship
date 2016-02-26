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

class MageWorx_Adminhtml_Block_Orderspro_Customer_Edit_Tab_Orders extends Mage_Adminhtml_Block_Customer_Edit_Tab_Orders
{
    public function __construct() {
        parent::__construct();
        $this->setDefaultFilter(array('order_group'=>0)); // Actual
    }
    
    protected function _prepareColumns() {   
        $helper = Mage::helper('orderspro');
        if (!$helper->isEnabled()) return parent::_prepareColumns();        
        
        $listColumns = $helper->getCustomerGridColumns();        
        $currencyCode = $helper->getCurrentCurrencyCode();
        
        foreach ($listColumns as $column) {
            switch ($column) {
                
                // standard fields                
                case 'increment_id':
                    $this->addColumn('increment_id', array(
                        'header'=> Mage::helper('customer')->__('Order #'),
                        'width' => '80px',
                        'type'  => 'text',
                        'index' => 'increment_id',
                    ));
                break;                
                
                case 'created_at':
                    $this->addColumn('created_at', array(
                        'header' => Mage::helper('customer')->__('Purchase On'),
                        'index' => 'created_at',
                        'type' => 'datetime',
                        'width' => '100px',
                    ));
                break;

                case 'billing_name':
                    $this->addColumn('billing_name', array(
                        'header' => Mage::helper('customer')->__('Bill to Name'),
                        'index' => 'billing_name',
                    ));
                break;    

                case 'shipping_name':
                    $this->addColumn('shipping_name', array(
                        'header' => Mage::helper('customer')->__('Shiped to Name'),
                        'index' => 'shipping_name',
                    ));
                break;
                    
                case 'grand_total':
                    $this->addColumn('grand_total', array(
                        'header' => Mage::helper('customer')->__('Order Total'),
                        'index' => 'grand_total',
                        'type'  => 'currency',
                        'currency' => 'order_currency_code',
                    ));
                break;    
                    
                case 'store_id':
                    if (!Mage::app()->isSingleStoreMode()) {
                        $this->addColumn('store_id', array(
                            'header'    => Mage::helper('customer')->__('Bought From'),
                            'index'     => 'store_id',
                            'type'      => 'store',
                            'store_view'=> true
                        ));
                    }
                break;

                case 'action':                    
                    $this->addColumn('action', array(
                        'header'    => ' ',
                        'filter'    => false,
                        'sortable'  => false,
                        'width'     => '100px',
                        'renderer'  => 'adminhtml/sales_reorder_renderer_action'
                    ));
                    
                break;
                
                // additional fields
                
                case 'product_names':
                    $this->addColumn('product_names', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_products',
                        'header' => $helper->__('Product(s) Name(s)'),
                        'index' => 'name'                        
                        ));
                break;    

                case 'product_skus':
                    $this->addColumn('product_skus', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_products',
                        'header' => $helper->__('SKU(s)'),
                        'index' => 'skus'                        
                        ));
                break;

                case 'customer_email':
                    $this->addColumn('customer_email', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Customer Email'),
                        'index' => 'customer_email'       
                        ));
                break;
            

                case 'customer_group':
                    $this->addColumn('customer_group', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',
                        'type'  => 'options',
                        'options' => $helper->getCustomerGroups(),
                        'header' => $helper->__('Customer Group'),
                        'index' => 'customer_group_id',
                        'align' => 'center'
                        ));
                break;    
                    

                case 'payment_method':
                    $this->addColumn('payment_method', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',
                        'type'  => 'options',
                        'options' => $helper->getAllPaymentMethods(),
                        'header' => $helper->__('Payment Method'),
                        'index' => 'method',
                        'align' => 'center'
                        ));
                break;
                    

                case 'total_refunded':
                    $this->addColumn('total_refunded', array(                            
                        'type'  => 'currency',
                        'currency_code' => $currencyCode,                
                        'header' => $helper->__('Total Refunded'),
                        'index' => 'total_refunded',
                        'total' => 'sum'
                        ));
                break;    
                    

                case 'shipping_method':
                    $this->addColumn('shipping_method', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',
                        'type'  => 'options',
                        'options' => $helper->getAllShippingMethods(),
                        'header' => $helper->__('Shipping Method'),
                        'index' => 'shipping_method',
                        'align' => 'center'
                        ));
                break;
            
            
                case 'shipped':
                    $this->addColumn('shipped', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',                
                        'type'  => 'options',
                        'options' => $helper->getShippedStatuses(),
                        'header' => $helper->__('Shipped'),
                        'index' => 'shipped',
                        'align' => 'center'
                        ));
                break;                    
                    
                case 'order_group':
                    $this->addColumn('order_group', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',                
                        'type'  => 'options',
                        'options' => $helper->getOrderGroups(),
                        'header' => $helper->__('Group'),
                        'index' => 'order_group_id',
                        'align' => 'center',                        
                        ));
                break;


                case 'qnty':
                    $this->addColumn('qnty', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_qnty',                
                        'filter'    => false,
                        'sortable'  => false,                
                        'header' => $helper->__('Qnty'),
                        'index' => 'total_qty',                
                        ));
                break;    

                    
                case 'tax_amount':
                    $this->addColumn('tax_amount', array(                            
                        'type'  => 'currency',
                        'currency_code' => $currencyCode,                
                        'header' => $helper->__('Tax Amount'),
                        'index' => 'tax_amount'
                        ));
                break;    
                    
 
                    
                    
                case 'discount_amount':
                    $this->addColumn('discount_amount', array(                            
                        'type'  => 'currency',
                        'currency_code' => $currencyCode,                
                        'header' => $helper->__('Discount'),
                        'index' => 'discount_amount'                
                        ));
                break;
            
                case 'internal_credit':
                    if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                        $this->addColumn('internal_credit', array(
                            'type'  => 'currency',
                            'currency_code' => $currencyCode,
                            'header' => $helper->__('Internal Credit'),
                            'index' => 'customer_credit_amount'
                            ));
                    }
                break;
            
                case 'coupon_code':
                    $this->addColumn('coupon_code', array(
                        'type'  => 'text',
                        'header' => Mage::helper('orderspro')->__('Coupon Code'),
                        'align' => 'center',
                        'index' => 'coupon_code'       
                        ));
                break;
                
                
                case 'is_edited':
                    $this->addColumn('is_edited', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',                
                        'type'  => 'options',
                        'options' => $helper->getEditedStatuses(),
                        'header' => $helper->__('Edited'),
                        'index' => 'is_edited',
                        'align' => 'center'
                        ));
                break;
            
                case 'status':
                    $this->addColumn('status', array(
                        'header' => Mage::helper('sales')->__('Status'),
                        'index' => 'status',
                        'type'  => 'options',
                        'width' => '70px',
                        'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
                    ));
                break;
            }            
        }

        $this->sortColumnsByOrder();
        return $this;
    }

}
