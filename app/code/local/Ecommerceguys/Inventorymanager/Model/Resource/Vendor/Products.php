<?php
class Ecommerceguys_Inventorymanager_Model_Resource_Vendor_Products extends Mage_Catalog_Model_Resource_Abstract
{
	protected $_writeConn;
	protected $_vendorProductTable;
	
	
	public function _construct(){
		parent::_construct();
		$this->_writeConn = Mage::getSingleton('core/resource')->getConnection('core_write');
		$this->_vendorProductTable = Mage::getSingleton('core/resource')->getTableName('inventorymanager_vendorproduct');
	}
	
	public function remove($id,$field = ""){
		if($field == ""){
			$field = "vendor_id";
		}
		if(is_array($id)){
			$where = "$field in (".implode(",", $id) . ")";
		}else{
			$where = "$field = " .$id ;
		}
		try{
			$this->_writeConn->delete($this->_vendorProductTable, $where);
		}catch (Exception $e){
			
		}
	}
	
	public function insertOne($insertVar){
		try{
			$this->_writeConn->insert($this->_vendorProductTable, $insertVar);
		}catch (Exception $e){
			
		}
	}
	
	public function insertMulti($insertVars){
		try{
			$this->_writeConn->insertMultiple($this->_vendorProductTable, $insertVar);
		}catch (Exception $e){
			
		}
	}
	
	public function getRecords($vendorId){
		try {
		$select = $this->_writeConn->select()
                ->from($this->_vendorProductTable)
                ->where("vendor_id = $vendorId");
        return $this->_writeConn->fetchAll($select);
		}catch (Exception $e){
			
		}
	}
}