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
            $this->getLayout()->createBlock('inventorymanager/adminhtml_sales_order_view_tabs_serial')->toHtml()
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
    
    public function assignShipmentAction(){
    	$serial = $this->getRequest()->getParam('serial');
    	$shipmentId  = $this->getRequest()->getParam('shipment_id');
    	if($serial != "" && $shipmentId != ""){
    		$orderId  = $this->getRequest()->getParam('order_id');
    		$serialModel = Mage::getModel('inventorymanager/label')->load($serial, "serial");
    		if($serialModel && $serialModel->getId()){
    			
    			try{
	    			$serialModel->addData(
						array(
							'shipment_id'	=>	$shipmentId,
							'real_order_id'	=>	$orderId
						)
					)->save();
					echo $serialModel->getId();
    			}catch (Exception $e){
    				
    			}
    		}
    	}
    }
    
    public function unassignShipmentAction(){
    	if($id = $this->getRequest()->getParam('serial_id')){
    		$serialModel = Mage::getModel('inventorymanager/label')->load($id);
    		if($serialModel && $serialModel->getId()){
    			try{
	    			$serialModel->addData(
						array(
							'shipment_id'	=>	0,
							'real_order_id'	=>	0
						)
					)->save();
    			}catch (Exception $e){
    				
    			}
    		}
    	}
    }
}