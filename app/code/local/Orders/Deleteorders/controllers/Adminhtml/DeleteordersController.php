<?php
class Orders_Deleteorders_Adminhtml_DeleteordersController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('deleteorderstab')->_title($this->__('Delete Orders'));
        $block = $this->getLayout()->createBlock('core/text')->setText('<h2>Main Delete Area</h2>');
        $this->_addContent($block);
        $this->getAllOrders();
        $checkedOrders = $_GET['checkedOrders'];
        if (empty($checkedOrders))
        {
            $this->getTextBlock("You didn't select any orders.");
        }
        else
        {
            $this->deleteCheckedOrders($checkedOrders);
        }
        $this->renderLayout();
    }

    public function getAllOrders()
    {
        $collectiveOrderData = Mage::getModel('editorder/order')->getCollection()->getData();
        $form = "<form method=\"get\">";
        foreach ($collectiveOrderData as $order)
        {
            if ($order['increment_id'] != "")
            {
                $form .= $this->createCheckbox($order['increment_id'], $order['customer_firstname'], $order['customer_lastname'], $order['entity_id']);
            }
        }
        $form .= "<br><input type=\"submit\" value=\"Delete Orders\" />";
        $form .= "</form>";
        $this->getTextBlock($form);
    }

    public function getTextBlock($text)
    {
       $block = $this->getLayout()->createBlock('core/text')->setText($text); 
       $this->_addContent($block);
    }

    public function createCheckbox($orderNumber, $firstname, $lastname, $id)
    {
        $text = $orderNumber . ' ' . $firstname . ' ' . $lastname;
        $form = "<input type=\"checkbox\" name=\"checkedOrders[]\" value=" . $id . " />  " .  $text . "<br />";
        return $form;
    }

    public function deleteCheckedOrders($orders)
    {
        $params = $this->getRequest()->getParams();
        //var_dump($params);
        $count = count($orders);
        //echo ("You selected $count order(s): ");
        for ($i=0; $i < $count; $i++)
        {
            $orderToDelete = Mage::getModel('sales/order')->load($orders[$i]);
            //$orderToDelete->delete();
            echo("Order: " . $orders[$i] . " has been deleted. ");
        }

    }
}
