<?php
class Ecommerceguys_Inventorymanager_Model_Shipmanager_Shipment extends Mage_Core_Model_Abstract
{
   public function _construct()
    {
        parent::_construct();
        $this->_init('inventorymanager/shipmanager_shipment');
    }
    /**
     * Completes the Shipment, followed by completing the Order life-cycle
     * It is assumed that the Invoice has already been generated
     * and the amount has been captured.
     */
    public function completeShipment($orderIncrementId,$shipmentTrackingNumber,$shipmentCarrierCode,$shipmentCarrierTitle)
    {
        /**
         * It can be an alphanumeric string, but definitely unique.
         */
        $orderIncrementId = $orderIncrementId;
        
        /**
         * Provide the Shipment Tracking Number,
         * which will be sent out by any warehouse to Magento
         */
        $shipmentTrackingNumber = $shipmentTrackingNumber;
     
        /**
         * This can be blank also.
         */
        $customerEmailComments = '';
     
        $order = Mage::getModel('sales/order')
                     ->loadByIncrementId($orderIncrementId);
     
        if (!$order->getId()) {
            Mage::throwException("Order does not exist, for the Shipment process to complete");
        }
     
        if ($order->canShip()) {
            try {
                $shipment = Mage::getModel('sales/service_order', $order)
                                ->prepareShipment($this->_getItemQtys($order));
     
                /**
                 * Carrier Codes can be like "ups" / "fedex" / "custom",
                 * but they need to be active from the System Configuration area.
                 * These variables can be provided custom-value, but it is always
                 * suggested to use Order values
                 */
                $shipmentCarrierCode = $shipmentCarrierCode;
                $shipmentCarrierTitle = $shipmentCarrierTitle;
     
                $arrTracking = array(
                    'carrier_code' => isset($shipmentCarrierCode) ? $shipmentCarrierCode : $order->getShippingCarrier()->getCarrierCode(),
                    'title' => isset($shipmentCarrierTitle) ? $shipmentCarrierTitle : $order->getShippingCarrier()->getConfigData('title'),
                    'number' => $shipmentTrackingNumber,
                );
     
                $track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
                $shipment->addTrack($track);
     
                // Register Shipment
                $shipment->register();
     
                // Save the Shipment
                $this->_saveShipment($shipment, $order, $customerEmailComments);
     
                // Finally, Save the Order
                $this->_saveOrder($order);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
     
    /**
     * Get the Quantities shipped for the Order, based on an item-level
     * This method can also be modified, to have the Partial Shipment functionality in place
     *
     * @param $order Mage_Sales_Model_Order
     * @return array
     */
    protected function _getItemQtys(Mage_Sales_Model_Order $order)
    {
        $qty = array();
     
        foreach ($order->getAllItems() as $_eachItem) {
            if ($_eachItem->getParentItemId()) {
                $qty[$_eachItem->getParentItemId()] = $_eachItem->getQtyOrdered();
            } else {
                $qty[$_eachItem->getId()] = $_eachItem->getQtyOrdered();
            }
        }
     
        return $qty;
    }
     
    /**
     * Saves the Shipment changes in the Order
     *
     * @param $shipment Mage_Sales_Model_Order_Shipment
     * @param $order Mage_Sales_Model_Order
     * @param $customerEmailComments string
     */
    protected function _saveShipment(Mage_Sales_Model_Order_Shipment $shipment, Mage_Sales_Model_Order $order, $customerEmailComments = '')
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
                               ->addObject($shipment)
                               ->addObject($order)
                               ->save();
     
       /* 
       $emailSentStatus = $shipment->getData('email_sent');
        if (!is_null($customerEmail) && !$emailSentStatus) {
            $shipment->sendEmail(true, $customerEmailComments);
            $shipment->setEmailSent(true);
        }
     */
        return $this;
    }
     
    /**
     * Saves the Order, to complete the full life-cycle of the Order
     * Order status will now show as Complete
     *
     * @param $order Mage_Sales_Model_Order
     */
    protected function _saveOrder(Mage_Sales_Model_Order $order)
    {
        $order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
        $order->setData('status', Mage_Sales_Model_Order::STATE_COMPLETE);
     
        $order->save();
     
        return $this;
    }

    /**
     * Get Zencart Order status data
     */
    protected function zencartShippedOrderStatuses()
    {
        /*
        $resource   = Mage::getSingleton('core/resource');
        $conn       = $resource->getConnection('oscomm_read');
        $results    = $conn->query("SELECT * FROM orders_status");
        $row = $results->fetchAll();
        return $this;*/
        return array(111,105,109,115,114,116,118);
    }
    /**
     * Get the order shipped status
     * return 0,1
     */
    public function getzencartOrderShippedStatus($orderId)
    {
        $resource   = Mage::getSingleton('core/resource');
        $conn       = $resource->getConnection('oscomm_read');
        $results    = $conn->query("SELECT orders_status FROM orders where orders_id=".$orderId);
        $row = $results->fetch();
        if(is_array($row) && !empty($row)){
            if(!in_array($row['orders_status'],$this->zencartShippedOrderStatuses())){
                return 0; 
            }else{
                return 1;
            }

        }
        return; 
    }
    public function getzencartOrderData($orderId)
    {
        $resource   = Mage::getSingleton('core/resource');
        $conn       = $resource->getConnection('oscomm_read');
        $results    = $conn->query("SELECT customers_email_address,customers_name FROM orders where orders_id=".$orderId);
        $row = $results->fetch();
        /*echo "<pre>";
        print_r($row);
        exit;*/
        if(is_array($row) && !empty($row)){
            return $row;
        }
        return; 
    }
    public function zencartUpdateOrderStatus($customername,$customeremail,$orderId,$trackingNumber)
    {
        //echo "test";exit;
        $resource   = Mage::getSingleton('core/resource');
        $connread       = $resource->getConnection('oscomm_read');
        $connwrite       = $resource->getConnection('oscomm_write');
        $comments = "FedEx Tracking: ".$trackingNumber;
        $customer_notified = 0;

        if($this->zencartCustomerShipmentNotify($customername,$customeremail,$orderId,$trackingNumber) == true){
            $customer_notified = 1;
        }
        return;
        $connwrite->query("Update orders SET orders_status = '115', last_modified = now() where orders_id=".$orderId);
        $connwrite->query("insert into orders_status_history(orders_id, orders_status_id, date_added, customer_notified, comments) values ('".(int)$orderId."',115,now(),'".$customer_notified."','".$comments."')");
        return; 
    }

public function zencartCustomerShipmentNotify($customername,$customeremail,$orderId,$trackingNumber){

        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('customer_zencart_shipment_email');
        //Getting the Store E-Mail Sender Name.
        $senderName = Mage::getStoreConfig('trans_email/ident_general/name');

        //Getting the Store General E-Mail.
        $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');

        $customeremail = "ralph@clevermage.com";
        //Variables for Confirmation Mail.
        $emailTemplateVariables = array();
        $emailTemplateVariables['customername'] = $customername;
        $emailTemplateVariables['orderid'] = $orderId;
        $emailTemplateVariables['fedextracking'] = $trackingNumber;
        //Appending the Custom Variables to Template.
        
        //$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $html = "<h3>Hello ".$customername.",</h3><br><div><p>Your Order has been shipped.<p><br><p>Order Id: ".$orderId."</p><br><p>FedEX Tracking Number: ".$trackingNumber."</p></div>";
        //Sending E-Mail to Customers.
        $mail = Mage::getModel('core/email')
         ->setToName($senderName)
         ->setToEmail($customeremail)
         ->setBody($html)
         ->setSubject('Update shipment for Order #'.$orderId)
         ->setFromEmail($senderEmail)
         ->setFromName($senderName)
         ->setType('html');
         try{
         //Confimation E-Mail Send
            $mail->send();
            return true;
         }
         catch(Exception $error)
         {
         Mage::getSingleton('core/session')->addError($error->getMessage());
         return false;
         }
         return;
    }

}