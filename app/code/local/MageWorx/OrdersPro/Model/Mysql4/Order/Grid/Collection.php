<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

class MageWorx_OrdersPro_Model_Mysql4_Order_Grid_Collection extends Mage_Sales_Model_Mysql4_Order_Grid_Collection
{        
    
    protected $_setFields = array();    
    
    public function __construct($resource=null) {
        parent::__construct();        
        if (Mage::helper('orderspro')->isEnabled() && $this->getSelect()!==null) {
                        
            
            if (Mage::app()->getRequest()->getControllerName()!='customer') {
            
                $listColumns = Mage::helper('orderspro')->getGridColumns();
                $shellRequestFlag = false;            

                foreach ($listColumns as $column) {

                    switch ($column) {
                        case 'product_names':
                        case 'product_skus':
                        case 'total_refunded':                    
                            $this->setOrderItemTbl();
                            $shellRequestFlag = true;
                        break;

                        case 'customer_email':
                        case 'customer_group':                    
                        case 'tax_amount':
                        //case 'tax_percent':
                        case 'discount_amount':
                        case 'shipping_method':
                        case 'internal_credit':
                        case 'coupon_code':    
                            //$this->setOrderTbl();
                            //$shellRequestFlag = true;
                        break;                

                        case 'payment_method':
                            $this->setFieldPaymentMethod();
                        break;            

                        case 'order_group':
                            $this->setFieldOrderGroup();
                            $shellRequestFlag = true;
                        break;

                        case 'qnty':   
                            //$this->setOrderTbl();
                            //$this->setInvoiceTbl();                                                            
                            $this->setOrderItemTbl();                    
                        case 'shipped':    
                            $this->setShipmentTbl();
                            $shellRequestFlag = true;
                        break;
                        case 'billing_company':
                        case 'billing_street':
                        case 'billing_city':
                        case 'billing_region':   
                        case 'billing_country':                            
                        case 'billing_postcode':
                            
                            $this->setOrderAddressTbl('billing');
                        break;
                        case 'shipping_company':
                        case 'shipping_street':    
                        case 'shipping_city':
                        case 'shipping_region':
                        case 'shipping_country':
                        case 'shipping_postcode':
                            $this->setOrderAddressTbl('shipping');
                        break;                        
                    }
                }

                if ($shellRequestFlag) $this->setShellRequest();
            } else {
                if (Mage::app()->getRequest()->getActionName()!='orders') return $this;                
                $listColumns = Mage::helper('orderspro')->getCustomerGridColumns();
                $shellRequestFlag = false;                                
                
                
                // for enterprise add salesarchive orders
                if (version_compare(Mage::getVersion(), '1.10.0', '>=')) {
                    $cloneSelect = clone $this->getSelect();
                    $union = Mage::getResourceModel('enterprise_salesarchive/order_collection')
                        ->getOrderGridArchiveSelect($cloneSelect);
                    $unionParts = array($cloneSelect, $union);
                    $this->getSelect()->reset()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);
                    $this->setShellRequest();
                }
                
                foreach ($listColumns as $column) {

                    switch ($column) {
                        case 'product_names':
                        case 'product_skus':
                        case 'total_refunded':                    
                            $this->setOrderItemTbl();
                            $shellRequestFlag = true;
                        break;

                        case 'customer_email':
                        case 'customer_group':                    
                        case 'tax_amount':
                        //case 'tax_percent':
                        case 'discount_amount':
                        case 'shipping_method':
                        case 'internal_credit':
                        case 'coupon_code':
                            //$this->setOrderTbl();
                            //$shellRequestFlag = true;
                        break;                

                        case 'payment_method':
                            $this->setFieldPaymentMethod();
                        break;            

                        case 'order_group':
                            $this->setFieldOrderGroup();
                            $shellRequestFlag = true;
                        break;

                        case 'qnty':   
                            //$this->setOrderTbl();
                            //$this->setInvoiceTbl();                                                            
                            $this->setOrderItemTbl();                    
                        case 'shipped':    
                            $this->setShipmentTbl();
                            $shellRequestFlag = true;
                        break;            
                    }
                }
                
                if ($shellRequestFlag) $this->setShellRequest();
                
                
                foreach ($listColumns as $column) {                      
                    switch ($column) {                        
                        case 'status': $this->addFieldToSelect('status'); break;                        
                        case 'product_names': 
                            $this->addFieldToSelect('name');
                            if (Mage::helper('orderspro')->isShowThumbnails()) $this->addFieldToSelect('product_ids');
                        break;        
                        case 'product_skus': $this->addFieldToSelect('skus'); break;
                        case 'total_refunded': $this->addFieldToSelect('total_refunded'); break;
                        case 'customer_email': $this->addFieldToSelect('customer_email'); break;
                        case 'customer_group': $this->addFieldToSelect('customer_group_id'); break;
                        case 'tax_amount': $this->addFieldToSelect('tax_amount'); break;
                        case 'discount_amount': $this->addFieldToSelect('discount_amount'); break;
                        case 'shipping_method': $this->addFieldToSelect('shipping_method'); break;                          
                        case 'payment_method': $this->addFieldToSelect('method'); break;
                        case 'internal_credit': 
                            if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) $this->addFieldToSelect('customer_credit_amount');                            
                            break;
                        case 'order_group': $this->addFieldToSelect('order_group_id'); break;
                        case 'qnty':
                            $this->addFieldToSelect('total_qty_shipped');
                            $this->addFieldToSelect('total_qty_invoiced');
                            $this->addFieldToSelect('total_qty_ordered');
                            $this->addFieldToSelect('total_qty_refunded');
                            break;                              
                        case 'shipped': $this->addFieldToSelect('shipped'); break;
                        case 'coupon_code': $this->addFieldToSelect('coupon_code'); break;
                        case 'billing_company': $this->addFieldToSelect('billing_company'); break;
                        case 'billing_city': $this->addFieldToSelect('billing_city'); break;
                        case 'billing_postcode': $this->addFieldToSelect('billing_postcode'); break;
                        case 'shipping_company': $this->addFieldToSelect('shipping_company'); break;
                        case 'shipping_city': $this->addFieldToSelect('shipping_city'); break;
                        case 'shipping_postcode': $this->addFieldToSelect('shipping_postcode'); break;
                        case 'is_edited': $this->addFieldToSelect('is_edited'); break;
                    }
                }
            }                
        }    
        
    }    

    public function setOrderItemTbl() {                      
        if ($this->getSelect()!==null && !isset($this->_setFields['setOrderItemTbl'])) {
            //$this->getSelect()->columns(array('product_names' =>"(SELECT GROUP_CONCAT(name SEPARATOR '\n') FROM ".$this->getTable('sales/order_item')." WHERE parent_item_id IS NULL AND order_id=main_table.entity_id)"));
            $this->getSelect()->joinLeft(array('order_item_tbl'=>$this->getTable('sales/order_item')),
                    'order_item_tbl.order_id = main_table.entity_id',
                    array(
                        'name' => new Zend_Db_Expr('GROUP_CONCAT(`name` SEPARATOR \'\n\')'),
                        'skus' => new Zend_Db_Expr('GROUP_CONCAT(`sku` SEPARATOR \'\n\')'),
                        'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(`product_id` SEPARATOR \'\n\')'),
                        'total_qty_refunded' => new Zend_Db_Expr('SUM(order_item_tbl.`qty_refunded`)'),
                        'total_qty_invoiced' => new Zend_Db_Expr('SUM(order_item_tbl.`qty_invoiced`)')
                    ))
                    ->where('order_item_tbl.`parent_item_id` IS NULL')
                    ->group('main_table.entity_id');
            
            $this->_setFields['setOrderItemTbl'] = true;
        }      
        return $this;
    }
    
