<?php
class Ecommerceguys_Inventorymanager_AdminuserController extends Mage_Core_Controller_Front_Action
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
	
	public function vendorsAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function logoutAction(){
		$session = Mage::getSingleton('core/session');
		$session->setUserType("");
		$session->setUser("");
		$this->_redirect('*/vendor/login');
	}
	
	public function vendorproductAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	public function vendorprofilesAction(){
		$this->loadLayout();
		$this->renderLayout();
	}

	public function newvendorAction() {

		$this->_forward('vendoredit');
	}

	public function vendoreditAction(){

		$id     = $this->getRequest()->getParam('vendor_id');
		$model  = Mage::getModel('inventorymanager/vendor')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('vendor_data', $model);

			$this->loadLayout();
			$this->renderLayout();
		} else {
			Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__('Vendor does not exist'));
			$this->_redirect('inventorymanager/adminuser/vendorprofiles');
		}
	}
	public function vendorsaveAction() {

		if ($data = $this->getRequest()->getPost()) {
			
			//echo "<pre>";
			//print_r($data); exit;
			
			
			
			if(isset($data['selected_values'])){
				$products = explode(",",$data['selected_values']); //Save the array to your database
			}
			$model = Mage::getModel('inventorymanager/vendor');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));

			if(isset($data['active']) && $data['active'] == 0){
				$model->setActive(0)->save();
				Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Vender is inactive, Active vendor first."));
                Mage::getSingleton('core/session')->setFormData($data);
                $this->_redirect('*/*/vendoredit', array('vendor_id' => $this->getRequest()->getParam('id')));
                return $this;
			}	
				
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				$vendorProductResource = Mage::getResourceModel('inventorymanager/vendor_products');
				$vendorProductResource->remove($model->getId());
				
				//echo "<pre>";
					foreach ($products as $productId){
						$vendorProductResource->insertOne(array('product_id'=>$productId, 'vendor_id'=>$model->getId()));
					}
				//exit;

				Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__('Vendor saved successfully'));
				Mage::getSingleton('core/session')->setFormData(false);

				if($data['is_back'] == 1){
					$this->_redirect('*/*/vendoredit', array('vendor_id' => $this->getRequest()->getParam('id')));
				}else{
					$this->_redirect('inventorymanager/adminuser/vendorprofiles');
				}
				return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::getSingleton('core/session')->setFormData($data);
                $this->_redirect('*/*/vendoredit', array('vendor_id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__('Unable to find vendor to save'));
        $this->_redirect('inventorymanager/adminuser/vendorprofiles');
	}

	public function vendordeleteAction() {
		if( $this->getRequest()->getParam('vendor_id') > 0 ) {
			try {
				$model = Mage::getModel('inventorymanager/vendor');
				 
				$model->setId($this->getRequest()->getParam('vendor_id'))
				->setActive(0)
					->save();
					 
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('adminhtml')->__('Vendor was successfully deleted'));
				$this->_redirect('inventorymanager/adminuser/vendorprofiles');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('core/session')->addError($e->getMessage());
				$this->_redirect('inventorymanager/adminuser/vendorprofiles');
				return;
			}
		}
		$this->_redirect('inventorymanager/adminuser/vendorprofiles');
	}
    
}