<?php
class Orders_Editorder_Block_View extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $html = "";
        $orders = Mage::getModel('editorder/order')->getCollection();
        $collection = $orders->getData();
        foreach($collection as $order)
        {
           $html = $html . 'Order Number: ' . $order['entity_id'];
           $html = $html . 'First Name: ' . $order['customer_firstname']; 
        }
        return $html;
    }
