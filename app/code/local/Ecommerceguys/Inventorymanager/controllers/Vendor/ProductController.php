<?php
require_once(Mage::getBaseDir().'/tcpdf/tcpdf.php');
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
				
				if(isset($_FILES['main_image']) && $_FILES['main_image']['name'] != ""){
					$data['main_image'] = $_FILES['main_image']['name'];
				}
				
				$activeObjectData = $activeObject->getData();
				// modify active array elements to compare with post data
				$activeObjectData['file'] = implode(",", Mage::helper('core')->jsonDecode($activeObjectData['files'])).",";
				$activeObjectData['description'] = trim($activeObjectData['description']);
				
				unset($activeObjectData['entity_id']);
				unset($activeObjectData['created_time']);
				unset($activeObjectData['updated_time']);
				unset($activeObjectData['is_revision']);
				unset($activeObjectData['files']);
				
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
			if(isset($_FILES['main_image']) && $_FILES['main_image']['name'] != ""){
				unset($data['main_image']);
			}
			if(isset($data['remove_main_image']) &&  $data['remove_main_image'] == 1){
				$data['main_image'] = "";
			}
			$vendorProduct = $this->getProductInfoModel();
			$vendorProduct->setData($data);
			$vendorProduct->setCreatedTime(now());
			$vendorResourceModel = Mage::getResourceModel('inventorymanager/vendor');
			try {
				$vendorProduct->save();
				$vendorResourceModel->addMaterial($data['material']);
				$vendorResourceModel->addLighting($data['lighting']);
				
				if(isset($_FILES['main_image']) && $_FILES['main_image']['name'] != ""){
					try {	
						$uploader = new Varien_File_Uploader('main_image');
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$path = Mage::getBaseDir('media') . DS . "productdetail" . DS ;
						$uploader->save($path,$vendorProduct->getId() . "_" . $_FILES['main_image']['name'] );
					} catch (Exception $e) {
			      
			        }
		  			$data['main_image'] = $uploader->getUploadedFileName();
				}
				$vendorProduct->setMainImage($data['main_image'])->save();
				
				
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
	
	public function gemeratePdfAction(){
		$productId = $this->getRequest()->getParam('product_id');
		$content = $this->getLayout()->createBlock('inventorymanager/vendor_product_generatepdf')
		->setTemplate('inventorymanager/vendor/product/generate.phtml')->toHtml();
		
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
		$pdf->Output($productId.'_orderlabel.pdf', 'D');
	}
	
	public function stockgridAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function serialgridAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	
	public function massproductsAction(){
		$this->loadLayout();
		$this->_initLayoutMessages('core/session');
		$this->renderLayout();
	}
	
	public function exportproductsAction(){
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=vendorproducts.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
		
		$vendorModel = Mage::getResourceModel('inventorymanager/vendor');
		$products = $vendorModel->getProducts($vendorId);
		
		$productInfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
		$productInfoCollection->addFieldToFilter("vendor_id", $vendorId);
		$productInfoCollection->addFieldToFilter("is_revision", 0);
		$venderProductinfoArray = array();
		foreach ($productInfoCollection as $productInfoObject){
			//print_r($productInfoObject->getData());
			
			$informationArray = array();
			$informationArray['cost'] = $productInfoObject->getCost();
			$informationArray['length'] = $productInfoObject->getLength();
			$informationArray['width'] = $productInfoObject->getWidth();
			$informationArray['height'] = $productInfoObject->getHeight();
			$informationArray['fun_spec'] = $productInfoObject->getFunSpec();
			$informationArray['material'] = $productInfoObject->getMaterial();
			$informationArray['lighting'] = $productInfoObject->getLighting();
			$informationArray['upc'] = $productInfoObject->getUpc();
			$informationArray['box_height'] = $productInfoObject->getBoxHeight();
			$informationArray['box_width'] = $productInfoObject->getBoxWidth();
			$informationArray['box_length'] = $productInfoObject->getBoxLength();
			$informationArray['box_weight'] = $productInfoObject->getBoxWeight();
			$informationArray['weight'] = $productInfoObject->getWeight();
			
			$venderProductinfoArray[$productInfoObject->getProductId()] = $informationArray;
		}
		
		echo "SKU,Cost,Weight KG,Length METER,Width METER,Height METER,Box Weight KG,Box Length METER,Box Width METER,Box Height METER,UPC Code,Material,Lighting,Full Specifications\n";
		foreach ($products as $product){
			echo $product['sku'];
			
			if(isset($venderProductinfoArray[$product['entity_id']])){
				$informationArray = $venderProductinfoArray[$product['entity_id']];
				echo ",";
				echo $informationArray['cost'] . ",";
				echo $informationArray['weight'] . ",";
				echo $informationArray['length']>0? $informationArray['length'] / 39.37 : "";
				echo ",";
				echo $informationArray['width']>0? $informationArray['width'] / 39.37 : "";
				echo ",";
				echo $informationArray['height']>0? $informationArray['height'] / 39.37 : "";
				echo ",";
				echo $informationArray['box_weight'] . ",";
				echo $informationArray['box_length']>0? $informationArray['box_length'] / 39.37 : "";
				echo ",";
				echo $informationArray['box_width']>0? $informationArray['box_width'] / 39.37 : "";
				echo ",";
				echo $informationArray['box_height']>0? $informationArray['box_height'] / 39.37 : "";
				echo ",";
				echo $informationArray['upc'] . ",";
				echo $informationArray['material'] . ",";
				echo $informationArray['lighting'] . ",";
				echo $informationArray['fun_spec'];
			}
			
			
			echo "\n";
		}
	}
	
	public function importproductsAction(){
		if(isset($_FILES["import_csv"])) {
		    $csv=file_get_contents($_FILES["import_csv"]["tmp_name"]);
		
			$csvArray = explode("\n", $csv);
			
			$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
			$productInfoArray = array();
			$productInfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
			$productInfoCollection->addFieldToFilter("vendor_id", $vendorId);
			$productInfoCollection->addFieldToFilter("is_revision", 0);
			foreach ($productInfoCollection as $productInfo){
				$productInfoArray[$productInfo->getProductId()] = $productInfo;
			}

			$error = false;
			//echo "<pre>";
			$iCounter = 0;
			foreach ($csvArray as $csvRow){
				$iCounter++;
				if($iCounter == 1){ continue; }
				$rowArray = explode(",", $csvRow);
				
				
				if(isset($rowArray[0])){
					//$catalogProduct = Mage::getModel('catalog/product')->load($rowArray[0], "sku");
					
					$product = Mage::getModel('catalog/product');
					$productid = Mage::getModel('catalog/product')->getResource()->getIdBySku($rowArray[0]);
					
					
					if($productid && $productid > 0){
						$insertData = array();
						
						$insertData['cost'] 		= isset($rowArray[1])?$rowArray[1]:"";
						$insertData['weight'] 		= isset($rowArray[2])?$rowArray[2]:"";
						$insertData['length'] 		= isset($rowArray[3])?$rowArray[3] * 39.37:"";
						$insertData['width'] 		= isset($rowArray[4])?$rowArray[4] * 39.37:"";
						$insertData['height'] 		= isset($rowArray[5])?$rowArray[5] * 39.37:"";
						$insertData['box_weight'] 	= isset($rowArray[6])?$rowArray[6]:"";
						$insertData['box_length'] 	= isset($rowArray[7])?$rowArray[7] * 39.37:"";
						$insertData['box_width'] 	= isset($rowArray[8])?$rowArray[8] * 39.37:"";
						$insertData['box_height']	= isset($rowArray[9])?$rowArray[9] * 39.37:"";
						$insertData['upc'] 			= isset($rowArray[10])?$rowArray[10]:"";
						$insertData['material']		= isset($rowArray[11])?$rowArray[11]:"";
						$insertData['lighting']		= isset($rowArray[12])?$rowArray[12]:"";
						$insertData['fun_spec']		= isset($rowArray[13])?$rowArray[13]:"";
						
						//print_r($insertData);
						
						
						try {
						
							if(isset($productInfoArray[$productid])){
								$infoObject = $productInfoArray[$productid];
								$infoObject->addData($insertData)->save();
								
								//echo "<br/> EXIST " . $infoObject->getId();
								
							}else{
								$insertData['is_revision']	=	0;
								$insertData['vendor_id']	=	$vendorId;
								$insertData['product_id']	=	$productid;
								$infoObject = Mage::getModel('inventorymanager/vendor_productinfo');
								$infoObject->setData($insertData)->save();
								
								//echo "<br/> NEW " . $infoObject->getId();
							}
							
						}catch (Exception $e){
							$error = true;
							
						}
					}
				}
			}
			//exit;
			if(!$error)
				Mage::getSingleton('core/session')->addSuccess($this->__("File is imported successfullly"));
			else
				Mage::getSingleton('core/session')->addError($this->__("Something went wrong"));
			$this->_redirect("*/*/massproducts");
						return $this;
		}
	}
}