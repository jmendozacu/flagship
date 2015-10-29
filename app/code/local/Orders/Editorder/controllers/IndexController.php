<?php
class Orders_Editorder_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //$this->loadLayout();
        //$this->renderLayout();
        echo 'test index';
    }
    
    public function viewAction()
    {
        echo 'test view action';
    }

    public function goodbyeAction()
    {
        echo 'Goodbye World!';
        $this->loadLayout();
        $this->renderLayout();
    }

    public function paramsAction()
    {
        echo '<dl>';
        foreach($this->getRequest()->getParams() as $key=>$value)
        {
            echo '<dt><strong>Param: </strong>' . $key . '</dt>';
            echo '<dt><strong>Value: </strong>' . $value . '</dt>';
        }
        echo '</dl>';
    }

    public function testModelAction()
    {
        $params = $this->getRequest()->getParams();
        $order = Mage::getModel('editorder/order');
        echo("Loading the order with an ID of " . $params['entity_id']);
        $orderInfo = $order->load($params['entity_id']);
        $data = $order->getData();
        var_dump($data);
    }

    public function editOrderTestAction()
    {
        $params = $this->getRequest()->getParams();
        $order = Mage::getModel('editorder/order');
        $order->load($params['entity_id']);
        $totalPrice = $order->getData('total_due');
        $billingAddressId = $order->getData('billing_address_id');
        
    }

    public function showAllOrdersAction()
    {
        $orders = Mage::getModel('editorder/order')->getCollection();
        $collection = $orders->getData();
        foreach($collection as $order)
        {
            echo "<b>ORDER NUMBER: " . $order['entity_id'] . "</b><br><br>";
            echo "BILLING ADDRESS ID: " . $order['billing_address_id'] . "<br>";
            echo "SHIPPING ADDRESS ID: " . $order['shipping_address_id'] . "<br>";
            echo "CUSTOMER EMAIL: " . $order['customer_email'] . "<br>";
            echo "CUSTOMER FIRST NAME: " . $order['customer_firstname'] . "<br>";
            echo "CUSTOMER LAST NAME: " . $order['customer_lastname'] . "<br>";
            echo "CUSTOMER MIDDLE NAME: " . $order['customer_middlename'] . "<br>";
            echo 'CUSTOMER PREFIX: ' . $order['customer_prefix'] . "<br>";
            echo 'CUSTOMER SUFFIX: ' . $order['customer_suffix'] . "<br>";
            echo 'CUSTOER GENDER:' . $order['customer_gender'] . "<br>";
        }
        
    }
}
?>
