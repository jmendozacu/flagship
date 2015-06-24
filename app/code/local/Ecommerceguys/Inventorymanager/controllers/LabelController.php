<?php
require_once(Mage::getBaseDir().'/tcpdf/tcpdf.php');
class Ecommerceguys_Inventorymanager_LabelController extends Mage_Core_Controller_Front_Action 
{
	public function generateAction(){
		$orderId = $this->getRequest()->getParam('id');
		
		$labelCollection = Mage::getModel('inventorymanager/label')->getCollection();
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
		}
		
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
		$pdf->Output('orderlabel.pdf', 'D');
	}
	
	
	public function findAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function editAction(){
		$apiKey = $this->getRequest()->getParam('serial_key');
		$validate = $this->validateApiKey();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function validateApiKey(){
		
	}
}