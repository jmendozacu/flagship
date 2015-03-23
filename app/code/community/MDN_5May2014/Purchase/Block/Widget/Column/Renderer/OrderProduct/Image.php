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

class MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Image
	extends MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Abstract 
{
    public function render(Varien_Object $row)
    {
		$html = '';
		if ($row->getsmall_image() != 'no_selection') 
		{
			$productImage = Mage::getBaseUrl('media') . 'catalog/product' . $row->getsmall_image();
			//$url = $this->getUrl('AdvancedStock/Products/Edit', array('product_id' => $row->getpop_product_id()));
			//$html = '<a href="'.$url.'"><img src="' . $productImage . '" width="50" height="50"></a>';
			$html = '<img src="' . $productImage . '" width="50" height="50">';
		}
		return $html;
    }
    
}