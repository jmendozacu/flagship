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

class MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Serials
	extends MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Abstract 
{
    public function render(Varien_Object $row)
    {
    	
    	$order = mage::getModel('Purchase/Order')->load($row->getPopOrderNum());
    	 $puchase_order = $order->getPoOrderId();
    	 $productId = $row->getPopProductId();
    	 $_collection = Mage::getModel('barcodes/barcodes')->getCollection()
						  ->addFieldToFilter('purchase_order', array('eq' => $puchase_order))
						  ->addFieldToFilter('product_id', array('eq' => $productId))
						  ->getSize();

        return $_collection;
		
    }
    
}