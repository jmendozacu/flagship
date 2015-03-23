<?php

class Rvtech_Purchaseorder_Adminhtml_PurchaseorderController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('purchaseorder/set_order')
				->_addBreadcrumb('Purchase Order Manager', 'Purchase Order Manager');
		return $this;
	}

	public function indexAction() {
		$this->_initAction();
		$this->renderLayout();
		//$handle = Mage::getSingleton('core/layout')->getUpdate()->getHandles();

	}
	public function gridAction()
     {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('purchaseorder/adminhtml_purchaseorder_grid')->toHtml()
        );
     }
}

?>