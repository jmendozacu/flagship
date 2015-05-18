<?php 
class Ecommerceguys_Inventorymanager_Adminhtml_CommentController extends Mage_Adminhtml_Controller_action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function postAction(){
		if($data = $this->getRequest()->getPost()){
			$model = Mage::getModel('inventorymanager/comment');
			$model->setData($data);
			$model->setCreatedTime(now());
			try {
				$model->save();
			}catch (Exception $e){
				
			}
		}
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