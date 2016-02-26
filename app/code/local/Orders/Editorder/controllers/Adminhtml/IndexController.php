<?php
class Orders_Editorder_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('editordertab')->_title($this->__('Edit Orders'));
        //$this->_addContent($this->getLayout()->createBlock('editorder/view'))->renderLayout();
        $this->renderLayout();
    }
    
    public function showAllOrdersAction()
    {
        $orders = Mage::getModel('editorder/order')->getCollection();
        $collection = $orders->getData();
        foreach($collection as $order)
        {
            echo "ORDER NUMBER: " . $order['entity_id'] . "<br><br>";
            echo "BILLING ADDRESS ID: " . $order['billing_address_id'] . "<br>";
            echo "SHIPPING ADDRESS ID: " . $order['shipping_address_id'] . "<br>
";          echo "CUSTOMER EMAIL: " . $order['customer_email'] . "<br>";
            echo "CUSTOMER FIRST NAME: " . $order['customer_firstname'] . "<br>"
;           echo "CUSTOMER LAST NAME: " . $order['customer_lastname'] . "<br>";
            echo "CUSTOMER MIDDLE NAME: " . $order['customer_middlename'] . "<br
>";
            echo 'CUSTOMER PREFIX: ' . $order['customer_prefix'] . "<br>";
            echo 'CUSTOMER SUFFIX: ' . $order['customer_suffix'] . "<br>";
            echo 'CUSTOMER GENDER:' . $order['customer_gender'] . "<br>";
        }
    }
}
?>
