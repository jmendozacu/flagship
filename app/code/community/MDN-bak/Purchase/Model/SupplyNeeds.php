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
class MDN_Purchase_Model_SupplyNeeds extends Mage_Core_Model_Abstract {

    /**
     * Constructeur
     *
     */
    public function _construct() {
        parent::_construct();
        $this->_init('Purchase/SupplyNeeds');
    }

    /**
     * Retourne le statut du besoin d'appro sous la forme num�rique pour faciliter le tri
     *
     */
    public function getStatusForSort() {
        //todo: implement
        return 0;
    }

    /**
     * Return all supply needs as array
     *
     */
    public function getSupplyNeeds() {
        $retour = array();

        //Recupere la collection de la base et la retourne sous la forme d'un tableau
        $collection = $this->getCollection();
        foreach ($collection as $item) {
            $retour[] = $item;
        }

        //Retourne le tout
        return $retour;
    }

    /**
     * Return products that MAY belong to supply needs
     * further checks are made
     *
     */
    public function getCandidateProductIds() {
        $inventoryGroupName = mage::helper('purchase/MagentoVersionCompatibility')->getStockOptionsGroupName();

        //collect default settings
        $DefaultManageStock = Mage::getStoreConfig('cataloginventory/' . $inventoryGroupName . '/manage_stock');
        if ($DefaultManageStock == '')
            $DefaultManageStock = 1;
        $DefaultNotifyStockQty = Mage::getStoreConfig('cataloginventory/' . $inventoryGroupName . '/notify_stock_qty');
        if ($DefaultNotifyStockQty == '')
            $DefaultNotifyStockQty = 0;
        $tablePrefix = mage::getModel('Purchase/Constant')->getTablePrefix();
        $supplyNeedsAttributeId = mage::getModel('Purchase/Constant')->GetProductManualSupplyNeedQtyAttributeId();

        //todo: use magento resource sql builder instead of direct sql
        $sql = "
			select 
				distinct(product_id)
			from 
				" . $tablePrefix . "cataloginventory_stock_item tbl_stock
				left outer join " . $tablePrefix . "catalog_product_entity_int tbl_supneed on (tbl_stock.product_id = tbl_supneed.entity_id and tbl_supneed.store_id = 0 and tbl_supneed.attribute_id = " . $supplyNeedsAttributeId . ")
			where
				(qty < (stock_ordered_qty + if(use_config_notify_stock_qty = 1, " . $DefaultNotifyStockQty . ", notify_stock_qty) + if(tbl_supneed.value is null,0,tbl_supneed.value)))
		";

        $data = mage::getResourceModel('sales/order_item_collection')->getConnection()->fetchAll($sql);

        return $data;
    }

