<?php

class MDN_AdvancedStock_Model_Sales_Order_Item extends Mage_Sales_Model_Order_Item {

    private $_preparationWarehouse = null;

    /**
     * Retourne la marge pour cette ligne commande
     *
     */
    //todo: deporter
    public function GetMargin() {
        //Calcul la marge
        $retour = 0;
        $retour = ($this->getPrice() * $this->getqty_ordered()) - ($this->getData(mage::helper('purchase/MagentoVersionCompatibility')->getSalesOrderItemCostColumnName()) * $this->getqty_ordered());

        return $retour;
    }

    /**
     * Retourne la marge en %
     *
     */
    //todo: deporter
    public function GetMarginPercent() {
        if ($this->getPrice() > 0)
            return ($this->getPrice() - $this->getData(mage::helper('purchase/MagentoVersionCompatibility')->getSalesOrderItemCostColumnName())) / $this->getPrice() * 100;
        else
            return 0;
    }

    /**
     * Reset reserved qty if preparation warehouse change
     */
    protected function _beforeSave() {
        parent::_beforeSave();

        if ($this->getpreparation_warehouse() != $this->getOrigData('preparation_warehouse')) {
            $this->setreserved_qty(0);
        }
    }

    /**
     * when saving, update supply needs for product (if concerned)
     *
     */
    protected function _afterSave() {
        parent::_afterSave();

        $debug = '#After save on sales order item #' . $this->getId() . " : ";

        //if preparation warehouse change, plan operations
        if ($this->getpreparation_warehouse() != $this->getOrigData('preparation_warehouse')) {
            $productId = $this->getproduct_id();
            $oldWarehouseId = $this->getOrigData('preparation_warehouse');
            $newWarehouseId = $this->getpreparation_warehouse();
            $debug .= 'warehouse change from ' . $oldWarehouseId . ' to ' . $newWarehouseId . ', ';

            //updata data (ordered & reserved qty) for old warehouse
            if ($oldWarehouseId) {
                $stockItem = mage::getModel('cataloginventory/stock_item')->loadByProductWarehouse($productId, $oldWarehouseId);
                if ($stockItem) {
                    mage::helper('AdvancedStock/Product_Ordered')->storeOrderedQtyForStock($stockItem, $productId);
                    mage::helper('AdvancedStock/Product_Reservation')->storeReservedQtyForStock($stockItem, $productId);
                }
            }

            //update data for new warehouse
            $stockItem = mage::getModel('cataloginventory/stock_item')->loadByProductWarehouse($productId, $newWarehouseId);
            if (!$stockItem)
                $stockItem = mage::getModel('cataloginventory/stock_item')->createStock($productId, $newWarehouseId);
            mage::helper('AdvancedStock/Product_Ordered')->storeOrderedQtyForStock($stockItem, $productId);
            $order = mage::getModel('sales/order')->load($stockItem->getorder_id()); //todo : find a solution to avoid loading order...
            $this->setOrigData('preparation_warehouse', $this->getpreparation_warehouse());  //important, else, loop calls...
            mage::helper('AdvancedStock/Product_Reservation')->reserveOrderProduct($order, $this);

            //if product has parent, affect the same warehouse to the parent
            if ($this->getparent_item_id()) {
                $parentItem = Mage::getModel('sales/order_item')->load($this->getparent_item_id());
                $parentItem->setpreparation_warehouse($newWarehouseId)->save();
            }
        }


        $debug .= ', reserved qty pass from ' . $this->getOrigData('reserved_qty') . ' to ' . $this->getreserved_qty();
        //mage::log($debug);
        //dispatch event
        Mage::dispatchEvent('salesorderitem_aftersave', array('salesorderitem' => $this));

        return $this;
    }

    /**
     * return real qty shipped (multiply with parent item)
     *
     */
    public function getRealShippedQty() {
        $qty = 0;

        //if no parent
        if ($this->getparent_item_id() == null) {
            $qty = $this->getqty_shipped();
        } else {
            //if has parent
            $parentItem = mage::getModel('sales/order_item')->load($this->getparent_item_id());
            if ($parentItem->isShipSeparately()) {
                $qty = $this->getqty_shipped();
            } else {
                $qty = $parentItem->getqty_shipped() * ($this->getqty_ordered() / $parentItem->getqty_ordered());
            }
        }

        return $qty;
    }

    /**
     * Return qty remaining to ship
     *
     */
    public function getRemainToShipQty() {
        $retour = 0;

        //if no parent
        if ($this->getparent_item_id() == null) {
            switch ($this->getproduct_type()) {
                case null:
                case 'simple':
                case 'grouped':
                case 'giftcard':
                case 'configurable':
                    $retour = $this->getqty_ordered() - $this->getqty_shipped() - $this->getqty_refunded() - $this->getqty_canceled();
                    break;
                case 'bundle':
                    if ($this->isShipSeparately())
                        $retour = 0;
                    else
                        $retour = $this->getqty_ordered() - $this->getqty_shipped() - $this->getqty_refunded() - $this->getqty_canceled();
                    break;
            }
        }
        else {
            //if has parent
            $parentItem = mage::getModel('sales/order_item')->load($this->getparent_item_id());
            if ($parentItem->isShipSeparately()) {
                $retour = $this->getqty_ordered() - $this->getqty_shipped() - $this->getqty_refunded() - $this->getqty_canceled();
            } else {
                $retour = $parentItem->getqty_ordered() - $parentItem->getqty_shipped() - $parentItem->getqty_refunded() - $parentItem->getqty_canceled();
                $retour *= ( $this->getqty_ordered() / $parentItem->getqty_ordered());
            }
        }

        if ($retour < 0)
            $retour = 0;

        return $retour;
    }

    /**
     * Return shelf location according to preparation warehouse
     */
    public function getShelfLocation() {
        $warehouse = $this->getPreparationWarehouse();
        if ($warehouse) {
            $stockItem = $warehouse->getProductStockItem($this->getproduct_id());
            return $stockItem->getshelf_location();
        }
        else
            return '';
    }

    /**
     * Return preparation warehouse
     */
    public function getPreparationWarehouse() {
        if ($this->_preparationWarehouse == null) {
            $this->_preparationWarehouse = mage::getModel('AdvancedStock/Warehouse')->load($this->getpreparation_warehouse());
        }
        return $this->_preparationWarehouse;
    }

}