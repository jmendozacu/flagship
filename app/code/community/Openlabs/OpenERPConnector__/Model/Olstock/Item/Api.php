<?php

/** Distributed under GPL3 by Open Labs Business Solutions
 * If you dont know what that is google it.  
 * If you still cant find it its here:http://www.gnu.org/licenses/gpl-3.0.html 
 * http://www.openlabs.co.in
 */
/**
 * Catalog inventory api extension
 * Python usage: ser.call(
 * 			ses,
 * 			'ol_cataloginventory_stock_item.update',
 * 			[(#USE-TUPLE
 * 				{'product':1,'qty':3,'is_in_stock':1},
 * 				{'product':2,'qty':2,'is_in_stock':1},
 * 			)])
 * @category   Community
 * @author     Sharoon Thomas <sharoon.thomas@openlabs.co.in> Magento Core Team <core@magentocommerce.com>
 */
class Openlabs_OpenERPConnector_Model_Olstock_Item_Api extends Mage_Catalog_Model_Api_Resource {
	public function __construct() {
		$this->_storeIdSessionField = 'product_store_id';
	}

	public function update($datas) {
		$product = Mage :: getModel('catalog/product');
		foreach ($datas as $data) {

			$productId = $data['product'];

			if ($newId = $product->getIdBySku($productId)) {
				$productId = $newId;
			}

			$product->setStoreId($this->_getStoreId())->load($productId);

			if (!$product->getId()) {
				$this->_fault('not_exists');
			}

			if (!$stockData = $product->getStockData()) {
				$stockData = array ();
			}

			if (isset ($data['qty'])) {
				$stockData['qty'] = $data['qty'];
			}

			if (isset ($data['is_in_stock'])) {
				$stockData['is_in_stock'] = $data['is_in_stock'];
			}

			if (isset ($data['manage_stock'])) {
				$stockData['manage_stock'] = $data['manage_stock'];
			}

			if (isset ($data['use_config_manage_stock'])) {
				$stockData['use_config_manage_stock'] = $data['use_config_manage_stock'];
			}

			$product->setStockData($stockData);

			try {
				$product->save();
			} catch (Mage_Core_Exception $e) {
				$this->_fault('not_updated', $e->getMessage());
			}
		}
		return true;
	}
} // Class Mage_CatalogInventory_Model_Stock_Item_Api End