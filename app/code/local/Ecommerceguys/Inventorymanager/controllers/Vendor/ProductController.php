<?php
class Ecommerceguys_Inventorymanager_Vendor_ProductController extends Mage_Core_Controller_Front_Action
{
	public function editAction(){
		$this->loadLayout();
		$this->_initLayoutMessages('core/session');
		$this->renderLayout();
	}
	
	public function uploadAction(){
		if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
			try {	
				$uploader = new Varien_File_Uploader('upl');
           		//$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('media') . DS . "uploads" . DS ;
				$uploader->save($path, $_FILES['upl']['name'] );
				//echo Mage::helper('inventorymanager')->__("File Uploaded");
				echo 1;
				exit;
			} catch (Exception $e) {
				echo 2;
	      		//echo Mage::helper('inventorymanager')->__("File uploading fail");
	        }
		}
		exit;
	} 
	
	
	public function saveProeductinfoAction(){
		if($data = $this->getRequest()->getPost()){
			
			$files = explode(",",$data['file']);
			$files = array_filter($files);
			if(sizeof($files) > 0){
				$jsonFiles = Mage::helper('core')->jsonEncode($files);
				$data['files'] = $jsonFiles;
			}
			$vendorProduct = Mage::getModel('inventorymanager/vendor_productinfo');
			$vendorProduct->setData($data);
			$vendorProduct->setCreatedTime(now());
			try {
				$vendorProduct->save();
				Mage::getSingleton('core/session')->addSuccess($this->__("Product Information updated successfully"));
				
			}catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}else{
			Mage::getSingleton('adminhtml/session')->addError($this->__("Something went wrong"));
		}
		$this->_redirect("*/*/edit", array('id'=>$data['product_id']));
	}
}