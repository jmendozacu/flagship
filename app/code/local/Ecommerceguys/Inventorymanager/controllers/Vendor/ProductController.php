<?php
class Ecommerceguys_Inventorymanager_Vendor_ProductController extends Mage_Core_Controller_Front_Action
{
	public function editAction(){
		$this->loadLayout();
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
}