<?php
$includeFile = str_replace(array("index.php","index.php/"), "", Mage::getBaseDir().'/tcpdf/tcpdf.php');
require_once($includeFile);
class Ecommerceguys_Inventorymanager_Adminhtml_OrderController extends Mage_Adminhtml_Controller_action 
{
	public function printAction(){
		$ids = $this->getRequest()->getParam('order_ids');
		
		$content = "";
		foreach ($ids as $id){
			$content .= $this->getLayout()->createBlock('inventorymanager/adminhtml_sales_order_print')->setOrderId($id)->toHtml();
		}
		
		//echo $content;
		
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Inventory Manager');
		$pdf->SetTitle('Inventory Manager');
		$pdf->SetSubject('');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 8);
		$pdf->writeHTML($content, true, false, false, false, '');
		$pdf->lastPage();
		$pdf->Output('orders.pdf', 'D');
	}
}