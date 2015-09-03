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
				$model->save();
				
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
			$orderIncrementId = $data['order_number'];
			$order = Mage::getModel('sales/order')->load($orderIncrementId, "increment_id");
			if($order && $order->getId()){

				
				$this->_generatePdf($order->getId());

				$serial = $data['serial_key'];
				$serialModel = Mage::getModel('inventorymanager/label')->load($serial, "serial");
				if($serialModel && $serialModel->getId()){
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
							$serialModel->setRealOrderId($order->getId())->setIsOutStock(1)->save();
							Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__("Product Sent"));

							//$this->_generatePdf($order->getId());
							
							$this->_redirect('*/*/find');
							return;
						}else{
							Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Order and serial mismatch"));
							$this->_redirect('*/*/find');
							return;
						}
					}
				}
				Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Serial not found"));
				$this->_redirect('*/*/find');
				return;
			}
			Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Order not found"));
			$this->_redirect('*/*/find');
			return;
		}
	}
	
	public function sentAction(){
		$this->loadLayout();
		$this->renderLayout();
	}

	protected function _generatePdf($orderId){

		// If an order actually exists
		if ($orderId) {

		    //Get the order details based on the order id ($orderId)
		    $order = Mage::getModel('sales/order')->load($orderId);

		    // Get the id of the orders shipping address
		    $shippingId = $order->getShippingAddress()->getId();

		    // Get shipping address data using the id
		    $address = Mage::getModel('sales/order_address')->load($shippingId);
		    $addressArr = $address->getData();
		    // Display the shipping address data array on screen
		    $region = Mage::getModel('directory/region')->load($addressArr['region_id']);

		    /*Array
				(
				    [entity_id] => 4671
				    [parent_id] => 2341
				    [customer_address_id] => 
				    [quote_address_id] => 
				    [region_id] => 58
				    [customer_id] => 
				    [fax] => 
				    [region] => Utah
				    [postcode] => 85432
				    [lastname] => dsadas
				    [street] => 2321321
				w32131
				    [city] => SLC
				    [email] => dsa@sad.com
				    [telephone] => 3234242432
				    [country_id] => US
				    [firstname] => dasda
				    [address_type] => shipping
				    [prefix] => 
				    [middlename] => 
				    [suffix] => 
				    [company] => 
				    [vat_id] => 
				    [vat_is_valid] => 
				    [vat_request_id] => 
				    [vat_request_date] => 
				    [vat_request_success] => 
			)*/

			$shipment = new \RocketShipIt\Shipment('fedex');

			if(!$address->getCompany() || $address->getCompany() == ''){
				$shipment->setParameter('toCompany','test');	
			}else{
				$shipment->setParameter('toCompany',$address->getCompany());
			}
			

			$shipment->setParameter('toName',$address->getFirstname());
			$shipment->setParameter('toPhone',$address->getTelephone());
			$shipment->setParameter('toAddr1',$addressArr['street']);
			$shipment->setParameter('toCity',$address->getCity());
			$shipment->setParameter('toState',Mage::getModel('directory/region')->load($address->getRegionId())->getCode());

			$shipment->setParameter('toCode',84115);
			
			$shipment->setParameter('length',10);
			$shipment->setParameter('width',10);
			$shipment->setParameter('height',10);
			$shipment->setParameter('weight',10);

			$response = $shipment->submitShipment();
			//print_r($response);

			if($response['tracking_id'] && $response['label_img']){
				$data = base64_decode($response['label_img']);
				header('Content-type: application/pdf');
				header('Content-Disposition: attachment; filename='.$orderId.".pdf");
				echo $data;
				return;
			}
		}
		return;

	} 
}