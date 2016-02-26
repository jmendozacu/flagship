<?php
class Ecommerceguys_Inventorymanager_Model_Observer
{
	public function addMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
       // echo get_class($block) . "<br/>";
        if((get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction')
            && ($block->getRequest()->getControllerName() == 'orderspro_order' || $block->getRequest()->getControllerName() == 'sales_order'))
        {
            $block->addItem('print_order', array(
                'label' => 'Print Order',
                'url' => Mage::app()->getStore()->getUrl('inventorymanager/adminhtml_order/print'),
            ));
        }
    }
    
    public function saveShipmentAfter($observer){
		$shipment = $observer->getEvent()->getShipment();
		$order = $shipment->getOrder();
		
		$params = Mage::app()->getRequest()->getParams();
		$serials = $params['serials'];
		foreach ($serials as $serial){
			$serialModel = Mage::getModel('inventorymanager/label')->load($serial, "serial");
			if($serialModel && $serialModel->getId()){
				$serialModel->addData(
					array(
						'shipment_id'	=>	$shipment->getId(),
						'real_order_id'	=>	$order->getId()
					)
				)->save();
			}
		}
		
    }
}