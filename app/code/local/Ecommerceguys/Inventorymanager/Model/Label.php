<?php
class Ecommerceguys_Inventorymanager_Model_Label extends Mage_Core_Model_Abstract
{
	 public function _construct()
    {
        parent::_construct();
        $this->_init('inventorymanager/label');
    }
    
    public function setNewStatus($status){
    	$statuses = Mage::helper('inventorymanager')->getOrderedProductStatusArray();
    	if(!in_array($status, $statuses)){
    		Mage::getResourceModel('inventorymanager/label')->addStatus($status);
    	}
    	//return parent::setStatus($status);
    }
    
    public function removeStatus($status){
    	$statuses = Mage::helper('inventorymanager')->getOrderedProductStatusArray();
    	if(in_array($status, $statuses)){
    		Mage::getResourceModel('inventorymanager/label')->removeStatus($status);
    	}
    }
    
    public function getLocations(){
    	return Mage::getResourceModel('inventorymanager/label')->getLocations();
    }
    
    public function setLocation($location){
    	Mage::getResourceModel('inventorymanager/label')->addLocation($location);
    	return parent::setLocation($location);
    }
    
    public function generateLabels($orderId){
    	$labelCollection = Mage::getModel('inventorymanager/label')->getCollection();
		$labelCollection->addFieldToFilter('order_id', $orderId);
		if(!$labelCollection->count() || $labelCollection->count() <= 0){
			$products = Mage::getModel('inventorymanager/product')->getCollection();
			$products->addFieldToFilter('po_id', $orderId);
			foreach ($products as $product){
				for($qtyCounter = 1; $qtyCounter <= $product->getQty(); $qtyCounter++){
					$serial = Mage::helper('inventorymanager')->getSerial($orderId);
					$label = Mage::getModel('inventorymanager/label');
					$labelData = array(
						'product_id'	=>	$product->getId(),
						'order_id'		=>	$orderId,
						'location'		=>	"P.O.",
						'status'		=>	Mage::helper('inventorymanager')->__("Processing"),
						'serial'		=>	$serial,
						'created_time'	=>	now(),
						'updated_time'	=>	now()
					);
					$label->setData($labelData)->save();
				}
			}
		}
    }
    
    public function updateLabels($orderId){
    	$products = Mage::getModel('inventorymanager/product')->getCollection();
		$products->addFieldToFilter('po_id', $orderId);
		
		foreach ($products as $product){
			$productQty = $product->getQty();
			$productLabels = Mage::getModel('inventorymanager/label')->getCollection();
			$productLabels->addFieldToFilter('product_id', $product->getId());
			$productLabels->addFieldToFilter('order_id', $orderId);
			if($productLabels->count() == $productQty){
				continue;
			}elseif($productLabels->count() < $productQty){
				$pendingLabels = $productQty - $productLabels->count();
				while ($pendingLabels > 0){
					$pendingLabels--;
					$serial = Mage::helper('inventorymanager')->getSerial($orderId);
					$label = Mage::getModel('inventorymanager/label');
					$labelData = array(
						'product_id'	=>	$product->getId(),
						'order_id'		=>	$orderId,
						'location'		=>	"P.O.",
						'status'		=>	Mage::helper('inventorymanager')->__("Processing"),
						'serial'		=>	$serial,
						'created_time'	=>	now(),
						'updated_time'	=>	now()
					);
					$label->setData($labelData)->save();
				}
			}
		}
    }
    
}