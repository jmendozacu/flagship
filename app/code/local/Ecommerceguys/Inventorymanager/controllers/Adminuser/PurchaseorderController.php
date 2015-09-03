<?php

class Ecommerceguys_Inventorymanager_Adminuser_PurchaseorderController extends Mage_Core_Controller_Front_Action
{

	protected function _getSession()
    {
        return Mage::getSingleton('inventorymanager/session');
    }
	
	public function preDispatch(){
		parent::preDispatch();
		if (!$this->_getSession()->isAdminUser()) {
            $this->_redirect('*/vendor/login');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
	}
	
	public function indexAction(){
		
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function newAction() {
		$this->_forward('orderedit');
	}
	
	public function editAction() {
		$this->_forward('orderedit');
	}
	
	public function ordereditAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			//print_r($data); exit;
			$id = $this->getRequest()->getParam('id');
			$poProductIds = $data['po_product'];
			
			$model = Mage::getModel('inventorymanager/purchaseorder');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			try {
				$model->save();
				
				$orderProduct = Mage::getModel('inventorymanager/product')->getCollection();
				$orderProduct->addFieldToFilter('po_id', $model->getId());
				$orderProduct->addFieldToFilter('product_id', array('nin' => $poProductIds));
				foreach ($orderProduct as $orderP){
					$orderP->delete();
				}
				
				
				$productData['po_id'] = $model->getId();
				$tatalQty = 0;
				foreach ($data['qty'] as $productId => $qty){
					if(!in_array($productId, $poProductIds)){ continue; }
					$tatalQty+=$qty;
					$productData['qty'] = $qty;
					$productData['main_product_id'] = $productId;
					$productData['price'] = $data['product_value'][$productId];
					$productData['total'] = $productData['qty'] * $productData['price'];
					$orderProduct = Mage::getModel('inventorymanager/product');
					$existOrderProductColl = Mage::getModel('inventorymanager/product')->getCollection();
					$existOrderProductColl->addFieldToFilter('po_id', $model->getId());
					$existOrderProductColl->addFieldToFilter('main_product_id', $productId);
					if($existOrderProductColl->count() > 0){
						$existOrderProductObject = $existOrderProductColl->getFirstItem();
						$orderProduct->setId($existOrderProductObject->getId());
					}
					$orderProduct->setData($productData);
					$orderProduct->save();
				}
				//if(isset($data['id'])){
					
				//}
				$model->setOrderQty($tatalQty)->save();
				if($id == "" || $id <= 0){
					Mage::getModel('inventorymanager/label')->generateLabels($model->getId());
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('inventorymanager')->__('Order was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back') && $this->getRequest()->getParam('back') == 1) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventorymanager')->__('Unable to find order to save'));
        $this->_redirect('*/*/');
	}
	
	
}
