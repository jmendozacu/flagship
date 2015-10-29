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
class MDN_Purchase_Block_Order_Edit_Tabs_ProductsGrid extends Mage_Adminhtml_Block_Widget_Grid {

    private $_order = null;

    /**
     * Set purchase order
     *
     */
    public function setOrderId($value) {
        $this->_order = mage::getModel('Purchase/Order')->load($value);
        return $this;
    }

    /**
     * Get purchase order
     *
     */
    public function getOrder() {
        return $this->_order;
    }

    public function __construct() {
        parent::__construct();
        $this->setId('ProductsGrid');
        $this->setUseAjax(true);
        $this->setEmptyText($this->__('No items'));
		$this->setDefaultSort('sku');
		$this->setDefaultDir('asc');
    }

    /**
     * 
     *
     * @return unknown
     */
    protected function _prepareCollection() {
        $collection = $this->getOrder()->getProducts();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /*protected function _prepareMassaction()
   {
      $this->setMassactionIdField('serial_id');
      $this->getMassactionBlock()->setFormFieldName('id');
      $this->getMassactionBlock()->addItem('generate', array(
      'label'=> 'Generate Serials',*/
      //'url'  => $this->getUrl('*/*/generateSerials', array('' => '')),
      /*));
      return $this;
   }*/

    /**
     * 
     *
     * @return unknown
     */
    protected function _prepareColumns() {
        //get currency code
        $orderCurrencyCode = $this->getOrder()->getCurrency()->getCode();
        $baseCurrencyCode = Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);

        if (mage::getStoreConfig('purchase/purchase_product_grid/display_product_picture')) {
            $this->addColumn('picture', array(
                'header' => '',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Image',
                'align' => 'center',
                'filter' => false,
                'sortable' => false
            ));
        }

        $this->addColumn('sn_details', array(
            'header' => Mage::helper('purchase')->__('Details'),
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_SupplyNeedsDetails',
            'align' => 'center',
            'filter' => false,
            'sortable' => false,
            'product_id_field_name' => 'pop_product_id',
            'product_name_field_name' => 'pop_product_name'
        ));


        $this->addColumn('sku', array(
            'header' => Mage::helper('purchase')->__('Sku'),
            'index' => 'sku'
        ));

        $this->addColumn('pop_product_name', array(
            'header' => Mage::helper('purchase')->__('Product'),
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Name',
            'index' => 'pop_product_name'
        ));


        if (mage::getStoreConfig('purchase/purchase_product_grid/display_supplier_sku')) {
            $this->addColumn('supplier_sku', array(
                'header' => Mage::helper('purchase')->__('Supplier<br>sku'),
                'index' => 'pop_supplier_ref',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Textbox',
                'textbox_size' => '10',
                'textbox_name' => 'pop_supplier_ref_{id}',
                'align' => 'center'
            ));
        }

        $this->addColumn('pop_qty', array(
            'header' => Mage::helper('purchase')->__('Qty'),
            'index' => 'pop_qty',
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Qty',
            'align' => 'center'
        ));
        //  custom Code dec, 2013 starts
        $this->addColumn('productid', array(
            'header' => Mage::helper('purchase')->__('Product ID'),
            'index' => 'pop_product_id',
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_ProductId',
            'align' => 'center'
        ));
        /*$this->addColumn('serial_date', array(
                'header' => Mage::helper('purchase')->__('Date'),
                'editable' => true,
                'edit_only' => false,
                'filter' => false,
                'sortable' => false,
                'align' => 'center',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_ProductDate'
            ));*/
        $this->addColumn('serialId', array(
            'header' => Mage::helper('purchase')->__('Serials'),
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Serials',
            'align' => 'center'
        ));
        //  custom Code dec, 2013 ends
        if (mage::helper('purchase/Product_Packaging')->isEnabled()) {
            $this->addColumn('product_packaging', array(
                'header' => Mage::helper('purchase')->__('Packaging'),
                'filter' => false,
                'sortable' => false,
                'align' => 'center',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Packaging'
            ));
        }

        if (mage::getStoreConfig('purchase/purchase_product_grid/display_prefered_stock_level')) {
            $this->addColumn('prefered_stock_level', array(
                'header' => Mage::helper('purchase')->__('Ideal Stock'),
                'filter' => false,
                'sortable' => false,
                'align' => 'center',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_PreferedStockLevel'
            ));
        }


        $this->addColumn('delivered_qty', array(
            'header' => Mage::helper('purchase')->__('Delivered<br>Qty'),
            'index' => 'sku',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_DeliveredQty',
            'align' => 'center'
        ));

        $this->addColumn('buying_price', array(
            'header' => Mage::helper('purchase')->__('Buying<br>Price (%s)', $orderCurrencyCode),
            'index' => 'pop_price_ht',
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_BuyPrice',
            'filter' => false,
            'sortable' => false,
            'align' => 'center'
        ));

        if (mage::getStoreConfig('purchase/purchase_order/manage_deee') == 1) {
            $this->addColumn('pop_eco_tax', array(
                'header' => Mage::helper('purchase')->__('Weee<br>(%s)', $orderCurrencyCode),
                'index' => 'pop_eco_tax',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Textbox',
                'textbox_size' => '4',
                'textbox_name' => 'pop_eco_tax_{id}',
                'onkeyup' => 'updateOrderProductInformation({id})',
                'align' => 'center'
            ));
        }

        if (mage::getStoreConfig('purchase/purchase_product_grid/display_discount')) {
            $this->addColumn('pop_discount', array(
                'header' => Mage::helper('purchase')->__('Discount %'),
                'index' => 'pop_discount',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Textbox',
                'textbox_size' => '4',
                'textbox_name' => 'pop_discount_{id}',
                'align' => 'center',
                'postfix' => '%',
                'onkeyup' => 'updateOrderProductInformation({id})'
            ));
        }

        $this->addColumn('sale_price', array(
            'header' => Mage::helper('purchase')->__('Sale<br>Price (%s)', $baseCurrencyCode),
            'filter' => false,
            'sortable' => false,
            'align' => 'center',
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_SalePrice',
            'currency_change_rate' => $this->getOrder()->getpo_currency_change_rate()
        ));

        if (mage::getStoreConfig('purchase/purchase_product_grid/display_last_buy_price') == 1) {
            $this->addColumn('last_buy_price', array(
                'header' => Mage::helper('purchase')->__('Last<br>Price (%s)', $orderCurrencyCode),
                'filter' => false,
                'sortable' => false,
                'align' => 'center',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_LastBuyPrice'
            ));
        }

        $this->addColumn('tax_rate', array(
            'header' => Mage::helper('purchase')->__('Tax<br>Rate (%)'),
            'index' => 'pop_tax_rate',
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Textbox',
            'textbox_size' => '3',
            'textbox_name' => 'pop_tax_rate_{id}',
            'filter' => false,
            'sortable' => false,
            'align' => 'center'
        ));

        if (mage::getStoreConfig('purchase/purchase_product_grid/display_subtotal') == 1) {
            $this->addColumn('subtotal', array(
                'header' => Mage::helper('purchase')->__('Subtotal (%s)', $orderCurrencyCode),
                'filter' => false,
                'sortable' => false,
                'align' => 'center',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Subtotal'
            ));
        }

        if (mage::getStoreConfig('purchase/purchase_product_grid/display_specific_delivery_date')) {
            $this->addColumn('delivery_date', array(
                'header' => Mage::helper('purchase')->__('Delivery<br>Date'),
                'filter' => false,
                'sortable' => false,
                'align' => 'center',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_DeliveryDate'
            ));
        }
		
        $this->addColumn('delete', array(
            'header' => Mage::helper('purchase')->__('Delete'),
            'index' => 'sku',
            'filter' => false,
            'sortable' => false,
            'align' => 'center',
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_OrderProduct_Delete'
        ));


        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/ProductsGrid', array('_current' => true, 'po_num' => $this->getOrder()->getId()));
    }
    // add new generate and View serials button
    public function getMainButtonsHtml()
        {
            $purchase_order =  $this->getOrder()->getPoOrderId();
            $purchase_order = base64_encode($purchase_order);
            // $purchase_serial = Mage::getModel('barcodes/barcodes')->getCollection()
            //             ->addFieldToFilter('purchase_order', array('eq'=>$purchase_order))->getData();
            $html = parent::getMainButtonsHtml();//get the parent class buttons
            $addButtonSerialView ='';
            // if(!empty($purchase_serial)){
                $addButtonSerialView = $this->getLayout()->createBlock('adminhtml/widget_button') //create the add button
                ->setData(array(
                    'label'     => Mage::helper('purchase')->__('View Serials'),
                    'onclick'   => "window.open('".$this->getUrl('barcode_admin/adminhtml_barcodes', array('filter' => $purchase_order))."','_blank')",
                ))->toHtml();
            // }

            // $compareCollection = $this->getOrder()->getProducts();
            // $prodCollection = $compareCollection->getData();
            // $storeQty = 0;
            // $_collection = 0;
            // $addButtonSerialGenerate ='';
            // foreach ($prodCollection as $prodqty) {
            //     $storeQty += $prodqty['pop_qty'];
            //     $productId = $prodqty['pop_product_id'];
            //     $_collection += Mage::getModel('barcodes/barcodes')->getCollection()
            //                   ->addFieldToFilter('purchase_order', array('eq' => $purchase_order))
            //                   ->addFieldToFilter('product_id', array('eq' => $productId))
            //                   ->getSize();
            // }
            // if($_collection < $storeQty){
            //         $addButtonSerialGenerate = 1;
            // }
            
            $addButton = '';
            // if ($addButtonSerialGenerate) {
            $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                    'label'     => Mage::helper('purchase')->__('Generate Serials'),
                    'onclick'   => "serial_check.value=1;beforeSavePurchaseOrder()"
                ))->toHtml();
            // }
            $inputhide = '<input type="hidden" id="serial_check" name="serial_check" value="">';
            return $addButtonSerialView.$addButton.$html.$inputhide;
    }

}
