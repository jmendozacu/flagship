<?php

class MDN_AdvancedStock_MiscController extends Mage_Adminhtml_Controller_Action
{

	
	/**
	 * Display mass stock editor grid
	 *
	 */
	public function MassStockEditorAction()
	{
		$this->loadLayout();
        $this->renderLayout();		
	}
	
	/**
	 * Mass action to validate payment
	 *
	 */
	public function ValidatepaymentAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids');
		if (!empty($orderIds)) 
		{
			foreach ($orderIds as $orderId)
			{
				$order = mage::getModel('sales/order')->load($orderId);
				$order->setpayment_validated(1)->save();
			}
		}
		
		//Confirm & redirect
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payments validated'));	
		$this->_redirect('adminhtml/sales_order/');
	}
		
	/**
	 * Mass action to cancel payment
	 *
	 */
	public function CancelpaymentAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids');
		if (!empty($orderIds)) 
		{
			foreach ($orderIds as $orderId)
			{
				$order = mage::getModel('sales/order')->load($orderId);
				$order->setpayment_validated(0)->save();
			}
		}
				
		//Confirm & redirect
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payments canceled'));	
		$this->_redirect('adminhtml/sales_order/');

	}
	

	
	/**
	 * Change sales order payment (from sales order shee)
	 *
	 */
	public function SavepaymentAction()
	{
		//recupere les infos
		$orderId = $this->getRequest()->getParam('order_id');
		$value = $this->getRequest()->getParam('payment_validated');
		
		//Charge la commande et modifie
		$order = mage::getModel('sales/order')->load($orderId);
		$order->setpayment_validated($value)->save();
		
		//Confirme
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payment state updated'));	

		//redirige
		$this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
	}

	/**
	 * apply mass stock editor changes
	 *
	 */
	public function MassStockSaveAction()
	{
		//collect data
		$stringStock = $this->getRequest()->getPost('stock');
		$stringStockMini = $this->getRequest()->getPost('stockmini');
		
		//process stock
		$t_stock = explode(',', $stringStock);
		foreach($t_stock as $item)
		{
			if ($item != '')
			{
				//retrieve data
				$t = explode('-', $item);
				$stockId = $t[0];
				$qty = $t[1];
				
				//load stockitem and save
				$stockItem = mage::getModel('cataloginventory/stock_item')->load($stockId);
				if ($stockItem->getId())
				{
					if ($stockItem->getqty() != $qty)
						$stockItem->setqty($qty)->save();
				}
			}
		}
		
		//process stock mini
		$t_stockMini = explode(',', $stringStockMini);
		foreach($t_stockMini as $item)
		{
			if ($item != '')
			{
				//retrieve data
				$t = explode('-', $item);
				$stockId = $t[0];
				$qtyMini = $t[1];
				
				//load stockitem and save
				$stockItem = mage::getModel('cataloginventory/stock_item')->load($stockId);
				if ($stockItem->getId())
				{
						$stockItem->setnotify_stock_qty($qtyMini)->setuse_config_notify_stock_qty(0)->save();
				}
			}
		}
	}

	//************************************************************************************************************************************************************
	//************************************************************************************************************************************************************
	//STOCK ERRRORS
	//************************************************************************************************************************************************************
	//************************************************************************************************************************************************************
	
	/**
	 * Display stock error grid
	 *
	 */
	public function IdentifyErrorsAction()
	{
		$this->loadLayout();
        $this->renderLayout();
	}
	
	/**
	 * Refresh stock error list
	 *
	 */
	public function RefreshErrorListAction()
	{
		mage::helper('AdvancedStock/StockError')->refresh();
	}
	
	/**
	 * try to fix error
	 *
	 */
	public function FixErrorAction()
	{
		//retrieve data
		$stockErrorId = $this->getRequest()->getParam('se_id');
		
		try 
		{
			$stockError = mage::getModel('AdvancedStock/StockError')->load($stockErrorId);
			if ($stockError->getId())
				$stockError->fix();	
			else 
				throw new Exception('Unable to find stock !');
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Error fixed'));	
		}
		catch (Exception $ex)
		{
			Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : ').$ex->getMessage());	
		}
		
		//redirect
		$this->_redirect('AdvancedStock/Misc/IdentifyErrors');
	}
	
	/**
	 * Try to fix all errors
	 *
	 */
	public function MassFixErrorsAction()
	{
		mage::helper('AdvancedStock/StockError')->fixAllErrors();
	}
	
	/**
	 * Update is valid for all orders
	 *
	 */
	public function UpdateIsValidForAllOrdersAction()
	{
		$taskGroup = 'refresh_is_valid';
		mage::helper('BackgroundTask')->AddGroup($taskGroup, mage::helper('AdvancedStock')->__('Refresh is_valid for orders'), 'AdvancedStock/Misc/ConfirmUpdateIsValidForAllOrders');
		
		//plan task for each orders
		$collection = mage::getModel('sales/order')
							->getCollection()
							->addAttributeToFilter('state', array('nin' => array('complete', 'canceled')));
		foreach ($collection as $order)
		{
			$orderId = $order->getId();
			mage::helper('BackgroundTask')->AddTask('Update is_valid for order #'.$orderId, 
									'AdvancedStock/Sales_ValidOrders',
									'UpdateIsValidWithSave',
									$orderId,
									$taskGroup
									);	
		}
		
		//execute task group
		mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
	}
	
	public function ConfirmUpdateIsValidForAllOrdersAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}