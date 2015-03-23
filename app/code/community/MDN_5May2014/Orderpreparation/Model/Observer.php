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
class MDN_Orderpreparation_Model_Observer 
{
	
	/**
	 * Catch event sales order after place to dispatch order
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function sales_order_afterPlace(Varien_Event_Observer $observer)
    {
    	$order = $observer->getEvent()->getOrder(); 
    	if ($order && $order->getId())
    	{
    		//plan dispatch order
			mage::helper('BackgroundTask')->AddTask('Dispatch order #'.$order->getId(), 
						'Orderpreparation',
						'dispatchOrder',
						$order->getId(),
						null,
						true
						);	
    	}
    }
    
}
