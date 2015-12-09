<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml shipment create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId    = 'shipment_id';
        $this->_controller  = 'sales_order_shipment';
        $this->_mode        = 'view';

        parent::__construct();

//        $this->_removeButton('reset');
        $this->_removeButton('delete');
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/emails')) {
            $this->_updateButton('save', 'label', Mage::helper('sales')->__('Send Tracking Information'));
            $this->_updateButton('save',
                'onclick', "deleteConfirm('"
                . Mage::helper('sales')->__('Are you sure you want to send Shipment email to customer?')
                . "', '" . $this->getEmailUrl() . "')"
            );
        }
            $this->_addButton('printLabel', array(
                'label'     => Mage::helper('sales')->__('Print Shipping Label'),
                'class'     => 'save',
//                'onclick'   => 'setLocation(\''.$this->getPrintLabelUrl().'\')'
                'onclick'   => 'window.open(\''.$this->getPrintLabelUrl().'\')'
                )
            );
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    public function getHeaderText()
    {
        if ($this->getShipment()->getEmailSent()) {
            $emailSent = Mage::helper('sales')->__('the shipment email was sent');
        }
        else {
            $emailSent = Mage::helper('sales')->__('the shipment email is not sent');
        }
        return Mage::helper('sales')->__('Shipment #%1$s | %3$s (%2$s)', $this->getShipment()->getIncrementId(), $emailSent, $this->formatDate($this->getShipment()->getCreatedAtDate(), 'medium', true));
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array(
                'order_id'  => $this->getShipment()->getOrderId(),
                'active_tab'=> 'order_shipments'
            ));
    }

    public function getEmailUrl()
    {
        return $this->getUrl('*/sales_order_shipment/email', array('shipment_id'  => $this->getShipment()->getId()));
    }

    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array(
            'invoice_id' => $this->getShipment()->getId()
        ));
    }

    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getShipment()->getBackUrl()) {
                return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getShipment()->getBackUrl() . '\')');
            }
            return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/sales_shipment/') . '\')');
        }
        return $this;
    }

    public function getStateNameByAbbreviation($state){

if ($state=='Alabama') { return 'AL';}
if ($state=='Alaska') { return 'AK';}
if ($state=='Arizona') { return 'AZ';}
if ($state=='Arkansas') { return 'AR';}
if ($state=='California') { return 'CA';}
if ($state=='Colorado') { return 'CO';}
if ($state=='Connecticut') { return 'CT';}
if ($state=='Delaware') { return 'DE';}
if ($state=='Florida') { return 'FL';}
if ($state=='Georgia') { return 'GA';}
if ($state=='Hawaii') { return 'HI';}
if ($state=='Idaho') { return 'ID';}
if ($state=='Illinois') { return 'IL';}
if ($state=='Indiana') { return 'IN';}
if ($state=='Iowa') { return 'IA';}
if ($state=='Kansas') { return 'KS';}
if ($state=='Kentucky') { return 'KY';}
if ($state=='Louisiana') { return 'LA';}
if ($state=='Maine') { return 'ME';}
if ($state=='Maryland') { return 'MD';}
if ($state=='Massachusetts') { return 'MA';}
if ($state=='Michigan') { return 'MI';}
if ($state=='Minnesota') { return 'MN';}
if ($state=='Mississippi') { return 'MS';}
if ($state=='Missouri') { return 'MO';}
if ($state=='Montana') { return 'MT';}
if ($state=='Nebraska') { return 'NE';}
if ($state=='Nevada') { return 'NV';}
if ($state=='New Hampshire') { return 'NH';}
if ($state=='New Jersey') { return 'NJ';}
if ($state=='New Mexico') { return 'NM';}
if ($state=='New York') { return 'NY';}
if ($state=='North Carolina') { return 'NC';}
if ($state=='North Dakota') { return 'ND';}
if ($state=='Ohio') { return 'OH';}
if ($state=='Oklahoma') { return 'OK';}
if ($state=='Oregon') { return 'OR';}
if ($state=='Pennsylvania') { return 'PA';}
if ($state=='Rhode Island') { return 'RI';}
if ($state=='South Carolina') { return 'SC';}
if ($state=='South Dakota') { return 'SD';}
if ($state=='Tennessee') { return 'TN';}
if ($state=='Texas') { return 'TX';}
if ($state=='Utah') { return 'UT';}
if ($state=='Vermont') { return 'VT';}
if ($state=='Virginia') { return 'VA';}
if ($state=='Washington') { return 'WA';}
if ($state=='West Virginia') { return 'WV';}
if ($state=='Wisconsin') { return 'WI';}
if ($state=='Wyoming') { return 'WY';}



        return "DC";

     }
    public function getPrintLabelUrl()
      {
$_order = $this->getShipment()->getOrder();
$_shippingAddress = $_order->getShippingAddress();
$_firstName = $_shippingAddress->getFirstname();
$_lastName = $_shippingAddress->getLastname();
$_company = $_shippingAddress->getCompany();
$_street= $_shippingAddress->getStreetFull();
$_region = $_shippingAddress->getRegion();
$_email = $_shippingAddress->getEmail();

 $_state = $this->getStateNameByAbbreviation($_region);
//  $_state = $region;
$_city = $_shippingAddress->getCity();
$_zip = $_shippingAddress->getPostcode();
$_country = $_shippingAddress->getCountry_id();
$_phone = $_shippingAddress->getTelephone();

$_method = $_order->getShippingMethod();

$_shipment = $this->getShipment();
$_date = $_order->getOrderDate();
// $_order_id = $this->getShipment()->getId();
 $_order_id = $_order->getId();
 $_order_id = $_order->getId();

$amount = $_order->getGrandTotal() - $_order->getShippingAmount();

$items = $_order->getAllItems();
$_qnty = 0;
$_weight = 0;

//foreach ($items as $itemId => $item)
foreach ($items as $item)
{
    $_qnty += $item->getQtyOrdered();
    $_weight += $item->getWeight() * $_qnty;
}

        $_street = str_replace("\n"," ",$_street);
        $_street = str_replace(" ","%20",$_street);
        $_phone = str_replace(" ","",$_phone);
        $_phone = str_replace("-","",$_phone);
        $_phone = str_replace("(","",$_phone);
        $_phone = str_replace(")","",$_phone);
        $_firstName = str_replace(" ","",$_firstName);
        $_lastName = str_replace(" ","",$_lastName);
        return "http://www.cobbconsulting.net/print_label/demo/print_label.php?invoice_id=" . $_order_id . "&firstName=$_firstName&lastName=$_lastName&company=$_company&country=$_country&street=$_street&city=$_city&zip=$_zip&state=$_state&phone=$_phone&method=$_method&date=$_date&qnty=$_qnty&weight=$_weight&amount=$amount&email=$_email" ;

    }
}
