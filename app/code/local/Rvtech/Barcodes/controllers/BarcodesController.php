<?php class Rvtech_Barcodes_BarcodesController extends Mage_Core_Controller_Front_Action
{
   public function indexAction()
   {
     	$this->loadLayout();
      	$this->renderLayout();
   }
   public function mymethodAction()
   {
     echo 'test mymethod';
    }
    public function saveAction()
 	{
	    $purchase = ''.$this->getRequest()->getPost('purchase_order');
	    $date = ''.$this->getRequest()->getPost('date');
	    $factory = ''.$this->getRequest()->getPost('factory');
	    $product = ''.$this->getRequest()->getPost('product');
	    $barcode = ''.$this->getRequest()->getPost('barcode');
	    if(isset($purchase)&&($purchase!='') && isset($date)&&($date!='')
	                               && isset($factory)&&($factory!='') )
		   	{
		      $generate = Mage::getModel('barcodes/barcodes');
		      $generate->setData('purchase_order', $purchase);
		      $generate->setData('date', $date);
		      $generate->setData('factory', $factory);
		      $generate->setData('product', $product);
		      $generate->setData('barcode', $barcode);
		      $generate->save();
		   	}
	   $this->_redirect('barcodes/barcodes/index');
	}
}
?>