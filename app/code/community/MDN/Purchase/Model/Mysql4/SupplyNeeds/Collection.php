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
/**
 * Collection de quotation
 *
 */
class MDN_Purchase_Model_Mysql4_SupplyNeeds_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('Purchase/SupplyNeeds');
    }
    
	/**
	 * Return ids for suppliers used in supply needs
	 */
	public function getSupplierIds()
	{
		//todo : improve the way we get list... when supply needs table structure will be changed
		$this->getSelect()
			->reset()
			->from($this->getMainTable(), 'group_concat(sn_suppliers_ids, \'0\')')
			;
		$value = $this->getConnection()->fetchOne($this->getSelect());
		$value = str_replace(',,', ',', $value);
		$value = str_replace(', ,', ',', $value);
		$value = trim($value, ',');
		$value = explode(',', $value);
		
		$value = array_unique($value);
		
		return $value;
	}
	
	/**
	 * Return amount for one supplier / one status
	 */
	public function getAmount($supplierId, $supplyNeedStatus)
	{
		$productSupplierTable = Mage::getSingleton('core/resource')->getTableName('Purchase/ProductSupplier');
		$this->getSelect()
			->reset()
			->from($this->getMainTable(), 'sum(sn_needed_qty * pps_last_unit_price)')
			->from($productSupplierTable, '')
			->where('pps_product_id = sn_product_id')
			->where('pps_supplier_num = '.$supplierId)
			->where('sn_status = \''.$supplyNeedStatus.'\'')
			;
		
		
		$value = $this->getConnection()->fetchOne($this->getSelect());
		return number_format($value, 0);
	}

}