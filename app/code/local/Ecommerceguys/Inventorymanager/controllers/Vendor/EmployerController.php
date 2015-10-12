<?php

class Ecommerceguys_Inventorymanager_Vendor_EmployerController extends Mage_Core_Controller_Front_Action
{
	
	protected function _getSession()
    {
        return Mage::getSingleton('inventorymanager/session');
    }

	public function indexAction(){
	   if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/login');
            return;
        }
     
		$this->loadLayout();
		$this->renderLayout();
	}

	public function newAction() {
	   if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/login');
            return;
        }
     
		$this->_forward('edit');
	}

	public function editAction(){


		$id     = $this->getRequest()->getParam('vendor_id');
		$model  = Mage::getModel('inventorymanager/vendor')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('vendor_employer_data', $model);

			$this->loadLayout();
			$this->renderLayout();
		} else {

			Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__('Vendor does not exist'));
			$this->_redirect('inventorymanager/vendor_employer');
		}
	}
	public function saveAction() {
		
		
		if ($data = $this->getRequest()->getPost()) {
			
			$model = Mage::getModel('inventorymanager/vendor');
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('core/session')->setFormData(false);


				$this->_redirect('inventorymanager/vendor_employer');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::getSingleton('core/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__('Unable to find vendor to save'));
        $this->_redirect('inventorymanager/vendor_employer');
	}

		public function deleteAction() {
		if( $this->getRequest()->getParam('vendor_id') > 0 ) {
			try {
				$model = Mage::getModel('inventorymanager/vendor');
				 
				$model->setId($this->getRequest()->getParam('vendor_id'))
					->delete();
					 
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('adminhtml')->__('Employer was successfully deleted'));
				$this->_redirect('inventorymanager/vendor_employer');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('core/session')->addError($e->getMessage());
				$this->_redirect('inventorymanager/vendor_employer');
				return;
			}
		}
		$this->_redirect('inventorymanager/vendor_employer');
	}


}