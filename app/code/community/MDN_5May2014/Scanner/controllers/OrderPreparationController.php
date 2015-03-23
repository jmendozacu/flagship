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
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Scanner_OrderPreparationController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Picking
	 *
	 */

	protected $_location = 'DOCK';

	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function setPickingList($product_ids = array())
	{
		if(empty($product_ids))
		{
			return;
		}	

		$ordertoprepareitemColl = Mage::getModel('Orderpreparation/ordertoprepareitem')
                        				->getCollection()
                        				->addFieldToFilter('product_id',$product_ids);

		foreach ($ordertoprepareitemColl as $item) 
		{
			$item->setDisplayInPickingList(0);
		}

		$ordertoprepareitemColl->save();
	}

	public function savePickingAction()
	{

		$postData = $this->getRequest()->getParams();		
		$product_ids = array();
		foreach ($postData as $key => $value) 
		{
			if(strpos($key,'product') !== false)
			{
				$id = explode('product_', $key);				
				$product_ids[] = $id[1];
			}	
		}


		if(!empty($product_ids))
		{

			$this->setPickingList($product_ids);

			$rvProducts = Mage::getSingleton('barcodes/barcodes')->getCollection()
	                                    ->addFieldToFilter('product_id',array('in' => $product_ids));
			
			foreach ($rvProducts as $row)
			{
				$row->setLocation($this->_location);
			}

			try
			{
				$rvProducts->save();
				$this->_redirect('Scanner/OrderPreparation/index');
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
		}	


		// TODO: the DOCK location should be saved to the Serials module serial
		echo 'After setting the display_in_picking_list = 0, the location DOCK should be saved to the Serials module location.';
	}
}