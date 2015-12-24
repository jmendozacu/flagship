<?php 
class Ecommerceguys_Inventorymanager_Adminuser_Purchaseorder_CommentController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function postAction(){
		if($data = $this->getRequest()->getPost()){
			if(!isset($data['po_id']) || $data['po_id'] < 0){
				Mage::getSingleton('core/session')->addError(Mage::helper("inventorymanager")->__("No purchase order found"));
				$this->_redirect('*/adminuser_purchaseorder/edit');
				return false;
			}
			if(isset($_FILES['send_file']['name']) && $_FILES['send_file']['name'] != '') {
				/*try {	
					$uploader = new Varien_File_Uploader('send_file');
					//$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS ."purchaseorder_comments" . DS ;
					$uploader->save($path, $_FILES['send_file']['name'] );
					
				} catch (Exception $e) {
		      
		        }*/

	  			$data['attachement'] = $_FILES['send_file']['name'];
			}
			
			$model = Mage::getModel('inventorymanager/comment');
			$model->setData($data);
			$model->setCreatedTime(now());
			
			$purchaseorderModel = Mage::getModel('inventorymanager/purchaseorder')->load($data['po_id']);
			$purchaseorderModel->setStatus($data['status']); // UPDATE PURCHASEORDER STATUS
			try {
				$purchaseorderModel->save();
				$model->save();
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__('Comment is added'));
			}catch (Exception $e){
				Mage::getSingleton('core/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/adminuser_purchaseorder/edit' , array('id'=>$data['po_id']));
	}
	
	public function refreshAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function removeAction(){
		if($id = $this->getRequest()->getParam('id')){
			$model = Mage::getModel('inventorymanager/comment')->load($id);
			try{
				$model->delete();
			}catch (Exception $e){
				
			}
		}
	}
	
	public function downloadAction(){
		
		if($id = $this->getRequest()->getParam('id')){
			$comment = Mage::getModel('inventorymanager/comment')->load($id);
			try{
				$file_url = Mage::getBaseUrl('media')."purchaseorder_comments/" . $comment->getAttachement();
				header('Content-Type: application/octet-stream');
				header("Content-Transfer-Encoding: Binary"); 
				header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
				readfile($file_url); 
			}catch (Exception $e){
				
			}
		}
	}
	
	public function ajaxUploadAction(){
		$data = $this->getRequest()->getParams();
		if(isset($_FILES['send_file']['name']) && $_FILES['send_file']['name'] != '') {
			try {	
				$uploader = new Varien_File_Uploader('send_file');
				$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				$uploader->setAllowRenameFiles(false);
				
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('media') . DS ."purchaseorder_comments" . DS ;
				$result = $uploader->save($path, $_FILES['send_file']['name'] );
				$returnVar = "";
				if(isset($result['file'])){
					echo $result['file'];
				}
				
			} catch (Exception $e) {
	      
	        }
  			
		}
	}
}