    /**
     *
     *
     * @param unknown_type $productId
     */
    public function refreshSupplyNeedsForProduct($productId) {
        $debug = '##Refresh supply needs for product #' . $productId;

        //delete product from supply needs
        $this->deleteProductFromSupplyNeeds($productId);

        //check exceptions
        $product = mage::getModel('catalog/product')->load($productId);
        if (!$product->getId())
            return false;
        if ($product->getexclude_from_supply_needs())
            return false;
        if (!$this->allowAddToSupplyNeeds($product))
            return false;

        //if product disabled
        if (mage::getStoreConfig('purchase/supplyneeds/display_disabled_products') != 1) {
            if ($product->getStatus() != 1)  //intentionnaly dont use constand for old magento version compatibility
                return false;
        }

        //init datas
        $stocks = mage::getModel('cataloginventory/stock_item')
                        ->getCollection()
                        ->join('AdvancedStock/Warehouse', 'main_table.stock_id=`AdvancedStock/Warehouse`.stock_id')
                        ->addFieldToFilter('product_id', $productId);

        $totalQty = 0;
        $totalNeededQty = 0;
        $totalNeededQtyForValidOrders = 0;
        $totalAvailableQty = 0;
        $manualSupplyNeedQty = $product->getmanual_supply_need_qty();
        $generalStatus = MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusOk;
        $waitingForDeliveryQty = $product->getwaiting_for_delivery_qty();
        $pendingOrdersIds = mage::helper('AdvancedStock/Sales_PendingOrders')->getPendingOrderIdsForProduct($productId);

        //parse stock collection
        foreach ($stocks as $stock) {

            //check if product manage stock
            if (!$stock->ManageStock()) {
                $debug .= ' --> doesnt manage stock';
                //mage::log($debug);
                return false;
            }

            //if supply needs disabled for warehouse
            if ($stock->getstock_disable_supply_needs() == 1)
                continue;

            //compute totals
            $totalQty += $stock->getqty();
            $totalNeededQty += $stock->getNeededQty();
            $totalAvailableQty += $stock->getAvailableQty();
            $totalNeededQtyForValidOrders += $stock->getNeededQtyForValidOrders();

            //define general status
            switch ($stock->getStatus()) {
                case MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusSalesOrder:
                    $generalStatus = MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusSalesOrder;
                    break;
                case MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusQtyMini:
                    if ($generalStatus == MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusOk)
                        $generalStatus = MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusQtyMini;
                    break;
            }
        }
        $totalNeededQty += $manualSupplyNeedQty;

        //if status set to salesOrder and waiting for delivery qty is enough, set status to QtyMini
        if ($generalStatus == MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusSalesOrder) {
            if ($totalNeededQtyForValidOrders <= $waitingForDeliveryQty) {
                if ($totalNeededQtyForValidOrders < $totalNeededQty) {
                    $generalStatus = MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusQtyMini;
                }
            }
        }

        //set status to other if concerns only manual supply needs
        if (($manualSupplyNeedQty > 0) && ($generalStatus == MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusOk))
            $generalStatus = MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusOther;

        $debug .= sprintf('(totalqty=%s, totalneededqty=%s, totalneededqtyforvalidorders=%s, manualsupplyneeds=%s, generalstatus=%s, waitingfordelivery=%s)', $totalQty, $totalNeededQty, $totalNeededQtyForValidOrders, $manualSupplyNeedQty, $generalStatus, $waitingForDeliveryQty);

        //exit if status is OK
        if ($generalStatus == MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusOk)
            return true;

        //set general status to pending supply
        if ($totalNeededQty <= $waitingForDeliveryQty)
            $generalStatus = MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusPendingSupply;

        //create supply needs record
        $deadline = $this->defineDeadLineFromPendingOrders($pendingOrdersIds, $productId);
        $deadlineForPurchase = $this->defineDeadLineForPurchase($deadline, $product);
        $isCritical = 0;
        $isWarning = 0;
        $priority = $this->calculateSupplyNeedPriority($generalStatus, $deadline, $isCritical);
        $suppliersInformation = $this->getSuppliersInformation($product);

        $configurableProductHelper = mage::helper('AdvancedStock/Product_ConfigurableAttributes');

        mage::getModel('Purchase/SupplyNeeds')
                ->setsn_product_id($product->getId())
                ->setsn_product_sku($product->getSku())
                ->setsn_manufacturer_id($product->getManufacturer())
                ->setsn_manufacturer_name($product->getAttributeText('manufacturer'))
                ->setsn_product_name($product->getName() . $configurableProductHelper->getDescription($product->getId(), false))
                ->setsn_status($generalStatus)
                ->setsn_needed_qty(($totalNeededQty - $waitingForDeliveryQty >= 0 ? $totalNeededQty - $waitingForDeliveryQty : 0))
                ->setsn_needed_qty_for_valid_orders(($totalNeededQtyForValidOrders - $waitingForDeliveryQty >= 0 ? $totalNeededQtyForValidOrders - $waitingForDeliveryQty : 0))
                ->setsn_details('')
                ->setsn_deadline($deadline)
                ->setsn_is_critical($isCritical)
                ->setsn_purchase_deadline($deadlineForPurchase)
                ->setsn_suppliers_ids($suppliersInformation['suppliers_ids'])
                ->setsn_suppliers_name($suppliersInformation['suppliers_name'])
                ->setsn_is_warning($isWarning)
                ->setsn_priority($priority)
                ->save();
    }

    /**
     * Define if product can be added to supply needs (method to override to implement custom logic)
     *
     * @param unknown_type $product
     * @return unknown
     */
    protected function allowAddToSupplyNeeds($product) {
        return true;
    }

    /**
     * Return information for suppliers
     *
     * @param unknown_type $product
     */
    private function getSuppliersInformation($product) {
        $SuppliersName = "";
        $SuppliersIds = "";
        $suppliers = mage::getModel('Purchase/ProductSupplier')->getSuppliersForProduct($product);
        $BestSupplierUnderlined = false;
        foreach ($suppliers as $supplier) {
            if (!$BestSupplierUnderlined && $supplier->getpps_last_unit_price() > 0)
                $SuppliersName .= '<u><b>';
            $SuppliersName .= $supplier->getsup_name();
            if ($supplier->getpps_last_unit_price() > 0)
                $SuppliersName .= ' (' . $supplier->getpps_last_unit_price() . ')';
            if (!$BestSupplierUnderlined && $supplier->getpps_last_unit_price() > 0) {
                $BestSupplierUnderlined = true;
                $SuppliersName .= '</b></u>';
            }
            $SuppliersName .= ', ';
            $SuppliersIds .= ',' . $supplier->getsup_id() . ', ';
        }

        //return
        $retour = array();
        $retour['suppliers_name'] = $SuppliersName;
        $retour['suppliers_ids'] = $SuppliersIds;
        return $retour;
    }

