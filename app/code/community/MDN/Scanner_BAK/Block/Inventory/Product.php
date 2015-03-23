<?php

class MDN_Scanner_Block_Inventory_Product extends Mage_Adminhtml_Block_Widget_Form
{
	private $_product = null;
	private $_stocks = null;
	private $_stockMovements = null;
	private $_barcodes = null;
	
	public function getImageUrl()
	{
		$url = '';
		if (($this->getProduct()->getsmall_image() != null) && ($this->getProduct()->getsmall_image() != 'no_selection'))
		{
			$url = mage::helper('catalog/image')->init($this->getProduct(), 'small_image')->resize(50);
		}
		else
		{
			//try to get picture from parent
			$configurableProduct = $this->getConfigurableProduct($this->getProduct());
			if ($configurableProduct)
			{
				if (($configurableProduct->getsmall_image() != null) && ($configurableProduct->getsmall_image() != 'no_selection'))
				{
					$url = mage::helper('catalog/image')->init($configurableProduct, 'small_image')->resize(50);
				}
			}
		}
		return $url;
	}


	public function getProduct()
	{
		if ($this->_product == null)
		{
			$productId = $this->getRequest()->getParam('product_id');
			$this->_product = mage::getModel('catalog/product')->load($productId);
		}
		return $this->_product;
	}
	
	
	public function getStocks()
	{
		if ($this->_stocks == null)
		{
			$this->_stocks = mage::helper('AdvancedStock/Product_Base')->getStocks($this->getProduct()->getId());
		}
		return $this->_stocks;
	}

	public function getStockMovements()
	{
		if ($this->_stockMovements == null)
		{
			$this->_stockMovements = mage::getModel('AdvancedStock/StockMovement')
										->getCollection()
										->addFieldToFilter('sm_product_id', $this->getProduct()->getId())
										->setOrder('sm_date', 'desc');
		}
		return $this->_stockMovements;
	}
	
	public function getBarcodes()
	{
		if ($this->_barcodes == null)
		{
			$this->_barcodes = mage::helper('AdvancedStock/Product_Barcode')->getBarcodesForProduct($this->getProduct()->getId());
		}
		return $this->_barcodes;
	}
	
	public function getProductInformationUrl()
	{
		return $this->getUrl('Scanner/Inventory/ProductInformation');
	}
	
	public function getNewBarcodeUrl()
	{
		return $this->getUrl('Scanner/Inventory/AddBarcode');
	}
	
	public function changeProductLocationUrl()
	{
		return $this->getUrl('Scanner/Inventory/ChangeProductLocation');
	}
	
    private function getConfigurableProduct($product)
    {
		$parentIdArray = mage::helper('AdvancedStock/MagentoVersionCompatibility')->getProductParentIds($product);
    	foreach ($parentIdArray as $parentId)
    	{
    		$parent = mage::getModel('catalog/product')->load($parentId);
    		return $parent;
    	}

    	return null;
    }

    //BOF ARUN
    /**
     *
     * @author ArunV
     * @return JSON object
     * 
     */

    public function arvKeywordTypeArr()
    {
    	$keyword = $this->getRequest()->getParam('keyword');
    	$keyword_type = $this->getRequest()->getParam('keyword_type');

    	return Mage::helper('core')->jsonEncode(array('key' => $keyword, 'type' => $keyword_type));
    }

    public function arvGetSerials()
    {
    	$productId = $this->getRequest()->getParam('product_id');
    	$model = Mage::getModel('barcodes/barcodes')
    				->getCollection()
    				->addFieldToFilter('product_id',$productId);
    				

    	return $model;			


    }

    public function getProductSerialLocationURL()
    {
    	$productId = $this->getRequest()->getParam('product_id');
    	$keyword = $this->getRequest()->getParam('keyword');
    	$keyword_type = $this->getRequest()->getParam('keyword_type');

    	$url = Mage::getUrl('Scanner/Inventory/ChangeProductLocation',array(
    					'stock_id' => $productId,
    					// 'keyword'  => $keyword,
    					'keyword_type' => $keyword_type
    		         ));

    	return $url;
    }

    //EOF Arun
	
}