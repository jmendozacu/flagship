<?php
class Ecommerceguys_Inventorymanager_Adminhtml_LabelController extends Mage_Adminhtml_Controller_action
{
	public function serialgridAction(){
		 $productGridBlock = $this->getLayout()->createBlock('inventorymanager/adminhtml_product_labels', 'label_product');
		 //$this->getResponse()->setBody($productGridBlock->toHtml());
		 echo $productGridBlock->toHtml();
	}
	
	public function gridAction(){
		$productGridBlock = $this->getLayout()->createBlock('inventorymanager/adminhtml_product_labels', 'label_product');
		 $this->getResponse()->setBody($productGridBlock->toHtml());
		 //echo $productGridBlock->toHtml();
	}
	
	public function ordergridAction(){
		/*$productGridBlock = $this->getLayout()->createBlock('inventorymanager/adminhtml_sales_order_view_tabs', 'order_serial');
		 $this->getResponse()->setBody($productGridBlock->toHtml());*/
		 
		 $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('inventorymanager/adminhtml_sales_order_view_tabs')->toHtml()
        );
	}
	
	protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
}