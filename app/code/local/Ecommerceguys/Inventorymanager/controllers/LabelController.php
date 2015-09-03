<?php
require_once(Mage::getBaseDir().'/tcpdf/tcpdf.php');
class Ecommerceguys_Inventorymanager_LabelController extends Mage_Core_Controller_Front_Action 
{
	
	protected function _getSession()
    {
        return Mage::getSingleton('inventorymanager/session');
    }
	
	public function preDispatch(){
		parent::preDispatch();
		if (!$this->_getSession()->isLoggedIn() && !$this->_getSession()->isAdminUser()) {
            $this->_redirect('*/vendor/login');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
	}
	
	public function generateAction(){
		$orderId = $this->getRequest()->getParam('id');
		
		/*$labelCollection = Mage::getModel('inventorymanager/label')->getCollection();
		$labelCollection->addFieldToFilter('order_id', $orderId);
		if(!$labelCollection->count() || $labelCollection->count() <= 0){
			$products = Mage::getModel('inventorymanager/product')->getCollection();
			$products->addFieldToFilter('po_id', $orderId);
			foreach ($products as $product){
				for($qtyCounter = 1; $qtyCounter <= $product->getQty(); $qtyCounter++){
					$serial = Mage::helper('inventorymanager')->getSerial();
					$label = Mage::getModel('inventorymanager/label');
					$labelData = array(
						'product_id'	=>	$product->getId(),
						'order_id'		=>	$orderId,
						'location'		=>	1,
						'serial'		=>	$serial,
						'created_time'	=>	now(),
						'updated_time'	=>	now()
					);
					$label->setData($labelData)->save();
				}
			}
			
			//exit;
		}*/
		
		$content = $this->getLayout()->createBlock('inventorymanager/label_generate')
		->setTemplate('inventorymanager/labelgenerate.phtml')->toHtml();
		
		//echo $content;
		
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Inventory Manager');
		$pdf->SetTitle('Inventory Manager');
		$pdf->SetSubject('');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		$pdf->SetHeaderData('/../skin/frontend/default/theme279/images/prohoods_logo_sm.png', 0, '', '');
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		// ---------------------------------------------------------
		// set font
		// add a page
		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 8);
		
		$pdf->writeHTML($content, true, false, false, false, '');
		$pdf->lastPage();

		// ---------------------------------------------------------
		//Close and output PDF document
		$pdf->Output($orderId.'_orderlabel.pdf', 'D');
	}
	
	
	public function findAction(){
		$this->loadLayout();
		$this->_initLayoutMessages('core/session');
		$this->renderLayout();
	}
	
	public function editAction(){
		$serialKey = $this->getRequest()->getParam('serial_key');
		$validate = $this->validateSerialKey();
		if(!$validate){
			Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Not valid serial key"));
			$this->_redirect("*/*/find");
			return $this;
		}
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function validateSerialKey(){
		$serialKey = $this->getRequest()->getParam('serial_key');
		$labelCollection = Mage::getModel('inventorymanager/label')->getCollection();
		$labelCollection->addFieldToFilter('serial', $serialKey);
		if($labelCollection->count() > 0){
			return true;
		}
		return false;
	}
	
	public function editpostAction(){
		if($data = $this->getRequest()->getPost()){
			$diffLocation = false;
			$diffStatus = false;
			$model = Mage::getModel('inventorymanager/label')->load($data['label_id']);
			if($data['location'] != $model->getLocation()){
				$diffLocation = true;
			}
			if($data['status'] != $model->getStatus()){
				$diffStatus = true;
			}
			try{
				$serialHistory = Mage::getModel('inventorymanager/label_history');
				if($diffLocation && $diffStatus){
					$serialHistory->addStatusAndLocation($model->getId());
				}elseif($diffLocation){
					$serialHistory->addLocation($model->getId());
				}elseif ($diffStatus){
					$serialHistory->addStatus($model->getId());
				}
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
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__("Product label updated successfully"));
			}catch (Exception $e){
				Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__("Something went wrong, Please try again"));
			}
			$this->_redirect('*/*/edit', array('serial_key'=>$model->getSerial()));
		}
	}
	
	public function addnewstatusAction(){
		$status = $this->getRequest()->getParam('status');
		$staticStatuses = Mage::helper('inventorymanager')->getStaticStatusOptions();
		if(in_array($status, $staticStatuses)){
			return;
		}
		
		try{
			Mage::getModel('inventorymanager/label')->setNewStatus($status);
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	
	public function removenewstatusAction(){
		$status = $this->getRequest()->getParam('status');
		try{
			Mage::getModel('inventorymanager/label')->removeStatus($status);
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	
	public function addlocationAction(){
		$location = $this->getRequest()->getParam('location');
		try {
			Mage::getResourceModel('inventorymanager/label')->addLocation($location);
		}catch (Exception $e){
			Mage::log($e->getMessage());
		}
	}
	
	public function removelocationAction(){
		$location = $this->getRequest()->getParam('location');
		try {
			Mage::getResourceModel('inventorymanager/label')->removeLocation($location);
		}catch (Exception $e){
			Mage::log($e->getMessage());
		}
	}
	
	public function updatelocationAction(){
		$serialId = $this->getRequest()->getParam('serial_id');
		$serial = Mage::getModel('inventorymanager/label')->load($serialId);
		if($serial && $serial->getId()){
			try {
				$serial->setLocation($this->getRequest()->getParam('location'))->save();
			}catch (Exception $e){
				echo Mage::helper('inventorymanager')->__("Something went wrong. Please try again");
			}
		}else{
			echo Mage::helper('inventorymanager')->__("Serial object not found");
		}
	}
}