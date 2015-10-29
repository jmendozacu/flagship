<?php

class MDN_AdvancedStock_TransferController extends Mage_Adminhtml_Controller_Action {

    /**
     * Transfer grid
     */
    public function GridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edit transfer
     */
    public function EditAction() {

        $transferId = $this->getRequest()->getParam('st_id');
        $transfer = mage::getModel('AdvancedStock/StockTransfer')->load($transferId);
        mage::register('current_transfer', $transfer);

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save stock transfer
     */
    public function SaveAction()
    {
        //load object
        $transfer = mage::getModel('AdvancedStock/StockTransfer');
        $data = $this->getRequest()->getPost();
        if ($data['st_id'])
                $transfer->load($data['st_id']);

        //update datas
        foreach($data as $key => $value)
            $transfer->setData($key, $value);

        //add products
        $productsToAdd = $this->getProductsToAdd($data['add_product_log']);
        foreach($productsToAdd as $productId => $qty)
        {
            if ($qty > 0)
                $transfer->addProduct($productId, $qty);
        }

        //update products data
        $productsChanges = $this->getProductsChanges($data['product_log']);
        foreach($productsChanges as $id => $fields)
        {
            $tranferProduct = mage::getModel('AdvancedStock/StockTransfer_Product')->load($id);
            foreach($fields as $field => $value)
                $tranferProduct->setData($field, $value);

            if ($tranferProduct->getdelete())
                $tranferProduct->delete();
            else
                $tranferProduct->save();
        }

        //save
        $transfer->save();

        //update status
        $transfer->updateStatus();

        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Data saved'));
        $this->_redirect('AdvancedStock/Transfer/Edit', array('st_id' => $transfer->getId()));
    }

    /**
     * Converts products to add string to array
     * @param <type> $data
     */
    protected function getProductsToAdd($data)
    {
        $productsToAdd = array();
        $lines = explode(';', $data);
        foreach($lines as $line)
        {
            $t = explode('=', $line);
            if (count($t) != 2)
                continue;
            $qty = $t[1];
            $id = str_replace('add_qty_', '', $t[0]);
            $productsToAdd[$id] = $qty;
        }
 
        return $productsToAdd;
    }


    /**
     * Converts products to change string to array
     * @param <type> $data
     */
    protected function getProductsChanges($data)
    {
        $productsToChange = array();

        $lines = explode(';', $data);
        foreach($lines as $line)
        {
            $t = explode('=', $line);
            if (count($t) != 2)
                continue;

            $value = $t[1];
            $fieldName = $t[0];
            $lastUnderscore = strrpos($fieldName, '_');
            
            $id = substr($fieldName, $lastUnderscore + 1);
            $fieldName = substr($fieldName, 0, $lastUnderscore);

            if (!isset($productsToChange[$id]))
                $productsToChange[$id] = array();
            $productsToChange[$id][$fieldName] = $value;

        }

        return $productsToChange;
    }

    /**
     * Ajax update for add products grid
     */
    public function AddProductsGridAction()
    {
        $transferId = $this->getRequest()->getParam('st_id');
        $transfer = mage::getModel('AdvancedStock/StockTransfer')->load($transferId);
        mage::register('current_transfer', $transfer);

        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('AdvancedStock/Transfer_Edit_Tabs_AddProducts')->toHtml()
        );
    }
    
    /**
     * Ajax update for products grid
     */
    public function ProductsGridAction()
    {
        $transferId = $this->getRequest()->getParam('st_id');
        $transfer = mage::getModel('AdvancedStock/StockTransfer')->load($transferId);
        mage::register('current_transfer', $transfer);

        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('AdvancedStock/Transfer_Edit_Tabs_Products')->toHtml()
        );
    }

    /**
     * Apply transfer
     * @return <type>
     */
    public function ApplyAction()
    {
        try
        {
            $transferId = $this->getRequest()->getParam('st_id');
            $forceTransfer = $this->getRequest()->getParam('force_transfer');
            $transfer = mage::getModel('AdvancedStock/StockTransfer')->load($transferId);

            if ((!$transfer->canBeApplied()) && (!$forceTransfer))
            {
                $this->_redirect('AdvancedStock/Transfer/NotFullyApplicable', array('st_id' => $transfer->getId()));
                return;
            }

            $transfer->apply();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Transfer applied'));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }

        //confirm & redirect
        $this->_redirect('AdvancedStock/Transfer/Edit', array('st_id' => $transfer->getId()));

    }

    /**
     * Display products that cant be transfered
     */
    public function NotFullyApplicableAction()
    {
        $transferId = $this->getRequest()->getParam('st_id');
        $transfer = mage::getModel('AdvancedStock/StockTransfer')->load($transferId);
        mage::register('current_transfer', $transfer);

        $this->loadLayout();
        $this->renderLayout();
    }

}