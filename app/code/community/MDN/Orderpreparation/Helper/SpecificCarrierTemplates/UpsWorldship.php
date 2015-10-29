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
class MDN_Orderpreparation_Helper_SpecificCarrierTemplates_UpsWorldship extends MDN_Orderpreparation_Helper_SpecificCarrierTemplates_Abstract
{
	/**
	 * Generate XML output
	 *
	 * @param unknown_type $orderPreparationCollection
	 */
	public function createExportFile($orderPreparationCollection)
	{
		//init xml writer and write directives
		$xml = mage::helper('Orderpreparation/XmlWriter');
		$xml->init();
		$xml->push('OpenShipments', array('xmlns' => 'x-schema:OpenShipments.xdr'));
		
		//browse collection
		foreach ($orderPreparationCollection as $orderToPrepare)
		{
			//init info for order
			$xml->push('OpenShipment', array('ShipmentOption' => '', 'ProcessStatus' => ''));
			$order = mage::getModel('sales/order')->load($orderToPrepare->getorder_id());
			$shipment = mage::getModel('sales/order_shipment')->loadByIncrementId($orderToPrepare->getshipment_id());
			$address = $this->getAddress($order);
			
			//ship to section
			$xml->push('ShipTo');
			
			$xml->element('CustomerID', $order->getcustomer_id());
			$xml->element('CompanyOrName', $address->getname());
			$xml->element('Attention', $shipment->getincrement_id());
			$xml->element('Address1', $address->getStreet(1));
			$xml->element('Address2', $address->getStreet(2));
			$xml->element('CountryTerritory', $address->getCountry());
			$xml->element('PostalCode', $address->getPostcode());
			$xml->element('CityOrTown', $address->getcity());
			$xml->element('StateProvinceCounty', $address->getregion());
			$xml->element('Telephone', $address->gettelephone());
			$xml->element('EmailAddress', $order->getCustomerEmail());
			
			$xml->pop();
			
			//shipment info
			$xml->push('ShipmentInformation');
			
			$xml->element('ServiceType', 'ST');
			$xml->element('PackageType', 'CP');
			$xml->element('NumberOfPackages', '1');
			$xml->element('ShipmentActualWeight', $orderToPrepare->getreal_weight());
			$xml->element('DescriptionOfGoods', 'Isolee Tienda');
			$xml->element('Reference1', $order->getincrement_id());
			$xml->element('DocumentOnly', '0');
			$xml->element('GoodsNotInFreeCirculation', '0');
			$xml->element('BillingOption', 'PP');
			
			$xml->push('COD');
			$xml->element('CashierCheckorMoneyOrderOnlyIndicator', '1');
			$xml->element('Amount', $order->getGrandTotal());
			$xml->element('Currency', $order->getOrderCurrencyCode());
			$xml->pop();	//end COD

			$xml->pop();	//end ShipmentInformation
			
			$xml->pop();	//end OpenShipment
		}
		
		
		$xml->pop();	//end OpenShipments
		
		return $xml->getXml();
	}
}