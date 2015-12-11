<?php

/**
 * Block pour l'index de la page de préparation de commandes
 *
 */
class MDN_OrderPreparation_Block_Header extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        
        $this->setTemplate('Orderpreparation/Header.phtml');
    }
    
    /**
     * return button list
     *
     */
    public function getButtons()
    {
    	
    	$retour = array();
    	
    	//select orders
    	$item = array();
    	$item['position'] = count($retour) + 1;
    	$item['onclick'] = "document.location.href='".Mage::helper('adminhtml')->getUrl('OrderPreparation/OrderPreparation')."'";
    	$item['caption'] = $this->__('Select orders');
    	$retour['select_orders'] = $item;
    	
    	//print (or download) picking list
    	if (mage::getStoreConfig('orderpreparation/order_preparation_step/print_method') == 'download')
    	{
	    	$item = array();
	    	$item['position'] = count($retour) + 1;
	    	$item['onclick'] = "document.location.href='".Mage::helper('adminhtml')->getUrl('OrderPreparation/OnePagePreparation/DownloadPickingList')."'";
	    	$item['caption'] = $this->__('Picking list');
	    	$retour['download_picking_list'] = $item;
    	}
    	else
    	{
	    	$item = array();
	    	$item['position'] = count($retour) + 1;
			$confirmMsg = $this->__('Picking list sent to printer');
	    	$item['onclick'] = "ajaxCall('".Mage::helper('adminhtml')->getUrl('OrderPreparation/OnePagePreparation/PrintPickingList')."', '".$confirmMsg."')";
	    	$item['caption'] = $this->__('Picking list');
	    	$retour['print_picking_list'] = $item;
    	}
    	    	        	
    	//Create shipments & invoices
    	if (mage::getStoreConfig('orderpreparation/order_preparation_step/mode') == 'mass')
    	{
	    	$item = array();
	    	$item['position'] = count($retour) + 1;
	    	$item['onclick'] = "document.location.href='".Mage::helper('adminhtml')->getUrl('OrderPreparation/OrderPreparation/Commit')."'";
	    	$item['caption'] = $this->__('Create shipments/invoices');
	    	$retour['create_objects'] = $item;
    	}
    		    	
    	//Download documents
    	if (mage::getStoreConfig('orderpreparation/order_preparation_step/mode') == 'mass')
    	{
    		if (mage::getStoreConfig('orderpreparation/order_preparation_step/print_method') == 'download')
    		{
		    	$item = array();
		    	$item['position'] = count($retour) + 1;
		    	$item['onclick'] = "document.location.href='".Mage::helper('adminhtml')->getUrl('OrderPreparation/OrderPreparation/DownloadDocuments')."'";
		    	$item['caption'] = $this->__('Download documents');
		    	$retour['download_documents'] = $item;
    		}
    		else 
    		{
		    	$item = array();
		    	$item['position'] = count($retour) + 1;
				$confirmMsg = $this->__('Documents sent to printer');
		    	$item['onclick'] = "ajaxCall('".Mage::helper('adminhtml')->getUrl('OrderPreparation/OrderPreparation/PrintDocuments')."', '".$confirmMsg."')";
		    	$item['caption'] = $this->__('Print documents');
		    	$retour['print_documents'] = $item;
    		}
    	}
    	
    	//process orders
    	if (mage::getStoreConfig('orderpreparation/order_preparation_step/mode') == 'order_by_order')
    	{
	    	$item = array();
	    	$item['position'] = count($retour) + 1;
	    	$item['onclick'] = "document.location.href='".Mage::helper('adminhtml')->getUrl('OrderPreparation/OnePagePreparation')."'";
	    	$item['caption'] = $this->__('Process orders');
	    	$retour['process_orders'] = $item;
    	}
    	    	    	
    	//Import trackings
    	if (mage::getStoreConfig('orderpreparation/order_preparation_step/mode') == 'mass')
    	{
	    	$item = array();
	    	$item['position'] = count($retour) + 1;
	    	$item['onclick'] = "document.location.href='".Mage::helper('adminhtml')->getUrl('OrderPreparation/CarrierTemplate/ImportTracking')."'";
	    	$item['caption'] = $this->__('Shipping label / Trackings');
	    	$retour['shipping_label_trackings'] = $item;
    	}
    	    	
    	//Notify customers
    	$item = array();
    	$item['position'] = count($retour) + 1;
		$confirmMsg = $this->__('Customers notified');
    	$item['onclick'] = "ajaxCall('".Mage::helper('adminhtml')->getUrl('OrderPreparation/OrderPreparation/NotifyCustomers')."', '".$confirmMsg."')";
    	$item['caption'] = $this->__('Notify');
    	$retour['notify_customers'] = $item;
    	    	
    	//Finish
    	$item = array();
    	$item['position'] = count($retour) + 1;
    	$item['onclick'] = "document.location.href='".Mage::helper('adminhtml')->getUrl('OrderPreparation/OrderPreparation/Finish')."'";
    	$item['caption'] = $this->__('Finish');
    	$retour['finish'] = $item;
    	
    	return $retour;
    }
    

}