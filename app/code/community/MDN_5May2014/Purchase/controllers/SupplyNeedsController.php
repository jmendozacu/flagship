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
class MDN_Purchase_SupplyNeedsController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {

    }

    /**
     * Affiche la liste
     *
     */
    public function ListAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * 
     *
     */
    public function StatsAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display grid
     *
     */
    public function GridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * rafraichit le cache pour les supply needs
     *
     */
    public function RefreshListAction() {
        //create backgroundtask group
        $taskGroup = 'refresh_suppy_needs';
        mage::helper('BackgroundTask')->AddGroup($taskGroup, mage::helper('purchase')->__('Refresh Supply Needs'), 'Purchase/SupplyNeeds/Grid');

        //empty table
        Mage::getResourceModel('Purchase/SupplyNeeds')->TruncateTable();

        //collect product ids
        $ids = mage::getModel('Purchase/SupplyNeeds')->getCandidateProductIds();

        for ($i = 0; $i < count($ids); $i++) {
            //add tasks to group
            $productId = $ids[$i]['product_id'];
            mage::helper('BackgroundTask')->AddTask('Update supply needs for product #' . $productId,
                    'purchase',
                    'updateSupplyNeedsForProduct',
                    $productId,
                    $taskGroup
            );
        }

        //execute task group
        mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
    }

    /**
     * Create a purchase order and add products
     *
     */
    public function CreatePurchaseOrderAction() {
        //init vars
        $supplyNeedsIds = $this->getRequest()->getPost('supply_needs_product_ids');
        $sup_num = $this->getRequest()->getPost('supplier');

        //cree la commande
        $order = mage::getModel('Purchase/Order')
                        ->setpo_sup_num($sup_num)
                        ->setpo_date(date('Y-m-d'))
                        ->setpo_currency(Mage::getStoreConfig('purchase/purchase_order/default_currency'))
                        ->setpo_tax_rate(Mage::getStoreConfig('purchase/purchase_order/default_shipping_duties_taxrate'))
                        ->setpo_order_id(mage::getModel('Purchase/Order')->GenerateOrderNumber())
                        ->save();

        //rajoute les produits
        foreach ($supplyNeedsIds as $supplyNeedId) {
            //retrieve information
            $supplyNeed = mage::getModel('Purchase/SupplyNeeds')->load($supplyNeedId);
            if ($supplyNeed) {
                $qty = $supplyNeed->getsn_needed_qty();
                $productId = $supplyNeed->getsn_product_id();

                //add product
                try {
                    $order->AddProduct($productId, $qty);
                } catch (Exception $ex) {
                    Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
                }
            }
        }

        //confirme
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order successfully Created'));
        $this->_redirect('Purchase/Orders/Edit', array('po_num' => $order->getId()));
    }

    /**
     * refresh supply need for 1 product
     *
     */
    public function RefreshProductAction() {
        $productId = $this->getRequest()->getParam('product_id');

        try {
            mage::getModel('Purchase/SupplyNeeds')->refreshSupplyNeedsForProduct($productId);
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured ') . ' : ' . $ex->getMessage());
        }

        //redirect
        $this->_redirect('AdvancedStock/Products/Edit', array('product_id' => $productId));
    }

    /**
     * Create purchase order from stats
     */
    public function CreatePoFromStatsAction() {
        //get datas
        $supplierId = $this->getRequest()->getParam('sup_id');
        $statuses = explode(',', $this->getRequest()->getParam('status'));

        //create PO
        $po = mage::helper('purchase/Order')->createNewOrder($supplierId);

        //get supply needs
        foreach ($statuses as $status) {
            $supplyNeeds = mage::getModel('Purchase/SupplyNeeds')
                            ->getCollection()
                            ->addFieldToFilter('sn_status', $status)
                            ->addFieldToFilter('sn_suppliers_ids', array('like' => '%' . $supplierId . ',%'));

            foreach ($supplyNeeds as $supplyNeed) {
                $qty = $supplyNeed->getsn_needed_qty();
                $productId = $supplyNeed->getsn_product_id();
                try {
                    $po->AddProduct($productId, $qty);
                } catch (Exception $ex) {
                    Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
                }
            }
        }


        //confirm and redirect to PO
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order successfully Created'));
        $this->_redirect('Purchase/Orders/Edit', array('po_num' => $po->getId()));
    }

    /**
     * Update Prefered stock level
     */
    public function updatePreferedStockLevelAction() {
        //get product ids
        $productIds = mage::helper('AdvancedStock/Product_Base')->getProductIds();

        //create backgroundtask group
        $taskGroup = 'update_prefered_stock_level';
        mage::helper('BackgroundTask')->AddGroup($taskGroup, mage::helper('purchase')->__('Update prefered stock level'), 'Purchase/SupplyNeeds/Grid');

        //plan tasks
        foreach ($productIds as $productId) {
            //add tasks to group
            mage::helper('BackgroundTask')->AddTask('Update warning stock level for product #' . $productId,
                    'purchase/SupplyNeeds',
                    'updatePreferedStockLevel',
                    $productId,
                    $taskGroup
            );
        }

        //execute task group
        mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
    }

    /**
     * Import prefered stock levels
     */
    public function ImportPreferedStockLevelsAction() {
        try {
            //load file
            $uploader = new Varien_File_Uploader('file');
            $uploader->setAllowedExtensions(array('txt', 'csv'));
            $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
            $uploader->save($path);

            if ($uploadFile = $uploader->getUploadedFileName()) {
                //load file
                $filePath = $path . $uploadFile;
                $content = file($filePath);

                //process file
                $helper = mage::helper('purchase/PreferedStockLevel');
                $result = $helper->import($content);

                //confirm & redirect
                Mage::getSingleton('adminhtml/session')->addSuccess($result);
                $this->_redirect('Purchase/SupplyNeeds/Grid');
            }
            else
                throw new Exception('Unable to load file');
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
            $this->_redirect('Purchase/SupplyNeeds/Grid');
        }
    }

}