    /**
     * Define dead line from pendingorders
     *
     * @param unknown_type $pendingOrders
     */
    private function defineDeadLineFromPendingOrders($pendingOrdersIds, $productId) {
        $timeStamp = null;
        $debug = '';

        $collection = mage::getModel('sales/order_item')
                        ->getCollection()
                        ->addFieldToFilter('order_id', array('in' => $pendingOrdersIds))
                        ->addFieldToFilter('product_id', $productId);
        foreach ($collection as $orderItem) {
            $remainToShip = $orderItem->getRemainToShipQty();
            if (($remainToShip > 0) && ($remainToShip > $orderItem->getreserved_qty())) {
                $planning = mage::helper('SalesOrderPlanning/Planning')->getPlanningForOrderId($orderItem->getorder_id());
                if ($planning) {
                    $orderToPrepareDate = $planning->getFullstockDate();
                    if ($orderToPrepareDate != '') {
                        $orderToPrepareTimeStamp = strtotime($orderToPrepareDate);
                        if (($orderToPrepareTimeStamp < $timeStamp) || ($timeStamp == null)) {
                            $timeStamp = $orderToPrepareTimeStamp;
                        }
                    }
                }
            }
        }

        if ($timeStamp != null)
            return date('Y-m-d', $timeStamp);
        else
            return null;
    }

    /**
     * Define dead line for purchase
     *
     * @param unknown_type $deadLine
     * @param unknown_type $product
     */
    public function defineDeadLineForPurchase($deadLine, $product) {
        $value = null;

        //todo : consider holydays
        if ($deadLine != null) {
            $productAvailabilityStatus = mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus')->load($product->getId(), 'pa_product_id');
            if ($productAvailabilityStatus->getId()) {
                $defaultSupplyDelay = $productAvailabilityStatus->getpa_supply_delay();
                if ($defaultSupplyDelay) {
                    $timeStamp = strtotime($deadLine);
                    $timeStamp -= $defaultSupplyDelay * 3600 * 24;
                    $value = date('Y-m-d', $timeStamp);
                }
            }
        }

        return $value;
    }

    /**
     * Delete product from supply needs
     *
     * @param unknown_type $productId
     */
    public function deleteProductFromSupplyNeeds($productId) {
        $sn = mage::getModel('Purchase/SupplyNeeds')->load($productId, 'sn_product_id');
        if ($sn->getId())
            $sn->delete();
    }

    /**
     * Define if a supply need is critical
     *
     * @param unknown_type $product
     * @param unknown_type $supplyNeed
     * @param unknown_type $pendingOrders
     */
    public function IsCritical($product, $supplyNeed, $pendingOrders) {
        //supply need is critical if only this product is missing in an order
        if (($pendingOrders != null) && (Mage::getStoreConfig('purchase/supplyneeds/is_critical_if_order_only_missing_product') == 1)) {
            //parse orders
            foreach ($pendingOrders as $order) {
                //parse order's products
                $otherProductsAreMissing = false;
                foreach ($order->getAllItems() as $OrderItem) {
                    if ($OrderItem->getproduct_id() != $product->getId()) {
                        $remainingQty = ($OrderItem->getqty_ordered() - $OrderItem->getRealShippedQty() - $OrderItem->getreserved_qty());
                        if ($remainingQty < 0)
                            $otherProductsAreMissing = true;
                    }
                }
                if (!$otherProductsAreMissing)
                    return true;
            }
        }

        return false;
    }

    /**
     * Define supply need priority
     *
     */
    public function calculateSupplyNeedPriority($status, $deadLine, $isCritical) {
        $retour = 0;

        switch ($status) {
            case MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusSalesOrder :
                if (($deadLine != null) && ($deadLine != '2099/12/31'))
                    $retour = strtotime($deadLine);
                else {
                    $retour = 6000000000;
                }
                break;
            case MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusOther :
                if (($deadLine != null) && ($deadLine != '2099/12/31'))
                    $retour = strtotime($deadLine);
                else {
                    $retour = 7000000000;
                }
                break;
            case MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusQtyMini :
                $retour = 8000000000;
                break;
            case MDN_AdvancedStock_Model_CatalogInventory_Stock_Item::_StatusPendingSupply :
                $retour = 9000000000;
                break;
            default:
                $retour = 10000000000;
                break;
        }

        //if is critical, modify priority
        if ($isCritical)
            $retour -= 500;

        return $retour;
    }

}