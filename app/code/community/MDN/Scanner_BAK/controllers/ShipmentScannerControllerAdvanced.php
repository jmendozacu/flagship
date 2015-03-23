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
		//echo "running saveShipmentAction...<br>\n";
		$post = $this->getRequest()->getPost();
		//echo "<pre>post: ".print_r($post, true)."</pre>\n";
		$saveBlock = $this->getLayout()->getBlockSingleton('MDN_Scanner_Block_ShipmentScanner_Unshipped');
		//die("saveBlock class ".get_class($saveBlock));
		$result = $saveBlock->saveShippedItems($post);
		if($result){
			//echo "shipped qty and serials location updated.";
		}
	}

	
	
}