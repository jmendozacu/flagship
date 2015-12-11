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
//Controlleur pour la gestion des suppliers
class MDN_Purchase_SuppliersController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {

    }

    /**
     * 
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
    public function NewAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * 
     *
     */
    public function CreateAction() {
        $Supplier = mage::getModel('Purchase/Supplier');
        $Supplier->setsup_name($this->getRequest()->getParam('sup_name'));
        $Supplier->save();

        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supplier Created'));
        $this->_redirect('Purchase/Suppliers/Edit/sup_id/' . $Supplier->getId());
    }

    /**
     *
     *
     */
    public function EditAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save supplier information
     *
     */
    public function SaveAction() {
        //load supplier & infos
        $Supplier = Mage::getModel('Purchase/Supplier')->load($this->getRequest()->getParam('sup_id'));
        $currentTab = $this->getRequest()->getParam('current_tab');
        $data = $this->getRequest()->getPost();

        //customize datas
        if (!isset($data['sup_sale_online']))
            $data['sup_sale_online'] = 0;
        if (isset($data['sup_discount_level']))
            $data['sup_discount_level'] = str_replace(',', '.', $data['sup_discount_level']);

        //save datas
        foreach ($data as $key => $value) {
            $Supplier->setData($key, $value);
        }
        $Supplier->save();

        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supplier Saved'));
        $this->_redirect('Purchase/Suppliers/Edit', array('sup_id' => $Supplier->getId(), 'tab' => $currentTab));
    }

    /**
     * Return supplier's orders grid
     */
    public function AssociatedOrdersGridAction() {
        $this->loadLayout();
        $supId = $this->getRequest()->getParam('sup_id');
        $Block = $this->getLayout()->createBlock('Purchase/Supplier_Edit_Tabs_Orders');
        $Block->setSupplierId($supId);
        $this->getResponse()->setBody($Block->toHtml());
    }

    /**
     * Return supplier's products grid
     */
    public function ProductsGridAction() {
        $this->loadLayout();
        $supId = $this->getRequest()->getParam('sup_id');
        $Block = $this->getLayout()->createBlock('Purchase/Supplier_Edit_Tabs_Products');
        $Block->setSupplierId($supId);
        $this->getResponse()->setBody($Block->toHtml());
    }

}