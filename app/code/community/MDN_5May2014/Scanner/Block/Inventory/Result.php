<?php

class MDN_Scanner_Block_Inventory_Result extends Mage_Adminhtml_Block_Widget_Form {

	private $_collection = null;
	private $_product = null;
	private $_productId = null;
	private $_keyword = null;

	//BOF ArunV
	private $_arunSearchMulti = array();
	private $_keywordIs = null;
	const KEYWORD_IS_BARCODE = 'barcode'; // ERP barcode
	const KEYWORD_IS_SERIAL = 'serial'; // ERP serial
	const KEYWORD_IS_UPC = 'UPC'; // Magento custom UPC
	const KEYWORD_IS_RVTECH = 'rvserial'; // Rvtech serial
	//EOF ArunV

	public function getQueryString() {
		return $this->_keyword;
	}

	/**
	 * Return the Query keyword type
	 *
	 * @author ArunV
	 */	
	public function getKeywordType() 
	{
		return $this->_keywordIs;
	}


	/**
	 * Initialise la collection
	 *
	 * @param unknown_type $keyword
	 */
	public function initResult($keyword) {
		$this->_keyword = $keyword;
		
		// check Rvtech serial, UPC, or ERP barcode
		if($this->queryRvtechSerial() || $this->queryUPC() || $this->queryAdvBarcode()) {
			return $this->_productId;
		}
	}

	/**
	 * Chcek results for AdvancedStock Barcode 
	 *
	 * @return boolean
	 * @author ArunV
	 */
	public function queryAdvBarcode() {
		if ($this->_keyword) {
			// Check for Single product
			$object = Mage::getModel('AdvancedStock/ProductBarcode');
			$barcode = $object->load(trim($this->_keyword), 'ppb_barcode');

			if ($barcode->getData('ppb_product_id')) {
				$this->_productId = $barcode->getData('ppb_product_id');
				//$this->_product = Mage::getModel('catalog/product')->load($barcode->getData('ppb_product_id'));
				$this->_keywordIs = self::KEYWORD_IS_BARCODE;		
				return true;
			}
		}
	}

	/**
	 * Chcek results for AdvancedStock Serials 
	 *
	 * @return boolean
	 * @author ArunV
	 */
	public function queryAdvSerials() {
		if ($this->_keyword) {
			// Check for Single product
			$object = Mage::getModel('AdvancedStock/ProductSerial');
			$serial = $object->load(trim($this->_keyword), 'pps_serial');

			if ($serial->getData('pps_product_id')) {
				$this->_productId = $serial->getData('pps_product_id');
				//$this->_product = Mage::getModel('catalog/product')->load($serial->getData('pps_product_id'));
				$this->_keywordIs = self::KEYWORD_IS_SERIAL;
				return true;
			}
		}
	}

	/**
	 * Chcek results for UPC 
	 *
	 * @return boolean
	 * @author ArunV
	 */
	public function queryUPC() {
		if ($this->_keyword) {
			// Check for Single product
			$UPCollection = Mage::getModel('catalog/product')
					->getCollection();

			$UPCFirst = $UPCollection->addFieldToFilter('upc', array('eq' => trim($this->_keyword)))
					->getFirstItem();
			
			if ($UPCFirst->getData('entity_id')) {
				$this->_productId = $UPCFirst->getData('entity_id');
				$this->_product = $UPCFirst;
				$this->_keywordIs = self::KEYWORD_IS_UPC;
				return true;
			}
		}
	}

	/**
	 * Chcek results for Barcode 
	 *
	 * @return boolean
	 * @author ArunV
	 */
	public function queryRvtechSerial() {
		if ($this->_keyword) {
			// Check for Single product
			$object = Mage::getModel('barcodes/barcodes');
			$barcode = $object->getCollection()
					->addFieldToFilter('dzv_serial', array('eq' => trim($this->_keyword)))
					//->join('Purchase/OrderProduct', 'pop_product_id=product_id')
					->getFirstItem()
			;
			
			if ($barcode->getProductId()) {
				$this->_productId = $barcode->getProductId();
				//$this->_product = Mage::getModel('catalog/product')->load($barcode->getData('product_id'));
				$this->_keywordIs = self::KEYWORD_IS_RVTECH;		
				return true;
			}
		}
	}

	/**
	 * D�finit si un seul produit correspond
	 *
	 * @return unknown
	 */
	public function hasOnlyOneResult() {
		if ($this->_product != null || !empty($this->_productId))
			return true;
		else {
			return (count($this->getCollection()) == 1);
		}
	}

	/**
	 * retourne tous les r�sultats
	 *
	 * @return unknown
	 */
	public function getCollection() {
		return $this->_collection;
	}

	/**
	 * retourne le seul produit
	 *
	 * @return unknown
	 */
	public function getOnlyProduct() {
		if ($this->_product != null)
			return $this->_product;
		else {
			foreach ($this->getCollection() as $item)
				return $item;
		}
	}
	
	public function getProductId() {
		if(!empty($this->_productId)) {
			return $this->_productId;
		}
	}

}
