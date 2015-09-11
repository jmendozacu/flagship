<?php
require_once(Mage::getBaseDir().'/tcpdf/tcpdf.php');
class Ecommerceguys_Inventorymanager_PurchaseorderController extends Mage_Core_Controller_Front_Action
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
	
	public function gridAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function viewAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function downloadAction(){
		$fileName = $this->getRequest()->getParam('file','');
		$filepath = Mage::getBaseDir('media')."/purchaseorder_comments/".$fileName;
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
	
	public function downloadproductpdfAction(){
		$data = $this->getRequest()->getParams();
		$productId = $data["product_id"];
		$orderId = $data["order_id"];
		
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
		}*/
		
		
		$content = $this->getLayout()->createBlock('inventorymanager/purchaseorder_productpdf')
		->setTemplate('inventorymanager/purchaseorder/productpdf.phtml')->toHtml();
		
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
		$pdf->Output($productId.'_productserials.pdf', 'D');
	}
	
	public function seenAction(){
		$id = $this->getRequest()->getParam('id');
		$purchaseorder = Mage::getModel('inventorymanager/purchaseorder')->load($id);
		if($purchaseorder && $purchaseorder->getId()){
			try {
				$purchaseorder->setIsSeen(1)->save();
				$this->_redirect("*/*/view", array("id"=>$purchaseorder->getId()));
			}catch (Exception $e){
				
			}
		}
	}
}