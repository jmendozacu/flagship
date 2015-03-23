<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AdvancedStock_Model_Observer {

    private $_maxOrder;

    /**
     * Function to process orders and upadte ordered qty, reserved qty ....
     *
     */
    public function UpdateStocksForOrders() {
        $debug = '<h1>Update stocks for orders</h1>';

        //collect orders with stocks_updated = 0 and status not finished (complete or canceled)
        $collection = mage::getModel('sales/order')
                        ->getCollection()
                        ->addFieldToFilter('stocks_updated', '0')
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('state', array('nin' => array('complete', 'canceled')));

        $this->_maxOrder = (int) mage::getStoreConfig('advancedstock/cron/order_update_stocks_max');
        $count = 0;
        foreach ($collection as $order) {
            $debug .= '<p><b>Processing order #' . $order->getId() . ' at (' . date('Y-m-d H:i:s') . ')</b>';

            try {
                //parse each product
                foreach ($order->getAllItems() as $item) {
                    $productId = $item->getproduct_id();

                    //get preparation warehouse
                    $preparationWarehouseId = mage::helper('AdvancedStock/Router')->getWarehouseForOrderItem($item, $order);
                    if (!$preparationWarehouseId)
                        $preparationWarehouseId = 1;

                    //Affect order item to warehouse using background task
                    //note : this update ordered qty and also try to reserve product for order
                    mage::helper('BackgroundTask')->AddTask('Affect order item #' . $item->getId() . ' to warehouse #' . $preparationWarehouseId,
                            'AdvancedStock/Router',
                            'affectWarehouseToOrderItem',
                            array('order_item_id' => $item->getId(), 'warehouse_id' => $preparationWarehouseId),
                            null
                    );
                }

                //update stocks_updated
                if ($order->getPayment()) {
                    $debug .= '<br>Set stocks updated = 1 for order #' . $order->getId();
                    $order->setstocks_updated(1)->save();
                }
                else
                    $debug .= '<br>--->Unable to retrieve payment for order #' . $order->getId();

                //execut X orders at once
                if ($count > $this->_maxOrder) {
                    $debug .= '<br>Exit after ' . $this->_maxOrder . ' loops';
                    break;
                }
                $count++;
            } catch (Exception $ex) {
                mage::logException($ex);
                $debug .= '<p>Error updating stocks for order #' . $order->getId() . ' : ' . $ex->getMessage() . '</p>';
            }
        }

        //print debug informaiton
        //echo $debug;
    }

    /**
     * Set payment validated to true when invoice is created
     *
     */
    public function sales_order_invoice_pay(Varien_Event_Observer $observer) {
        if (Mage::getStoreConfig('advancedstock/general/auto_validate_payment') == 1) {
            try {
                //recupere les infos
                $order = $observer->getEvent()->getInvoice()->getOrder();
                $order->setpayment_validated(1)->save();

                mage::log('payment_validated set to true for order #' . $order->getId());
            } catch (Exception $ex) {
                mage::log('Error when validating payment_validated: ' . $ex->getMessage());
            }
        }
    }

    /**
     * Called when an order is placed
     *
     * @param Varien_Event_Observer $observer
     * @return none
     */
    public function sales_order_afterPlace(Varien_Event_Observer $observer) {

        try {
            $order = $observer->getEvent()->getOrder();

            //init payment validated
            if ($order->getpayment_validated() != 1)
                $order->setpayment_validated(0);

            //parse products to affect cost & preparation warehouse
            foreach ($order->getAllItems() as $item) {
                $product = mage::getModel('catalog/product')->load($item->getproduct_id());
                if ($product) {

                    //store cost
                    switch ($product->gettype_id()) {
                        case 'simple':
                            $item->setData(mage::helper('purchase/MagentoVersionCompatibility')->getSalesOrderItemCostColumnName(), $product->getcost());
                            break;
                        case 'configurable':
                        case 'bundle':
                            $item->setData(mage::helper('purchase/MagentoVersionCompatibility')->getSalesOrderItemCostColumnName(), $this->computeCostFromSubProducts($item, $order->getAllItems()));
                            break;
                    }
                }
            }
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    /**
     * Change stock column in rma product reservation to display information for every warehouse
     *
     * @param Varien_Event_Observer $observer
     */
    public function productreturn_reservationgrid_preparecolumns(Varien_Event_Observer $observer) {
        $grid = $observer->getEvent()->getgrid();

        $grid->addColumn('qty', array(
            'header' => Mage::helper('ProductReturn')->__('Stock'),
            'index' => 'qty',
            'renderer' => 'MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_StockSummary',
            'filter' => false,
            'sortable' => false
        ));
    }

    /**
     * Compute cost from the sum of the costs of subproducts
     *
     * @param unknown_type $parentItem
     * @param unknown_type $items
     */
    private function computeCostFromSubProducts($parentItem, $items) {
        $retour = 0;
        $parentQuoteItemId = $parentItem->getquote_item_id();
        $parentItemQty = $parentItem->getqty_ordered();

        foreach ($items as $item) {
            if ($item->getquote_parent_item_id() == $parentQuoteItemId) {
                $product = mage::getModel('catalog/product')->load($item->getproduct_id());
                $retour += $product->getCost() * ($item->getqty_ordered() / $parentItemQty);
            }
        }

        return $retour;
    }

    /**
     * Update sales history for every products
     * Added here just to get an entry in models for cron
     * Called every sunday night
     */
    public function updateAllSalesHistory() {
        //if auto update enabled
        if (mage::getStoreConfig('advancedstock/sales_history/enable_auto_update') == 1)
            mage::helper('AdvancedStock/Sales_History')->scheduleUpdateForAllProducts();
    }

    /**
     * Called when sales history is updated
     */
    public function advancedstock_sales_history_change(Varien_Event_Observer $observer) {
        $salesHistory = $observer->getEvent()->getsales_history();

        //if auto calculate prefered stock level is enabled, refresh it
        if (mage::getStoreConfig('advancedstock/prefered_stock_level/enable_auto_calculation') == 1) {
            $productId = $salesHistory->getsh_product_id();
            mage::helper('AdvancedStock/Product_PreferedStockLevel')->updateForProduct($productId);
        }
    }


}