//    public function joinProductThumbnail() {        
//        $connection = $this->getConnection('core_read');
//        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();                
//
//        $attributeId = $connection->fetchOne("SELECT `attribute_id` FROM `".$tablePrefix."eav_attribute` WHERE `attribute_code` = 'thumbnail' AND `frontend_input` = 'media_image'");
//        if (!$attributeId) return $this;
//        $this->getSelect()->joinLeft(array('catalog_product_entity_tbl'=>$tablePrefix.'catalog_product_entity_varchar'),
//                'catalog_product_entity_tbl.entity_id = order_item_tbl.`product_id` AND catalog_product_entity_tbl.`attribute_id` = '.$attributeId. ' AND catalog_product_entity_tbl.`store_id`=0',
//                array('thumbnail' => new Zend_Db_Expr('GROUP_CONCAT(catalog_product_entity_tbl.`value` SEPARATOR \'\n\')')));
//        return $this;
//    }
    
    public function setOrderAddressTbl($addressType='billing') {                      
        if ($this->getSelect()!==null  && !isset($this->_setFields['setOrderAddressTbl'.$addressType])) {            
            $this->getSelect()->joinLeft(array('order_address_'.$addressType.'_tbl'=>$this->getTable('sales/order_address')),
                        'order_address_'.$addressType.'_tbl.parent_id = main_table.entity_id',
                        array($addressType.'_company' => 'company', $addressType.'_street' => 'street', $addressType.'_city' => 'city', $addressType.'_region' => 'region', $addressType.'_country_id' => 'country_id', $addressType.'_postcode' => 'postcode')
                    )
                    ->where('order_address_'.$addressType.'_tbl.`address_type` = "'.$addressType.'"')
                    ->group('main_table.entity_id');
            $this->_setFields['setOrderAddressTbl'.$addressType] = true;
        }      
        return $this;
    }
    
    
    public function setFieldPaymentMethod() {              
        if ($this->getSelect()!==null) {
            $this->getSelect()->joinLeft(array('order_payment_tbl'=>$this->getTable('sales/order_payment')),
                    'order_payment_tbl.parent_id = main_table.entity_id',                    
                    'method');
        }
        return $this;
    }
    
    public function setFieldOrderGroup() {              
        if ($this->getSelect()!==null) {
            $this->getSelect()->joinLeft(array('order_item_group_tbl'=>$this->getTable('orderspro/order_item_group')),
                    'order_item_group_tbl.order_id = main_table.entity_id',                    
                    array('order_group_id' => new Zend_Db_Expr('IFNULL(order_item_group_tbl.`order_group_id`, 0)'))
            );            
        }
        return $this;
    }
    
