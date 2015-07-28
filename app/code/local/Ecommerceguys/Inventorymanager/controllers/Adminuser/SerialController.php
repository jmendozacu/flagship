<?php
class Ecommerceguys_Inventorymanager_Adminuser_SerialController extends Mage_Core_Controller_Front_Action
{
	public function findreceiveAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function receiveAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function receivepostAction(){
		if($data = $this->getRequest()->getPost()){
			$model = Mage::getModel('inventorymanager/label')->load($data['label_id']);
			try{
				$model->setStatus($data['status']);
				$model->setLocation($data['location']);
				if(isset($_FILES['main_image']) && $_FILES['main_image']['name'] != ""){
					try {	
						$uploader = new Varien_File_Uploader('main_image');
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$path = Mage::getBaseDir('media') . DS . "label" . DS ;
						$uploader->save($path,$model->getId() . "_" . $_FILES['main_image']['name'] );
					} catch (Exception $e) {
			      
			        }
		  			$data['main_image'] = $uploader->getUploadedFileName();
				}
				//print_r($data); exit;
				$model->setMainImage($data['main_image']);
				if(isset($data['remove_main_image']) && $data['remove_main_image'] == 1){
					$model->setMainImage("");
				}
				$model->setIsInStock(1);
				$model->save();
				
				$productModel = Mage::getModel('catalog/product')->load($data['main_product_id']);
				$stocklevel = Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($productModel);
                $productQty = 0;
                if($stocklevel)
                	$productQty = $stocklevel->getQty();
                
                $productModel->setStockData(array( 
		            'qty' => $productQty + 1,
		            'is_in_stock' => 1,
		            'manage_stock' => 1,
		        )); 
				$productModel->save();
				
				if(isset($data['comment']) && trim($data['comment'])!= ""){
					$comment = Mage::getModel('inventorymanager/label_comment');
					$commentData = array(
						'comment'	=>	trim($data['comment']),
						'created_time'	=>	now(),
						'label_id'	=>	$model->getId()
					);
					$comment->setData($commentData)->save();
					
					if(isset($_FILES['comment_image']) && $_FILES['comment_image']['name'] != ""){
						try {	
							/* Starting upload */	
							$uploader = new Varien_File_Uploader('comment_image');
							// Any extention would work
			           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
							$uploader->setAllowRenameFiles(false);
							$uploader->setFilesDispersion(false);
									
							// We set media as the upload dir
							$path = Mage::getBaseDir('media') . DS . "label" . DS . "comments" . DS;
							$uploader->save($path,$comment->getId() . "_" . $_FILES['comment_image']['name'] );
							
						} catch (Exception $e) {
				      
				        }
			        
				        //this way the name is saved in DB
			  			$data['comment_image'] = $comment->getId() . "_" .$_FILES['comment_image']['name'];
					}
					$comment->setImage($data['comment_image'])->save();
				}
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__("Product label updated successfully"));
			}catch (Exception $e){
				Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Something went wrong, Please try again"));
			}
			$this->_redirect('*/*/receivepost', array('serial_key'=>$model->getSerial()));
		}
	}
}