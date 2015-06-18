<?php
class Ecommerceguys_Inventorymanager_LabelController extends Mage_Core_Controller_Front_Action 
{
	public function generateAction(){
		$orderId = $this->getRequest()->getParam('id');
		
		$labelCollection = Mage::getModel('inventorymanager/label')->getCollection();
		$labelCollection->addFieldToFilter('order_id', $orderId);
		if(!$labelCollection->count() || $labelCollection->count() <= 0){
			$products = Mage::getModel('inventorymanager/product')->getCollection();
			$products->addFieldToFilter('po_id', $orderId);
			
			foreach ($products as $product){
				$serial = Mage::helper('inventorymanager')->getSerial();
				$label = Mage::getModel('inventorymanager/label');
				$labelData = array(
					'product_id'	=>	$product->getId(),
					'order_id'		=>	$orderId,
					'location'		=>	1,
					'serial'		=>	$serial,
					'created_time'	=>	now(),
					'updated_time'	=>	now()
				);
				$label->setData($labelData)->save();
			}
		}
		
		$content = $this->getLayout()->createBlock('inventorymanager/label_generate')
		->setTemplate('inventorymanager/labelgenerate.phtml')->toHtml();
		
		echo $content;
	}
}