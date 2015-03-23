<?php

class Rvtech_Barcodes_Adminhtml_BarcodesController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('barcodes/set_time')
				->_addBreadcrumb('Barcode Manager', 'Barcode Manager');
		return $this;
	}

	public function indexAction() {
		$this->_initAction();
		$this->renderLayout();
		$handle = Mage::getSingleton('core/layout')->getUpdate()->getHandles();

	}

	public function editAction() {
		$barcodeId = $this->getRequest()->getParam('id');
		$barcodeModel = Mage::getModel('barcodes/barcodes')->load($barcodeId);
		$product_id = $barcodeModel->getData('product_id');
		$upc = Mage::getModel('catalog/product')
					->load($product_id)
					->getData('upc');
		$barcodeModel->setData('upc',$upc);			
		if ($barcodeModel->getId() || $barcodeId == 0) {
			Mage::register('barcodes_data', $barcodeModel);
			$this->loadLayout();
			$this->_setActiveMenu('barcodes/set_time');
			$this->_addBreadcrumb('Barcode Manager', 'Barcode Manager');
			$this->_addBreadcrumb('Barcode Description', 'Barcode Description');
			$this->getLayout()->getBlock('head')
					->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()
							->createBlock('barcodes/adminhtml_barcodes_edit'))
					->_addLeft($this->getLayout()
							->createBlock('barcodes/adminhtml_barcodes_edit_tabs')
			);
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')
					->addError('Barcode does not exist');
			$this->_redirect('*/*/');
		}
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {
		if ($this->getRequest()->getPost()) {
			try {
				$postData = $this->getRequest()->getPost();
				
				$barcodeModel = Mage::getModel('barcodes/barcodes');

				$id = $this->getRequest()->getParam('id');
				
				$invoice = $this->getRequest()->getParam('purchase_order');
				$date = date('Y-m-d', strtotime($this->getRequest()->getParam('date')));

				$factory_id = $this->getRequest()->getParam('factory_id');
				if ($factory_id == 22) { // Seng
					$FC = 'SN';
				} elseif ($factory_id == 78) { // Ohio
					$FC = 'OH';
				} elseif ($factory_id == 21) { // Jilu
					$FC = 'JL';
				} elseif ($factory_id == 20) { // Feishida
					$FC = 'FS';
				}

				$dzvserial = $this->getRequest()->getParam('dzv_serial');
				$product_id = $this->getRequest()->getParam('product_id');
				$quantity = $this->getRequest()->getParam('quantity');
				echo "quantity = $quantity<br>\n";
				if (empty($quantity) || !is_numeric($quantity) || $quantity < 1) {
					echo "quantity is either empty or not numeric or less than 1; changing to 1<br>\n";
					$quantity = 1;
				}
				$location = $this->getRequest()->getParam('location');
				echo "location = $location<br>\n";
				// select invoice serial count
				// TODO: We need to run this query: 
				// SELECT MAX(dzv_serial) AS max_serial FROM barcodes WHERE purchase_order = '$invoice';
				// Then split the #### from the serial and increment it.
				$max_serialRow = Mage::getModel('barcodes/barcodes')->getCollection()
						//->addFieldToFilter('purchase_order', array('eq' => $invoice))
						->addFieldToFilter('factory_id', array('eq' => $factory_id))
						->addFieldToFilter('date', array('eq' => $date))
						->addExpressionFieldToSelect('max_serial', 'MAX({{dzv_serial}})', 'dzv_serial')
						->getFirstItem();
				$max_serial_arr = explode('-', $max_serialRow->getMaxSerial());
				
				$serialCount = $max_serial_arr[1];
				echo "serial count = $serialCount<br>\n";
				if (empty($serialCount) || !is_numeric($serialCount) || $serialCount < 0) {
					echo "serial count is either empty or not numberic or less than 0; setting to 0<br>\n";
					$serialCount = 0;
				}
				if (!$id) {
					for ($i = 0; $i < $quantity; $i++) {
						$serialCount++;
						$sequence = sprintf("%04s", $serialCount);
						$dzvserial = $FC . (str_replace('-', '', $date)) . '-' . $sequence;
						$barcodeModel
							//->addData($postData)
							->setPurchaseOrder($invoice)
							->setDate($date)
							->setFactoryId($factory_id)
							->setProductId($product_id)
							->setDzvSerial($dzvserial)
							//->setLocation($location)
							//->setFactorySerial($this->getRequest()->getParam('factory_serial'))
						;
						$barcodeModel->save();
						$barcodeModel->unsetData();
					}
				} else {
					$barcodeModel->load($id);
					
					// very dangerous!!! $barcodeModel->addData($postData);
					// only save things that were changed
					if($invoice != $barcodeModel->getPurchaseOrder()) {
						$barcodeModel->setPurchaseOrder($invoice);
					}
					if($product_id != $barcodeModel->getProductId()) {
						$barcodeModel->setProductId($product_id);
					}
					if($location != $barcodeModel->getLocation()) {
						$barcodeModel->setLocation($location);
					}
					// generate new serial if factory or date has changed
					if ($factory_id != $barcodeModel->getFactoryId() || $date != $barcodeModel->getDate()) {
						$barcodeModel->setDate($date);
						$barcodeModel->setFactoryId($factory_id);
						$sequence = sprintf("%04s", $serialCount);
						$dzvserial = $FC . (str_replace('-', '', $date)) . '-' . $sequence;
						$barcodeModel->setDzvSerial($dzvserial);
					}
					
					$barcodeModel->save();
				}
				
				Mage::getSingleton('adminhtml/session')
						->addSuccess('Successfully saved');
				Mage::getSingleton('adminhtml/session')
						->settestData(false);
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')
						->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')
						->settestData($this->getRequest()
								->getPost()
				);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()
							->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}

	/**
	 * Export customer grid to CSV format
	 */
	public function exportCsvAction() {
		$date = date("Y-m-d-H-i-s", Mage::getModel('core/date')->timestamp(time()));
		$fileName = 'serials.csv';
		//$fileName = 'serials.csv';
		$content = $this->getLayout()->createBlock('barcodes/adminhtml_barcodes_grid')
				->getCsvFile();
		
		$this->_prepareDownloadResponse($fileName, $content);
	}

	/**
	 * Export customer grid to XML format
	 */
	public function exportXmlAction() {
		$date = date("Y-m-d-H-i-s", Mage::getModel('core/date')->timestamp(time()));
		$fileName = 'serials.xml';
		$content = $this->getLayout()->createBlock('barcodes/adminhtml_barcodes_grid')
				->getExcelFile();

		$this->_prepareDownloadResponse($fileName, $content);
	}

	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$barcodeModel = Mage::getModel('barcodes/barcodes');
				$barcodeModel->setId($this->getRequest()
								->getParam('id'))
						->delete();
				Mage::getSingleton('adminhtml/session')
						->addSuccess('Successfully deleted');
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')
						->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction()
{
    $serialIds = $this->getRequest()->getParam('id');
 
    if(!is_array($serialIds)) {
        Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Serial(s).'));
    } else {
	        try {
	            $serial = Mage::getSingleton('barcodes/barcodes');
	            foreach ($serialIds as $serialId) {
	                $serial->load($serialId)->delete();
	            }
	            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of '.count($serialIds).' record(s) were deleted.', count($adListingIds)));
	        } catch (Exception $e) {
	            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
	        }
    }
    $this->_redirect('*/*/');
}

	// AJAX fetch product ID by UPC
	public function getProductIDByUPCAction() {
		$this->getResponse()
				->clearHeaders()
				->setHeader('Content-Type', 'text/xml');

		$product_id = '';
		$upc = $this->getRequest()->getParam('id');

		if (is_numeric($upc)) {
			// select product by UPC
			// if product exists, return ID
			$product_id = '';
		}

		$this->getResponse()->setBody($product_id);
	}

	public function gridAction()
     {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('barcodes/adminhtml_barcodes_grid')->toHtml()
        );
     }

}

?>