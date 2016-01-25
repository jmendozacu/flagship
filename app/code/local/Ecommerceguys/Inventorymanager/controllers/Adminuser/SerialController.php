<?php

require_once(Mage::getBaseDir()."/RocketShipIt/autoload.php");

class Ecommerceguys_Inventorymanager_Adminuser_SerialController extends Mage_Core_Controller_Front_Action
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
			
			//print_r($data); exit;
			
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
				if($model->getIsInStock() == 0){
					$model->setIsInStock(1);
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
				}
				
				if(isset($data['is_shipped']) && $data['is_shipped'] == 1){
					$model->setStatus("Shipped");
				}
				
				$model->save();
				
				if(isset($data['comment']) && trim($data['comment'])!= ""){
					$comment = Mage::getModel('inventorymanager/label_comment');
					$commentData = array(
						'comment'	=>	trim($data['comment']),
						'created_time'	=>	now(),
						'label_id'	=>	$model->getId()
					);
					$comment->setData($commentData)->save();
					$model->setIsSeen(2)->save();
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
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__("Product Received"));
			}catch (Exception $e){
				Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Something went wrong, Please try again"));
			}
			$this->_redirect('*/*/receive', array('serial_key'=>$model->getSerial()));
		}
	}
	
	public function findAction(){



		//echo Mage::getBaseDir();exit;
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function findpostAction(){
		if($data = $this->getRequest()->getPost()){
			if ($this->getRequest()->getPost('order_number') and $this->getRequest()->getPost('serial_key')){
				$orderIncrementId = $data['order_number'];
				$order = Mage::getModel('sales/order')->load($orderIncrementId, "increment_id");
				if($order && $order->getId()){
					$serial = $data['serial_key'];
					$serialModel = Mage::getModel('inventorymanager/label')->load($serial, "serial");
					if($serialModel and $serialModel->getId()){
						if($serialModel->getIsOutStock() == 1){
							Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Product already sent"));
							$this->_redirect('*/*/find');
							return;
						}
						$purchaseorderProduct = Mage::getModel('inventorymanager/product')->load($serialModel->getProductId());
						if($purchaseorderProduct && $purchaseorderProduct->getId()){
							$productId = $purchaseorderProduct->getMainProductId();
							$isOrderContainsThisProduct = false;
							foreach ($order->getAllItems() as $item){
								if($item->getProductId() == $productId){
									$isOrderContainsThisProduct = true;
									break;
								}
							}
							if($isOrderContainsThisProduct){
								
								//$shippingAddress = $order->getShippingAddress();
								
								$purchaseOrderObject = Mage::getModel('inventorymanager/purchaseorder')->load($serialModel->getOrderId());
								$receivedQty = $purchaseOrderObject->getReceivedQty();
								if($receivedQty == ""){
									$receivedQty = 0;
								}
								$purchaseOrderObject->setReceivedQty($receivedQty+1)->save();
								$serialModel->setStatus("Received")->save();
								//$return = $this->_generatePdf();
								
								
								$response = Mage::getResourceModel('inventorymanager/api_fedex')->getResponse($serialModel->getId(), $order->getId());
								
								$zipname = Mage::getBaseDir().'\\media\\fedex\\'.$serialModel->getId() . '-shippingdoc.zip';
								$zip = new ZipArchive;
								$zip->open($zipname, ZipArchive::CREATE);
								//foreach ($files as $file) {
								
								$download_file1 = file_get_contents(Mage::getBaseDir().'\\media\\fedex\\billoflanding\\' . $serialModel->getId() . "-BillOfLading.pdf");
								$zip->addFromString($serialModel->getId() . "-BillOfLading.pdf",$download_file1);
								
								
								$download_file2 = file_get_contents(Mage::getBaseDir().'\\media\\fedex\\shippinglabels\\' . $serialModel->getId() . "-ShippingLabel.pdf");
								$zip->addFromString($serialModel->getId() . "-ShippingLabel.pdf",$download_file2);
								
								//$zip->addFile();
								//$zip->addFile(Mage::getBaseDir().'\\media\\fedex\\billoflanding\\' . $serialModel->getId() . "-ShippingLabel.pdf");
								//}
								$zip->close();
								
								//print_r($response);
								//header('Content-type: application/pdf');
								//header('Content-Disposition: attachment; filename=' . $serialModel->getId() . "-BillOfLading.pdf");
								//header('Content-Disposition: attachment; filename=' . $serialModel->getId() . "-ShippingLabel.pdf");
								
								header('Content-Type: application/zip');
								header('Content-disposition: attachment; filename='.$serialModel->getId() . '-shippingdoc.zip');
								header('Content-Length: ' . filesize($zipname));
								readfile($zipname);
								
								die();
								//exit;
								
								
								if($return['tracking_id'] && $return['label_img']){
									$serialModel->setRealOrderId($order->getId())->setIsOutStock(1)->setShippingPrice($return['charges'])->save();
									
									Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__("Product Sent"));

									$data = base64_decode($return['label_img']);
									header('Content-type: application/pdf');
									header('Content-Disposition: attachment; filename=' . $order->getId() . ".pdf");
									die($data);

								}else{
									Mage::getSingleton('core/session')->addError($return['error']);	
								}			
								
								$this->_redirect('*/*/find');
								return;
							}else{
								Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Order and serial mismatch"));
								$this->_redirect('*/*/find');
								return;
							}
						}else{
							Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Product not found"));
							$this->_redirect('*/*/find');
							return;	
						}
					}else{
						Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Serial not found"));
						$this->_redirect('*/*/find');
						return;
					}
				}else{
					Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Order not found"));
					$this->_redirect('*/*/find');
					return;
				}
			}else{
				$return = $this->_generatePdf();
				$data = base64_decode($return['label_img']);
				header('Content-type: application/pdf');
				header('Content-Disposition: attachment; filename=' . date('Y-m-d_H-i-s') . ".pdf");
				die($data);
			}
		}
	}
	
	public function sentAction(){
		$this->loadLayout();
		$this->renderLayout();
	}

	protected function _generatePdf(){
		$shipment = new \RocketShipIt\Shipment('fedex');

		
		$shipment->setParameter('shipper', Mage::getStoreConfig('inventorymanager/label_cfg/shipper',Mage::app()->getStore()));
		$shipment->setParameter('shipPhone', Mage::getStoreConfig('inventorymanager/label_cfg/phone',Mage::app()->getStore()));
		$shipment->setParameter('shipAddr1', Mage::getStoreConfig('inventorymanager/label_cfg/address',Mage::app()->getStore()));
		$shipment->setParameter('shipAddr2', Mage::getStoreConfig('inventorymanager/label_cfg/address2',Mage::app()->getStore()));
		$shipment->setParameter('shipCity', Mage::getStoreConfig('inventorymanager/label_cfg/city',Mage::app()->getStore()));
		$shipment->setParameter('shipState', Mage::getStoreConfig('inventorymanager/label_cfg/state',Mage::app()->getStore()));
		$shipment->setParameter('shipCode', Mage::getStoreConfig('inventorymanager/label_cfg/zip_code',Mage::app()->getStore()));
		$shipment->setParameter('accountNumber', Mage::getStoreConfig('inventorymanager/label_cfg/account_number',Mage::app()->getStore()));
		$shipment->setParameter('meterNumber', Mage::getStoreConfig('inventorymanager/label_cfg/meter_number',Mage::app()->getStore()));



		$shipment->setParameter('toName', $this->getRequest()->getPost('to_name'));
		$shipment->setParameter('toPhone',$this->getRequest()->getPost('to_phone'));
		$shipment->setParameter('toAddr1', $this->getRequest()->getPost('to_address'));
		$shipment->setParameter('toCity', $this->getRequest()->getPost('city'));
		$shipment->setParameter('toState', $this->getRequest()->getPost('state'));
		$shipment->setParameter('toCode', $this->getRequest()->getPost('to_code'));
		$shipment->setParameter('length', $this->getRequest()->getPost('length'));
		$shipment->setParameter('width',$this->getRequest()->getPost('width'));
		$shipment->setParameter('height',$this->getRequest()->getPost('height'));
		$shipment->setParameter('weight',$this->getRequest()->getPost('weight'));
		$shipment->setParameter('service', $this->getRequest()->getPost('shipping_option'));
		return $shipment->submitShipment();

	} 

	protected function getrateAction(){
		$rate = new \RocketShipIt\Rate('fedex');
		$rate->setParameter('shipCode', Mage::getStoreConfig('inventorymanager/label_cfg/zip_code',Mage::app()->getStore()));
		$rate->setParameter('toCode', $this->getRequest()->getPost('to_code'));
		$rate->setParameter('length', $this->getRequest()->getPost('length'));
		$rate->setParameter('width',$this->getRequest()->getPost('width'));
		$rate->setParameter('height',$this->getRequest()->getPost('height'));
		$rate->setParameter('weight',$this->getRequest()->getPost('height'));
		$rate->setParameter('service', $this->getRequest()->getPost('shipping_option'));
		$result = $rate->getRate();
		$_return = array('price' => 0);
		if ($result['RateReply']['Notifications']['Severity'] != 'NOTE'){
			$_return['error'] = $result['RateReply']['Notifications']['Message'];
		}

		foreach ($result['RateReply']['RateReplyDetails']['RatedShipmentDetails'] as $rate){
			if ($rate['ShipmentRateDetail']['RateType'] == 'PAYOR_ACCOUNT_PACKAGE'){
				$_return['price'] = number_format($rate['ShipmentRateDetail']['TotalNetCharge']['Amount'], 2);
			}
		}
		die(json_encode($_return));
	}
	
	public function locationsAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function addLocationAction(){
		$params = $this->getRequest()->getParams();
		$session = $this->_getSession();
		if(isset($params['location']) && $params['location'] != ""){
			Mage::getResourceModel('inventorymanager/label')->addLocationFromAgent($params);
			$session->addSuccess(Mage::helper('inventorymanager')->__("Location added."));
			$this->_redirect("inventorymanager/adminuser_serial/locations");
			return $this;
		}
		$session->addError(Mage::helper('inventorymanager')->__("Invalid data"));
		$this->_redirect("inventorymanager/adminuser_serial/locations");
		return $this;
	}
	
	public function removeLocationAction(){
		$data = $this->getRequest()->getParams();
		$session = $this->_getSession();
		if(isset($data['location']) && $data['location'] != ""){
			$labelResource = Mage::getResourceModel('inventorymanager/label');
			$labelResource->removeLocationFromAgent($data['location']);
			$session->addSuccess(Mage::helper('inventorymanager')->__("Location removed."));
			$this->_redirect("inventorymanager/adminuser_serial/locations");
			return $this;
		}
		$session->addError(Mage::helper('inventorymanager')->__("Invalid data"));
		$this->_redirect("inventorymanager/adminuser_serial/locations");
		return $this;
	}
	
	public function findorderAction(){
		$orderId = $this->getRequest()->getParam('order_id');
		$orderObject =  Mage::getModel('sales/order')->loadByIncrementId($orderId);
		
		if($orderObject && $orderObject->getId()){
			$address = $orderObject->getShippingAddress();
			
			
			
			$data = array();
			$data['name'] = $address->getName();
			$data['email'] = $orderObject->getCustomerEmail();
			$data['phone'] = $address->getTelephone();
			$data['address'] = $address->getStreet();
			$data['city'] = $address->getCity();
			$data['zipcode'] = $address->getPostcode();
			$data['country'] = $address->getCountryId();
			$data['region'] = $address->getRegion();
			$data['region_id'] = $address->getRegionId();
			echo Mage::helper('core')->jsonEncode($data);
		}
	}
	
	public function finddetailsAction(){
		$serialKey = $this->getRequest()->getParam('serial_key');
		$serialObject = Mage::getModel('inventorymanager/label')->load($serialKey, "serial");
		if($serialObject && $serialObject->getId()){
			
			$orderProduct = Mage::getModel('inventorymanager/product')->load($serialObject->getProductId());
			$catalogProduct = Mage::getModel('catalog/product')->load($orderProduct->getMainProductId());
			$purchaseorder = Mage::getModel('inventorymanager/purchaseorder')->load($serialObject->getOrderId());
			$productInfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
			$productInfoCollection->addFieldToFilter('vendor_id', $purchaseorder->getVendorId());
			$productInfoCollection->addFieldToFilter('product_id', $orderProduct->getMainProductId());
			if($productInfoCollection && $productInfoCollection->count() > 0){
				$productInfoObject = $productInfoCollection->getFirstItem();
				if($productInfoObject && $productInfoObject->getId()){
					
					//print_r($productInfoObject); exit;
					
					$data = array();
					$data['length']	= $productInfoObject->getBoxLength()!=""?$productInfoObject->getBoxLength():$productInfoObject->getLength();
					$data['width'] = $productInfoObject->getBoxWidth()!=""?$productInfoObject->getBoxWidth():$productInfoObject->getWidth();
					$data['height'] = $productInfoObject->getBoxHeight()!=""?$productInfoObject->getBoxHeight():$productInfoObject->getHeight();
					$data['weight']	= $productInfoObject->getWeight();
					$data['name']	= $catalogProduct->getName();
					echo Mage::helper('core')->jsonEncode($data);
				}
			}
			
			
		}
	}
	
	public function bulklocationAction(){
		$this->loadLayout();
		$this->_initLayoutMessages('core/session');
		$this->renderLayout();
	}
	
	public function savebulklocationAction(){
		$data = $this->getRequest()->getParams();
		
		if(isset($data['serial_keys']) && is_array($data['serial_keys'])){
			$serials = $data['serial_keys'];
			$serials = array_filter($serials);
			if(sizeof($serials) > 0){
				//try {
				$serialCount = 0;
				foreach ($serials as $serialKey) { 
					$serialKey = trim($serialKey);
					$serialObject = Mage::getModel('inventorymanager/label')->load($serialKey, "serial");
					if($serialObject && $serialObject->getId()){
						$serialCount++;
						try {
							$serialObject->setLocation($data['location'])->save();
						}catch (Exception $e){
						}
						//unset($serialObject);
					}
					//break;
				}
				if($serialCount > 0)
				{
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__("Serials Updated"));
					$this->_redirect("inventorymanager/adminuser_serial/bulklocation");
					return $this;
				}
				//}catch (Exception $e){
					
				//}
			}
		}
		
		Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("No serial found"));
		$this->_redirect("inventorymanager/adminuser_serial/bulklocation");
		return $this;
	}
}