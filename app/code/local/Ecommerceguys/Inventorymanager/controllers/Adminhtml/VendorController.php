<?php 
class Ecommerceguys_Inventorymanager_Adminhtml_VendorController extends Mage_Adminhtml_Controller_action
{
	
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('inventorymanager/vendors')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}
	
	public function indexAction(){
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('inventorymanager/vendor')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('vendor_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('inventorymanager/vendors');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('inventorymanager/adminhtml_vendor_edit'))
				->_addLeft($this->getLayout()->createBlock('inventorymanager/adminhtml_vendor_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventorymanager')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			//print_r($data); exit;
			if(isset($data['links'])){
				$products = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['products']); //Save the array to your database
			}
			
			
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
				$vendorProductResource = Mage::getResourceModel('inventorymanager/vendor_products');
				$vendorProductResource->remove($model->getId());
				foreach ($products as $productId){
					$vendorProductResource->insertOne(array('product_id'=>$productId, 'vendor_id'=>$model->getId()));
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('inventorymanager')->__('Vendor was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventorymanager')->__('Unable to find vendor to save'));
        $this->_redirect('*/*/');
	}
	
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('inventorymanager/vendor');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Vendor was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction() {
		//print_r($this->getRequest()->getParams()); exit;
        $vendorIds = $this->getRequest()->getParam('inventorymanager');
        if(!is_array($vendorIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorIds as $vendorId) {
                    $model = Mage::getModel('inventorymanager/vendor')->load($vendorId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($vendorIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function productsAction(){
    	$this->loadLayout();
        $this->getLayout()->getBlock('products.grid')
        ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }
    
    public function productsgridAction(){
    	$this->loadLayout();
        $this->getLayout()->getBlock('products.grid')
        ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }
}