//    public function setInvoiceTbl() {              
//        if ($this->getSelect()!==null) {
//            $this->getSelect()->joinLeft(array('invoice_tbl'=>$this->getTable('sales/invoice')),
//                    'invoice_tbl.order_id = main_table.entity_id',                    
//                    array('total_qty_invoiced' => new Zend_Db_Expr('IFNULL(invoice_tbl.`total_qty`, 0)'))
//            );
//        }
//        return $this;
//    }
    
    public function setShipmentTbl()
    {              
        if ($this->getSelect()!==null && !isset($this->_setFields['setShipmentTbl'])) {
            $this->getSelect()->joinLeft(array('shipment_tbl'=>$this->getTable('sales/shipment')),
                    'shipment_tbl.order_id = main_table.entity_id',                    
                    array (
                        'shipped' => new Zend_Db_Expr('(IF(IFNULL(shipment_tbl.`entity_id`, 0)>0, 1, 0))'),
                        'total_qty_shipped' => new Zend_Db_Expr('IFNULL(shipment_tbl.`total_qty`, 0)')
                    )                    
            )->group('main_table.entity_id');
            $this->_setFields['setShipmentTbl'] = true;
        }                        
        return $this;
    }        
    
    public function setShellRequest() {              
        if ($this->getSelect()!==null) {            
            $sql = $this->getSelect()->assemble();
            $this->getSelect()->reset()->from(array('main_table' => new Zend_Db_Expr('('.$sql.')')), '*');
            //echo $this->getSelect()->assemble(); exit;
        }                        
        return $this;
    }            
    
    
}
