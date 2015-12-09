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

class MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_LinkSerials
	extends MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Abstract 
{
    public function render(Varien_Object $row)
    {
    	
    	 $order = mage::getModel('Purchase/Order')->load($row->getPopOrderNum());
    	 $poId = $order->getPoOrderId();
    	 $pId = $row->getPopProductId();
    	 $_collection = Mage::getModel('barcodes/barcodes')->getCollection()
						  ->addFieldToFilter('purchase_order', array('eq' => $poId))
						  ->addFieldToFilter('product_id', array('eq' => $pId))
						  ->getSize();
        //$url = Mage::getUrl('barcodes/barcodes/index',array('key',f8614d36f14a5a17f323aa90c58695f3))
       if($_collection){
            $text = Mage::helper('purchase')->__('View');
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'barcode_admin/adminhtml_barcodes/index/key/2bc33f4364955de001232913ef8f8c45/poId/'.$poId.'/pId/'.$pId;
            return '<a href="'.$url.'" target="_blank">'.$text.'</a>';
        }
		
    }
    
}