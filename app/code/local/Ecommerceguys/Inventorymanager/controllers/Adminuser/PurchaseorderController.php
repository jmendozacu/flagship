<?php
require_once(Mage::getBaseDir().'/tcpdf/tcpdf.php');
class Ecommerceguys_Inventorymanager_Adminuser_PurchaseorderController extends Mage_Core_Controller_Front_Action
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
	
	public function indexAction(){
		
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function newAction() {

		$this->_forward('edit');
	}
	
	public function editAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function ordereditAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			//print_r($data); exit;
			$id = $this->getRequest()->getParam('id');
			$poProductIds = $data['po_product'];
			$mainProducts = $data['main_product'];
			
			/* BELLOW LOGIC USE TO CHANGE DATE FORMAT - TRIED WITH strtotime BUT WON'T WORK */
			$orderDate = explode("/", $data['date_of_po']);
			if(isset($orderDate[2]))
				$data['date_of_po'] = $orderDate[2]."-"; 
			if(isset($orderDate[1]))
				$data['date_of_po'] .= $orderDate[1]."-"; 
			if(isset($orderDate[0]))
				$data['date_of_po'] .= $orderDate[0];
				
			$expectedDate = explode("/", $data['expected_date']);
			if(isset($expectedDate[2]))
				$data['expected_date'] = $expectedDate[2]."-"; 
			if(isset($expectedDate[1]))
				$data['expected_date'] .= $expectedDate[1]."-"; 
			if(isset($expectedDate[0]))
				$data['expected_date'] .= $expectedDate[0];
			/***/
			
			$model = Mage::getModel('inventorymanager/purchaseorder');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			try {
				$model->save();
				
				$orderProduct = Mage::getModel('inventorymanager/product')->getCollection();
				$orderProduct->addFieldToFilter('po_id', $model->getId());
				$orderProduct->addFieldToFilter('product_id', array('nin' => $poProductIds));
				foreach ($orderProduct as $orderP){
					$orderP->delete();
				}
				
				
				$tatalQty = 0;
				
				foreach ($poProductIds as $orderProductId){
					$orderProductObject = Mage::getModel('inventorymanager/product')->load($orderProductId);
					if(isset($data['qty'][$orderProductObject->getMainProductId()])){
						$productData['qty'] = $data['qty'][$orderProductObject->getMainProductId()];
						if(isset($data['product_value'][$orderProductObject->getMainProductId()])){
							$productData['price'] = $data['product_value'][$orderProductObject->getMainProductId()];
							$orderProductObject->addData($productData);
							$tatalQty += $productData['qty'];
							try {
								$orderProductObject->save();
							}catch (Exception $e){
								
							}
						}
					}
				}
				
				
				$productData['po_id'] = $model->getId();
				foreach ($data['qty'] as $productId => $qty){
					if(in_array($productId, $mainProducts)){ continue; }
					$tatalQty+=$qty;
					$productData['qty'] = $qty;
					$productData['main_product_id'] = $productId;
					$productData['price'] = $data['product_value'][$productId];
					$productData['total'] = $productData['qty'] * $productData['price'];
					$orderProduct = Mage::getModel('inventorymanager/product');
					$existOrderProductColl = Mage::getModel('inventorymanager/product')->getCollection();
					$existOrderProductColl->addFieldToFilter('po_id', $model->getId());
					$existOrderProductColl->addFieldToFilter('main_product_id', $productId);
					if($existOrderProductColl->count() > 0){
						$existOrderProductObject = $existOrderProductColl->getFirstItem();
						$orderProduct->setId($existOrderProductObject->getId());
					}
					$orderProduct->setData($productData);
					$orderProduct->save();
				}
				//if(isset($data['id'])){
					
				//}
				$model->setOrderQty($tatalQty)->save();
				Mage::getModel('inventorymanager/label')->updateLabels($model->getId());
				if($id == "" || $id <= 0){
					//Mage::getModel('inventorymanager/label')->generateLabels($model->getId());
					Mage::helper('inventorymanager')->sendNewOrderEmail($model->getId());
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('inventorymanager')->__('Order was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back') && $this->getRequest()->getParam('back') == 1) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventorymanager')->__('Unable to find order to save'));
        $this->_redirect('*/*/');
	}
	
	public function generatepdfAction(){
		
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$id = $this->getRequest()->getParam('id');
		$purchaseOrder = Mage::getModel('inventorymanager/purchaseorder')->load($id);
		
		/*$content = $this->getLayout()->createBlock('inventorymanager/user_purchaseorder_pdf')
		->setTemplate('inventorymanager/adminuser/purchaseorder/pdf.phtml')->toHtml();*/
		$content = $this->getLayout()->createBlock('inventorymanager/user_purchaseorder_productpdf')
		->setTemplate('inventorymanager/adminuser/purchaseorder/productpdf.phtml')->toHtml();
		//print_r($content); exit;
		
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
		$pdf->Output('purchaseorder_'.$id.'.pdf', 'D');
	}
	public function findproductAction(){
		$this->loadLayout();
       	$this->renderLayout();
	}
	public function getproductinfoAction(){
		$this->loadLayout();
       	$this->renderLayout();
	}
	
	public function showbystatusAction(){
		$this->loadLayout();
       	$this->renderLayout();
	}
	
	public function deleteAction(){
		$id = $this->getRequest()->getParam('id', 0);
		$purchaseorderObject = Mage::getModel('inventorymanager/purchaseorder')->load($id);
		if($purchaseorderObject && $purchaseorderObject->getId()){
			try {
				$purchaseorderObject->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('inventorymanager')->__('Order has been deleted'));
				$this->_redirect('inventorymanager/adminuser_purchaseorder/');
				return $this;
			}catch (Exception $e){
				Mage::log($e);
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('inventorymanager/adminuser_purchaseorder/');
				return $this;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventorymanager')->__("Something went wrong"));
		$this->_redirect('inventorymanager/adminuser_purchaseorder/');
		return $this;
	}
}
