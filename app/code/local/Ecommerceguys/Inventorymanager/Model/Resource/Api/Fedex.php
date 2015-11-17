<?php 

class Ecommerceguys_Inventorymanager_Model_Resource_Api_Fedex extends Ecommerceguys_Inventorymanager_Model_Fedexcommon
{
	public $path_to_wsdl;
	
	public function __construct(){
		$this->path_to_wsdl = Mage::helper('inventorymanager')->wsdlPath() . "ShipService_v17.wsdl";
		ini_set('soap.wsdl_cache_enabled',0);
		ini_set('soap.wsdl_cache_ttl',0);
		define('SHIP_LABEL', 'BillOfLading.pdf');  // PDF label file.
		define('ADDRESS_LABEL', 'AddressLabel.pdf');  // PDF label file.

		ini_set("soap.wsdl_cache_enabled", "0");

	}
	
	public function getProperty($key){
		return parent::getProperty($key);
	}
	
	public function getResponse(){
		
		//echo $this->path_to_wsdl; exit;
		
		$client = new SoapClient($this->path_to_wsdl, array('trace' => 1));
		$request['WebAuthenticationDetail'] = array(
				'UserCredential' => array(
				'Key' => $this->getProperty('key'), 
				'Password' => $this->getProperty('password')
			)
		);
		
		$request['ClientDetail'] = array(
			'AccountNumber' => $this->getProperty('shipaccount'), 
			'MeterNumber' => $this->getProperty('meter')
		);
		
		$request['TransactionDetail'] = array('CustomerTransactionId' => 'Freight Shipment Example');
		
		$request['Version'] = array(
			'ServiceId' => 'ship', 
			'Major' => '17', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		
		$request['RequestedShipment'] = array(
			'ShipTimestamp' => date('c'),
			'DropoffType' => 'REGULAR_PICKUP', 
			'ServiceType' => 'FEDEX_FREIGHT_ECONOMY', 
			'PackagingType' => 'YOUR_PACKAGING', 
			'Shipper' => $this->getProperty('freightbilling'),
			'Recipient' => $this->addRecipient(),
			'ShippingChargesPayment' => $this->addShippingChargesPayment(),
			'FreightShipmentDetail' => array(
				'FedExFreightAccountNumber' => $this->getProperty('freightaccount'),
				'FedExFreightBillingContactAndAddress' => $this->getProperty('freightbilling'),
				'PrintedReferences' => array(
					'Type' => 'SHIPPER_ID_NUMBER',
					'Value' => 'RBB1057'
				),
				'Role' => 'SHIPPER',
				'PaymentType' => 'PREPAID',
				'CollectTermsType' => 'STANDARD',
				'DeclaredValuePerUnit' => array(
					'Currency' => 'USD',
					'Amount' => 50
				),
				'LiabilityCoverageDetail' => array(
					'CoverageType' => 'NEW',
					'CoverageAmount' => array(
						'Currency' => 'USD',
						'Amount' => '50'
					)
				),
				'TotalHandlingUnits' => 15,
				'ClientDiscountPercent' => 0,
				'PalletWeight' => array(
					'Units' => 'LB',
					'Value' => 20
				),
				'ShipmentDimensions' => array(
					'Length' => 60,
					'Width' => 40,
					'Height' => 50,
					'Units' => 'IN'
				),
				'LineItems' => array(
					'FreightClass' => 'CLASS_050',
					'ClassProvidedByCustomer' => false,
					'HandlingUnits' => 15,
					'Packaging' => 'PALLET',
					'Pieces' => 1,
					'BillOfLaddingNumber' => 'BOL_12345',
					'PurchaseOrderNumber' => 'PO_12345',
					'Description' => 'Heavy Stuff',
					'Weight' => array(
						'Value' => 500.0,
						'Units' => 'LB'
					),
					'Dimensions' => array(
						'Length' => 60,
						'Width' => 40,
						'Height' => 50,
						'Units' => 'IN'
					),
					'Volume' => array(
						'Units' => 'CUBIC_FT',
						'Value' => 30
					)
				)
			),	
			'LabelSpecification' => $this->addLabelSpecification(),
			'ShippingDocumentSpecification' => $this->addShippingDocumentSpecification(),
			'PackageCount' => 1,
			'PackageDetail' => 'INDIVIDUAL_PACKAGES'                                        
		);

		try {
			if($this->setEndpoint('changeEndpoint')){
				$newLocation = $client->__setLocation(setEndpoint('endpoint'));
			}
			$response = $client->processShipment($request); // FedEx web service invocation  
		    if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){
		    	$this->printSuccess($client, $response);
		        // Create PNG or PDF label
		        // Set LabelSpecification.ImageType to 'PNG' for generating a PNG label

		    	$shippingDocuments = $response->CompletedShipmentDetail->ShipmentDocuments;
		    	foreach($shippingDocuments as $key => $value){
		    		$type = $value->Type;
		    		if($type == "OUTBOUND_LABEL"){
		    			$bol = $value->Parts->Image;
		    			$fp = fopen(SHIP_LABEL, 'wb');
		    			fwrite($fp, $bol);
		        		fclose($fp);
		        		echo '<a href="./'.SHIP_LABEL.'">'.SHIP_LABEL.'</a> was generated.<br/>';
		    		}else if($type == "FREIGHT_ADDRESS_LABEL"){
		    			$addressLabel = $value->Parts->Image;
		    			$fp1 = fopen(ADDRESS_LABEL, 'wb');   
		        		fwrite($fp1, $addressLabel);
		        		fclose($fp1);
		        		echo '<a href="./'.ADDRESS_LABEL.'">'.ADDRESS_LABEL.'</a> was generated.<br/>'; 
		    		}
		    	}
		    }else{
		        printError($client, $response);
		    }
			Mage::log($client,nill, "fedex.log");    // Write to log file
		} catch (SoapFault $exception) {
		    printFault($exception, $client);
		}
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
		$shippingChargesPayment = array('PaymentType' => 'SENDER',
	        'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => $this->getProperty('freightaccount'),
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
			'LabelFormatType' => 'FEDEX_FREIGHT_STRAIGHT_BILL_OF_LADING', // valid values COMMON2D, LABEL_DATA_ONLY
			'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
			'LabelStockType' => 'PAPER_LETTER'
		);
		return $labelSpecification;
	}
	public function addShippingDocumentSpecification(){
		$shippingDocumentSpecification = array(
			'ShippingDocumentTypes' => array('FREIGHT_ADDRESS_LABEL'),
			'FreightAddressLabelDetail' => array(
				'Format' => array(
					'ImageType' => 'PDF',
					'StockType' => 'PAPER_4X6',
					//'ProvideInstuctions' => true
				)
			)
		);
		return $shippingDocumentSpecification;
	}
}