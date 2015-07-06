<?php
class Ecommerceguys_Inventorymanager_Vendor_ProductController extends Mage_Core_Controller_Front_Action
{
	
	protected function _getSession()
    {
        return Mage::getSingleton('inventorymanager/session');
    }
	
	public function preDispatch(){
		parent::preDispatch();
		if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/vendor/login');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
	}
	
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
			$data['description'] = trim($data['description']);
			if($activeObject  = $this->getProductInfoModel()->getActiveObject($data['vendor_id'], $data['product_id'])){
				// only enters in this condition if active object found
				
				$activeObjectData = $activeObject->getData();
				// modify active array elements to compare with post data
				$activeObjectData['file'] = implode(",", Mage::helper('core')->jsonDecode($activeObjectData['files'])).",";
				$activeObjectData['description'] = trim($activeObjectData['description']);
				unset($activeObjectData['entity_id']);
				unset($activeObjectData['created_time']);
				unset($activeObjectData['updated_time']);
				unset($activeObjectData['is_revision']);
				unset($activeObjectData['files']);
				
				//echo "POST DATA<br/>";
				//print_r($data);
				//echo "OBJ DATA<br/>";
				//print_r($activeObjectData);
				
				$difference = array_diff($data, $activeObjectData);
				if(sizeof($difference) <= 0){
					// if no change done then nothing to do
					Mage::getSingleton('core/session')->addSuccess($this->__("Product Information updated successfully"));
					$this->_redirect("*/*/edit", array('id'=>$data['product_id']));
					return $this;
				}
				//print_r($difference); 
			}			
			$this->getProductInfoModel()->setRevision($data['vendor_id'], $data['product_id']);
			
			$files = explode(",",$data['file']);
			$files = array_filter($files);
			if(sizeof($files) > 0){
				$jsonFiles = Mage::helper('core')->jsonEncode($files);
				$data['files'] = $jsonFiles;
			}
			$vendorProduct = $this->getProductInfoModel();
			$vendorProduct->setData($data);
			$vendorProduct->setCreatedTime(now());
			$vendorResourceModel = Mage::getResourceModel('inventorymanager/vendor');
			try {
				$vendorProduct->save();
				$vendorResourceModel->addMaterial($data['material']);
				$vendorResourceModel->addLighting($data['lighting']);
				Mage::getSingleton('core/session')->addSuccess($this->__("Product Information updated successfully"));
				
			}catch (Exception $e){
				Mage::getSingleton('core/session')->addError($e->getMessage());
			}
		}else{
			Mage::getSingleton('core/session')->addError($this->__("Something went wrong"));
		}
		$this->_redirect("*/*/edit", array('id'=>$data['product_id']));
	}
	
	public function downloadAction(){
		$fileName = $this->getRequest()->getParam('file','');
		$filepath = Mage::getBaseDir('media')."/uploads/".$fileName;
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filepath");
		header("Content-Type: mime/type");
		header("Content-Transfer-Encoding: binary");
		// UPDATE: Add the below line to show file size during download.
		header('Content-Length: ' . filesize($filepath));
		
		readfile($filepath);
	    exit;
	}
	
	public function getProductInfoModel(){
		return Mage::getModel('inventorymanager/vendor_productinfo');
	}
	
	public function showrevisionAction(){
		$this->loadLayout();
		$this->_initLayoutMessages('core/session');
		$this->renderLayout();
	}
	
	public function loadRevisionAction(){
		$revisionId = $this->getRequest()->getParam("revision_id");
		if($revisionId && $revisionId>0){
			try {
				$revisionObject = $this->getProductInfoModel()->load($revisionId);
				$this->getProductInfoModel()->setRevision($revisionObject->getVendorId(), $revisionObject->getProductId());
				$revisionObject->setIsRevision(0)->save();
				Mage::getSingleton('core/session')->addSuccess($this->__("Revision set successfullly"));
			}catch (Exception $e){
				Mage::getSingleton('core/session')->addError($e->getMessage());
			}
		}else{
			Mage::getSingleton('core/session')->addError($this->__("Something went wrong"));
		}
		$this->_redirect("*/*/edit", array('id'=>$revisionObject->getProductId()));
	}
	
	public function addmaterialAction(){
		$material = $this->getRequest()->getParam('material');
		if($material != ""){
			try{
				$resourceVendor = Mage::getResourceModel('inventorymanager/vendor')->addMaterial($material);
			}catch (Exception $e){
				echo $e->getMessage();
			}
		}
	}
	
	public function removematerialAction(){
		$material = $this->getRequest()->getParam('material');
		if($material != ""){
			try{
				$resourceVendor = Mage::getResourceModel('inventorymanager/vendor')->removeMaterial($material);
			}catch (Exception $e){
				echo $e->getMessage();
			}
		}
	}
	
	public function addlightingAction(){
		$lighting = $this->getRequest()->getParam('lighting');
		if($lighting != ""){
			try {
				$resourceVendor = Mage::getResourceModel('inventorymanager/vendor')->addLighting($lighting);
			}catch (Exception $e){
				
			}
		}
	}
	
	public function removelightingAction(){
		$lighting = $this->getRequest()->getParam('lighting');
		if($lighting != ""){
			try {
				$resourceVendor = Mage::getResourceModel('inventorymanager/vendor')->removeLighting($lighting);
			}catch (Exception $e){
				
			}
		}
	}
}