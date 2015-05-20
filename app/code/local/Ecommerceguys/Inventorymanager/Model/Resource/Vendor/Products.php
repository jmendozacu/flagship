<?php
class Ecommerceguys_Inventorymanager_Model_Resource_Vendor_Product extends Mage_Core_Model_Resource_Db_Abstract
{
	protected $_writeConn;
	protected $_vendorProductTable;
	
	
	public function _construct(){
		parent::_construct();
		$this->_writeConn = Mage::getSingleton('core/resource')->getConnection('core_write');
		$this->_vendorProductTable = Mage::getSingleton('core/resource')->getTableName('inventorymanager_vendorproduct');
	}
	
	public function delete($id){
		if(is_array($id)){
			$where = 'product_id in ('.implode(",", $id) . ')';
		}else{
			$where = 'product_id = ' .$id ;
		}
		$this->_writeConn->delete($this->_vendorProductTable, $where);
	}
	
	public function insertOne($insertVar){
		$this->_writeConn->insert($this->_vendorProductTable, $insertVar);
	}
	
	public function insertMulti($insertVars){
		$this->_writeConn->insertMultiple($this->_vendorProductTable, $insertVar);
	}
}