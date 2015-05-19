<?php 
class Ecommerceguys_Inventorymanager_Adminhtml_CommentController extends Mage_Adminhtml_Controller_action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function postAction(){
		if($data = $this->getRequest()->getPost()){
			if(isset($_FILES['send_file']['name']) && $_FILES['send_file']['name'] != '') {
				try {	
					$uploader = new Varien_File_Uploader('send_file');
					//$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS ."purchaseorder_comments" . DS ;
					$uploader->save($path, $_FILES['send_file']['name'] );
					
				} catch (Exception $e) {
		      
		        }
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('inventorymanager')->__('Comment is added'));
			}catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/adminhtml_purchaseorder/edit' , array('id'=>$data['po_id']));
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
}