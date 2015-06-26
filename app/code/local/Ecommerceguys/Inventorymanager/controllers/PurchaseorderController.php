<?php
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
}