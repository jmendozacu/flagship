<?php 
class Ecommerceguys_Inventorymanager_Model_Resource_Api_Fedexground extends Ecommerceguys_Inventorymanager_Model_Fedexcommon
{
	public $path_to_wsdl;
	public $shippingLabel;
	public $bol;
	
	public function __construct(){
		
		$this->path_to_wsdl = Mage::helper('inventorymanager')->wsdlPath() . "ShipService_v17.wsdl";
		ini_set('soap.wsdl_cache_enabled', 0);
		ini_set('soap.wsdl_cache_ttl', 0);
		//define('SHIP_LABEL', Mage::getBaseDir().'/media/fedex/billoflanding'.'BillOfLading.pdf');  // PDF label file.
		//define('ADDRESS_LABEL', 'AddressLabel.pdf');  // PDF label file.
		ini_set("soap.wsdl_cache_enabled", "0");
	}
	public function getProperty($key){
		
		$var = $key;
		
		if($var == 'key') Return Mage::getStoreConfig('inventorymanager/fedex_config/key');
	    if($var == 'password') Return Mage::getStoreConfig('inventorymanager/fedex_config/password'); 
		
		if($var == 'shipaccount') Return Mage::getStoreConfig('inventorymanager/fedex_config/shipaccount');
	    if($var == 'billaccount') Return Mage::getStoreConfig('inventorymanager/fedex_config/shipaccount');
	    if($var == 'dutyaccount') Return Mage::getStoreConfig('inventorymanager/fedex_config/freightaccount');
	    if($var == 'freightaccount') Return Mage::getStoreConfig('inventorymanager/fedex_config/freightaccount');
	    if($var == 'trackaccount') Return Mage::getStoreConfig('inventorymanager/fedex_config/shipaccount');
	
	    if($var == 'meter') Return Mage::getStoreConfig('inventorymanager/fedex_config/meter_number');
	    
	     if($var == 'recipient'){ 
	     	
	     	return array(
		        'Contact' => array(
		            'PersonName' => 'Recipient Name',
		            'CompanyName' => 'Recipient Company Name',
		            'PhoneNumber' => '1234567890'
		        ),
		        'Address' => array(
		            'StreetLines' => array('Address Line 1'),
		            'City' => 'Herndon',
		            'StateOrProvinceCode' => 'VA',
		            'PostalCode' => '20171',
		            'CountryCode' => 'US',
		            'Residential' => 1
		        )
	    	);
		}
	    
	    if($var == 'freightbilling'){ /*Return 
	    
	    array(
	        'Contact'=>array(
	            'ContactId' => 'freight1',
	            'PersonName' => 'Big Shipper',
	            'Title' => 'Manager',
	            'CompanyName' => 'Freight Shipper Co',
	            'PhoneNumber' => '1234567890'
	        ),
	        'Address'=>array(
	            'StreetLines'=>array(
	                '1202 Chalet Ln', 
	                'Do Not Delete - Test Account'
	            ),
	            'City' =>'Harrison',
	            'StateOrProvinceCode' => 'AR',
	            'PostalCode' => '72601-6353',
	            'CountryCode' => 'US'
	            )
	    );*/
	    	$shipper = Mage::getStoreConfig('inventorymanager/fedex_config/shipper_address');
			$shipperAddress = (array)json_decode($shipper); 
			$shipperAddress['Contact'] = (array)$shipperAddress['Contact'];
			$shipperAddress['Address'] = (array)$shipperAddress['Address'];
			return $shipperAddress;
	    }
		return parent::getProperty($key);
	}
	public function getResponse($serialId = 0, $orderId){

		$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

		$request['WebAuthenticationDetail'] = array(
			'ParentCredential' => array(
				'Key' => $this->getProperty('parentkey'), 
				'Password' => $this->getProperty('parentpassword')
			),
			'UserCredential' => array(
				'Key' => $this->getProperty('key'), 
				'Password' => $this->getProperty('password')
			)
		);

		$request['ClientDetail'] = array(
			'AccountNumber' => $this->getProperty('shipaccount'), 
			'MeterNumber' => $this->getProperty('meter')
		);
		$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Ground Domestic Shipping Request using PHP ***');
		$request['Version'] = array(
			'ServiceId' => 'ship', 
			'Major' => '17', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		$request['RequestedShipment'] = array(
			'ShipTimestamp' => date('c'),
			'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
			'ServiceType' => 'FEDEX_GROUND', // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
			'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
			'Shipper' => $this->addShipper(),
			'Recipient' => $this->addRecipient(),
			'ShippingChargesPayment' => $this->addShippingChargesPayment(),
			'LabelSpecification' => $this->addLabelSpecification(),
			/* Thermal Label */
			/*
			'LabelSpecification' => array(
				'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
				'ImageType' => 'EPL2', // valid values DPL, EPL2, PDF, ZPLII and PNG
				'LabelStockType' => 'STOCK_4X6.75_LEADING_DOC_TAB',
				'LabelPrintingOrientation' => 'TOP_EDGE_OF_TEXT_FIRST'
			),
			*/
			'PackageCount' => 1,
			'PackageDetail' => 'INDIVIDUAL_PACKAGES',                                        
			'RequestedPackageLineItems' => array(
				'0' => $this->addPackageLineItem1()
			)
		);
		   
		   
		                                                                                                                           
		try {

			if(setEndpoint('changeEndpoint')){
				$newLocation = $client->__setLocation(setEndpoint('endpoint'));
			}	
			$response = $client->processShipment($request); // FedEx web service invocation
		    if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){
		        printSuccess($client, $response);

		        $fp = fopen(SHIP_CODLABEL, 'wb');   
		        fwrite($fp, $response->CompletedShipmentDetail->CompletedPackageDetails->CodReturnDetail->Label->Parts->Image); //Create COD Return PNG or PDF file
		        fclose($fp);
		        echo '<a href="./'.SHIP_CODLABEL.'">'.SHIP_CODLABEL.'</a> was generated.'.Newline;
		        
		        // Create PNG or PDF label
		        // Set LabelSpecification.ImageType to 'PNG' for generating a PNG label
		    
		        $fp = fopen(SHIP_LABEL, 'wb');   
		        fwrite($fp, ($response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image));
		        fclose($fp);
		        echo '<a href="./'.SHIP_LABEL.'">'.SHIP_LABEL.'</a> was generated.'; 
		    }else{
		        printError($client, $response);
		    }

			writeToLog($client);    // Write to log file
		} catch (SoapFault $exception) {
		    printFault($exception, $client);
		}
	}
	public function addShipper(){
		$shipper = array(
			'Contact' => array(
				'PersonName' => 'Sender Name',
				'CompanyName' => 'Sender Company Name',
				'PhoneNumber' => '1234567890'
			),
			'Address' => array(
				'StreetLines' => array('Address Line 1'),
				'City' => 'Austin',
				'StateOrProvinceCode' => 'TX',
				'PostalCode' => '73301',
				'CountryCode' => 'US'
			)
		);
		return $shipper;
	}
	public function addRecipient(){
		$recipient = array(
			'Contact' => array(
				'PersonName' => 'Recipient Name',
				'CompanyName' => 'Recipient Company Name',
				'PhoneNumber' => '1234567890'
			),
			'Address' => array(
				'StreetLines' => array('Address Line 1'),
				'City' => 'Herndon',
				'StateOrProvinceCode' => 'VA',
				'PostalCode' => '20171',
				'CountryCode' => 'US',
				'Residential' => true
			)
		);
		return $recipient;	                                    
	}
	public function addShippingChargesPayment(){
		$shippingChargesPayment = array(
			'PaymentType' => 'SENDER',
	        'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => $this->getProperty('billaccount'),
					'Contact' => null,
					'Address' => array(
						'CountryCode' => 'US'
					)
				)
			)
		);
		return $shippingChargesPayment;
}
	public function addLabelSpecification(){
		$labelSpecification = array(
			'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
			'ImageType' => 'PNG',  // valid values DPL, EPL2, PDF, ZPLII and PNG
			'LabelStockType' => 'PAPER_4X6'
		);
		return $labelSpecification;
	}
	public function addSpecialServices(){
		$specialServices = array(
			'SpecialServiceTypes' => array('COD'),
			'CodDetail' => array(
				'CodCollectionAmount' => array(
					'Currency' => 'USD', 
					'Amount' => 150
				),
				'CollectionType' => 'ANY' // ANY, GUARANTEED_FUNDS
			)
		);
		return $specialServices; 
	}
	public function addPackageLineItem1(){
		$packageLineItem = array(
			'SequenceNumber'=>1,
			'GroupPackageCount'=>1,
			'Weight' => array(
				'Value' => 50.0,
				'Units' => 'LB'
			),
			'Dimensions' => array(
				'Length' => 108,
				'Width' => 5,
				'Height' => 5,
				'Units' => 'IN'
			),
			'CustomerReferences' => array(
				'0' => array(
					'CustomerReferenceType' => 'CUSTOMER_REFERENCE', // valid values CUSTOMER_REFERENCE, INVOICE_NUMBER, P_O_NUMBER and SHIPMENT_INTEGRITY
					'Value' => 'GR4567892'
				), 
				'1' => array(
					'CustomerReferenceType' => 'INVOICE_NUMBER', 
					'Value' => 'INV4567892'
				),
				'2' => array(
					'CustomerReferenceType' => 'P_O_NUMBER', 
					'Value' => 'PO4567892'
				)
			),
			'SpecialServicesRequested' => $this->addSpecialServices()
		);
		return $packageLineItem;
	}
}