<?php
require_once(Mage::getBaseDir().'/tcpdf/tcpdf.php');
class Ecommerceguys_Inventorymanager_Vendor_EmployerController extends Mage_Core_Controller_Front_Action
{
	
	public function preDispatch(){

		parent::preDispatch();
		if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/vendor/login');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        if($this->_getSession()->isEmployer()){
        	$this->_redirect('inventorymanager/vendor');
        	return;
        }

	}
	protected function _getSession()
    {
        return Mage::getSingleton('inventorymanager/session');
    }

	public function indexAction(){
	   if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/login');
            return;
        }
     
		$this->loadLayout();
		$this->renderLayout();
	}

	public function newAction() {
	   if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/login');
            return;
        }
     
		$this->_forward('edit');
	}

	public function editAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	public function saveAction() {
		
		
		if ($data = $this->getRequest()->getPost()) {
			$vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
			$data['parent_id'] = $vendorId;
			$model = Mage::getModel('inventorymanager/vendor_employee');
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			if(!$model->validateUsername()){
				Mage::getSingleton('core/session')->addError("Please use different username");
				Mage::getSingleton('core/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
			}
				
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now());
				}	
			
				$model->save();
				
				
				if(isset($_FILES['logo']) && $_FILES['logo']['name'] != ""){
					try {
						$uploader = new Varien_File_Uploader('logo');
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$path = Mage::getBaseDir('media') . DS . "inventorymanager". DS ."employee" . DS ;
						$uploader->save($path,$model->getId() . "_" . $_FILES['logo']['name'] );
					} catch (Exception $e) {
			      
			        }
		  			$model->setLogo($uploader->getUploadedFileName());
				}
				
				if(isset($_FILES['photo']) && $_FILES['photo']['name'] != ""){
					try {
						$uploader = new Varien_File_Uploader('photo');
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$path = Mage::getBaseDir('media') . DS . "inventorymanager". DS ."employee" . DS ;
						$uploader->save($path, $model->getId() . "_" . $_FILES['photo']['name'] );
					} catch (Exception $e) {
			      
			        }
		  			$model->setPhoto($uploader->getUploadedFileName());
				}
				if(isset($data['remove_photo']) && $data['remove_photo'] == 1){
					$model->setPhoto('');
				}
				if(isset($data['remove_logo']) && $data['remove_logo'] == 1){
					$model->setLogo('');
				}
				
				try {
					$model->save();
				}
				catch (Exception $e){
					
				}
				
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__('Employee saved'));

				$this->_redirect('inventorymanager/vendor_employer');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::getSingleton('core/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__('Unable to find vendor to save'));
        $this->_redirect('inventorymanager/vendor_employer');
	}

		public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('inventorymanager/vendor_employee');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('adminhtml')->__('Employer was successfully deleted'));
				$this->_redirect('inventorymanager/vendor_employer');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('core/session')->addError($e->getMessage());
				$this->_redirect('inventorymanager/vendor_employer');
				return;
			}
		}
		$this->_redirect('inventorymanager/vendor_employer');
	}

	public function generatebadgeAction(){
		$id = $this->getRequest()->getParam('id');
		$employee = Mage::getModel('inventorymanager/vendor_employee')->load($id);
		if($employee && $employee->getId()){
			$output = $this->getLayout()->createBlock('inventorymanager/vendor_employer_badge')->setTemplate('inventorymanager/vendor/employer/badge.phtml')->toHtml();
		}
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
		
		$pdf->writeHTML($output, true, false, false, false, '');
		$pdf->lastPage();

		
		$pdf->Output($id.'_employee_badge.pdf', 'D');
	}

}