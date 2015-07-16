<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     439
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advr_Block_Adminhtml_Order_Plain extends Mirasvit_Advr_Block_Adminhtml_Block_Container
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Orders'));

        return $this;
    }

    protected function _prepareChart()
    {
        return $this;
    }

    protected function _prepareGrid()
    {
        $this->_initGrid()
            ->setDefaultSort('sum_grand_total')
            ->setDefaultDir('desc')
            ->setPagerVisibility(true)
            ->setRowUrlCallback(array($this, 'rowUrlCallback'))
            ;

        return $this;
    }

    public function _prepareCollection()
    {
        $filterData = $this->getFilterData();

        $collection = Mage::getModel('sales/order')->getCollection();

        $collection->addFieldToFilter('created_at', array('gteq' => $filterData->getFromLocal()))
            ->addFieldToFilter('created_at', array('lteq' => $filterData->getToLocal()))
            ;

        $collection->getSelect()->joinLeft(
                array('payment_table' => $collection->getResource()->getTable('sales/order_payment')),
                'payment_table.parent_id = main_table.entity_id',
                array('method'));

        if (count($filterData->getStoreIds())) {
            $collection->getSelect()
                ->where('main_table.store_id IN('.implode(',', $filterData->getStoreIds()).')')
                ;
        }

        return $collection;
    }

    public function getColumns()
    {
        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $paymentMethods = Mage::getSingleton('payment/config')->getActiveMethods();
        $paymentMethodOptions = array();
        foreach ($paymentMethods as $paymentCode => $paymentModel) {
            $paymentMethodOptions[$paymentCode] = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
        }

        $columns = array(
            'increment_id' => array(
                'header'       => Mage::helper('advr')->__('Order #'),
                'totals_label' => Mage::helper('advr')->__('Totals')
            ),

            'invoice_id' => array(
                'header'          => Mage::helper('advr')->__('Invoice #'),
                'sortable'        => false,
                'filter'          => false,
                'frame_callback'  => array($this, 'invoice'),
                'export_callback' => array($this, 'invoice'),
                'hidden'          => true,
            ),

            'customer_firstname' => array(
                'header'            => Mage::helper('advr')->__('Firstname'),
                'column_css_class'  => 'nobr',
            ),

            'customer_lastname' => array(
                'header'            => Mage::helper('advr')->__('Lastname'),
                'column_css_class'  => 'nobr',
            ),

            'customer_email' => array(
                'header'            => Mage::helper('advr')->__('Email'),
                'column_css_class'  => 'nobr',
            ),

            'customer_group_id' => array(
                'header'            => Mage::helper('advr')->__('Customer Group'),
                'type'              => 'options',
                'options'           =>  $groups,
                'column_css_class'  => 'nobr',
            ),
            
            'customer_taxvat' => array(
                'header'       => Mage::helper('advr')->__('Tax/VAT number'),
                'hidden'       => true,
            ),

            'created_at' => array(
                'header'            => Mage::helper('advr')->__('Purchased On'),
                'type'              => 'datetime',
                'column_css_class'  => 'nobr',
            ),

            'state' => array(
                'header'       => Mage::helper('advr')->__('State'),
                'type'         => 'options',
                'options'      => Mage::getSingleton('sales/order_config')->getStates(),
                'hidden'       => true,
            ),

            'status' => array(
                'header'       => Mage::helper('advr')->__('Status'),
                'type'         => 'options',
                'options'      => Mage::getSingleton('sales/order_config')->getStatuses(),
            ),

            'products' => array(
                'header'          => Mage::helper('advr')->__('Item(s)'),
                'sortable'        => false,
                'filter'          => false,
                'frame_callback'  => array($this, 'products'),
                'export_callback' => array($this, 'products'),
                'hidden'          => true,
            ),

            'tracking_number' => array(
                'header'          => Mage::helper('advr')->__('Tracking Number'),
                'sortable'        => false,
                'filter'          => false,
                'frame_callback'  => array($this, 'trackingNumber'),
                'export_callback' => array($this, 'trackingNumber'),
                'hidden'          => true,
            ),

            'method' => array(
                'type'            => 'options',
                'header'          => Mage::helper('advr')->__('Payment Type'),
                'hidden'          => true,
                'options'         => $paymentMethodOptions,
                'index'           => 'method',
                'filter_index'    => 'payment_table.method',
            ),


            'total_qty_ordered' => array(
                'header'        => Mage::helper('advr')->__('Quantity Ordered'),
                'type'          => 'number',
            ),

            'base_tax_amount' => array(
                'header'        => Mage::helper('advr')->__('Tax'),
                'type'          => 'currency',
                'hidden'        => true,
            ),

            'base_shipping_amount' => array(
                'header'        => Mage::helper('advr')->__('Shipping'),
                'type'          => 'currency',
                'hidden'        => true,
            ),

            'base_discount_amount' => array(
                'header'        => Mage::helper('advr')->__('Discount'),
                'type'          => 'currency',
            ),

            'base_total_refunded' => array(
                'header'        => Mage::helper('advr')->__('Refunded'),
                'type'          => 'currency',
            ),

            'base_total_paid' => array(
                'header'        => Mage::helper('advr')->__('Paid'),
                'type'          => 'currency',
                'hidden'        => true,
            ),

            'base_total_invoiced' => array(
                'header'        => Mage::helper('advr')->__('Total Invoiced'),
                'type'          => 'currency',
                'hidden'        => true,
            ),

            'base_grand_total' => array(
                'header'        => Mage::helper('advr')->__('Grand Total'),
                'type'          => 'currency',
            ),
        );

        return $columns;
    }

    public function rowUrlCallback($row)
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getEntityId()));
    }

    public function invoice($value, $row, $column)
    {
        $data = array();
        $collection = $row->getInvoiceCollection();
        foreach ($collection as $invoice) {
            $data[] = $invoice->getIncrementId();
        }

        return implode(' ', $data);
    }

    public function products($value, $row, $column)
    {
        $collection = $row->getAllVisibleItems();
        foreach ($collection as $item) {
            $data[] = '<a class="nobr" href="'.$this->getUrl('adminhtml/catalog_product/edit', array('id' => $item->getProductId())).'">'
                    .$item->getSku()
                    .' / '
                    .Mage::helper('core/string')->truncate($item->getName(), 50)
                    .' / '.intval($item->getQtyOrdered()).' × '.Mage::helper('core')->currency($item->getBasePrice())
                .'</a>';
        }

        return implode('<br>', $data);
    }

    public function trackingNumber($value, $row, $column)
    {
        $trackNumbers = array();

        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->setOrderFilter($row);

        foreach ($shipmentCollection as $shipment){
            foreach($shipment->getAllTracks() as $trackNumber) {
                $trackNumbers[] = $trackNumber->getNumber();
            }
        }
        
        return implode('<br>', $trackNumbers);
    }
}