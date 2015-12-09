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
class MDN_Orderpreparation_Helper_Invoice extends Mage_Core_Helper_Abstract {

    /**
     * Store invoice id in ordertoprepare model
     *
     * @param unknown_type $OrderId
     * @param unknown_type $InvoiceId
     */
    public function StoreInvoiceId($OrderId, $InvoiceId) {
		$item = mage::helper('Orderpreparation')->getOrderToPrepareForCurrentContext($OrderId);
        $item->setinvoice_id($InvoiceId)->save();
    }

    /*
     * Check if invoice is created for 1 order
     *
     */

    public function InvoiceCreatedForOrder($OrderId) {
		$item = mage::helper('Orderpreparation')->getOrderToPrepareForCurrentContext($OrderId);
        if (($item->getinvoice_id() == null) || ($item->getinvoice_id() == ''))
            return false;
        else
            return true;
    }

    /**
     * Create invoice for order
     *
     * @param unknown_type $order
     */
    public function CreateInvoice(&$order) {
        try {

            if (!$order->canInvoice()) {
                return 0;
            }

            //verifie si il faut forcer la date de la facture
            $order_to_prepare = mage::helper('Orderpreparation')->getOrderToPrepareForCurrentContext($order->getId());

            Mage::dispatchEvent('orderpreparartion_before_create_invoice', array('order' => $order));

            //on cree la facture
            $convertor = Mage::getModel('sales/convert_order');
            $invoice = $convertor->toInvoice($order);

            //parcourt les �l�ments de la commande
            foreach ($order->getAllItems() as $orderItem) {
                //ajout au invoice
                $InvoiceItem = $convertor->itemToInvoiceItem($orderItem);
                $InvoiceItem->setQty($orderItem->getqty_ordered());
                $invoice->addItem($InvoiceItem);
            }

            //sauvegarde la facture
            $invoice->collectTotals();
            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder())
                            ->save();
            //$invoice->pay();
            $invoice->save();

            //link order & invoice
            $this->StoreInvoiceId($order->getid(), $invoice->getincrement_id());

            //validate payment
            $payment = $order->getPayment();
            $payment->pay($invoice);
            $payment->save();

            //capture invoice if required
            if (mage::getStoreConfig('orderpreparation/misc/capture_invoice')) {
                if ($invoice->canCapture())
                    $invoice->capture();
            }

            $order->save();

            Mage::dispatchEvent('orderpreparartion_after_create_invoice', array('order' => $order, 'invoice' => $invoice));

            return 1;
        } catch (Exception $ex) {
            throw new Exception('Error while creating Invoice for Order ' . $order->getincrement_id() . ': ' . $ex->getMessage() . ' - ' . $ex->getTraceAsString());
        }
    }

}
