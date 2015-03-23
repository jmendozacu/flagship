<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : ArunV
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Scanner_ShipmentScannerController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
    
     /**
	 * @author ArunV
	 **/
	public function saveShipmentAction()
	{
		//echo "running controller saveShipmentAction...<br>\n";
		$post = $this->getRequest()->getPost();
		//echo "<pre>post: ".print_r($post, true)."</pre>\n";
		$saveBlock = $this->getLayout()->getBlockSingleton('MDN_Scanner_Block_ShipmentScanner_Unshipped');
		//echo "saveBlock class ".get_class($saveBlock)."<br>\n";
		$updated = $saveBlock->saveShippedItems($post);
		//echo "updated is of type ".gettype($updated)." and is $updated<br>\n";
		if(is_numeric($updated)) {
			//die("updated is numeric and is $updated; setting success message");
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Updated $updated serials."));
		} else {
			//die("updated is not numeric and is $updated; setting error message");
			Mage::getSingleton('adminhtml/session')->addError($this->__($updated));
		}
		$this->_redirect('Scanner/ShipmentScanner/index');
	}

	
	
}