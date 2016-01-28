<?php 

class Ecommerceguys_Inventorymanager_Model_Resource_Api_Fedex extends Ecommerceguys_Inventorymanager_Model_Fedexcommon
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
		
		$serialObject = Mage::getModel('inventorymanager/label')->load($serialId);
     	$order = Mage::getModel('sales/order')->load($orderId);
     	
     	$shippingAddress = $order->getShippingAddress();
     	$region = Mage::getModel('directory/region')->load($shippingAddress->getRegionId);
     	
     	$regionVal = $shippingAddress->getRegion();
     	if($region && $region->getId()){
     		$regionVal = $region->getCode();
     	}
     	
     	$length = 0; $width = 0; $height = 0; $weight = 0;
     	$boxLength = 0; $boxWidth = 0; $boxHeight = 0; $boxWeight = 0;
     	if($serialObject && $serialObject->getId()){
			$orderProduct = Mage::getModel('inventorymanager/product')->load($serialObject->getProductId());
			$purchaseorder = Mage::getModel('inventorymanager/purchaseorder')->load($serialObject->getOrderId());
			$productInfoCollection = Mage::getModel('inventorymanager/vendor_productinfo')->getCollection();
			$productInfoCollection->addFieldToFilter('vendor_id', $purchaseorder->getVendorId());
			$productInfoCollection->addFieldToFilter('product_id', $orderProduct->getMainProductId());
			if($productInfoCollection && $productInfoCollection->count() > 0){
				$productInfoObject = $productInfoCollection->getFirstItem();
				if($productInfoObject && $productInfoObject->getId()){
					
					$catalogproduct = Mage::getModel('catalog/product')->load($productInfoObject->getProductId());
					
					//print_r($productInfoObject); exit;
					
					
					$length	= $productInfoObject->getLength();
					$width = $productInfoObject->getWidth();
					$height = $productInfoObject->getHeight();
					$weight	= $productInfoObject->getWeight();
					
					
					$boxLength = $productInfoObject->getBoxLength();
					$boxWidth = $productInfoObject->getBoxWidth();
					$boxHeight = $productInfoObject->getBoxHeight();
					$boxWeight = $productInfoObject->getBoxWeight();
				}
			}
			
			
		}
     	
     	
		//echo "<pre>";	     	
		//print_r($shippingAddress->getData()); exit;
	     	
		$recipient	= array(
			'Contact' => array(
		            'PersonName' => $shippingAddress->getFirstname() . $shippingAddress->getLastname(),
		            'CompanyName' => $shippingAddress->getCompany(),
		            'PhoneNumber' => $shippingAddress->getTelephone()
		        ),
		        'Address' => array(
		            'StreetLines' => $shippingAddress->getStreet(),
		            'City' => $shippingAddress->getCity(),
		            'StateOrProvinceCode' => $regionVal,
		            'PostalCode' => $shippingAddress->getPostcode(),
		            'CountryCode' => $shippingAddress->getCountryId(),
		            'Residential' => 1
		        )
		);
	     	
		//echo $this->path_to_wsdl; exit;
		
		$this->shippingLabel = Mage::getBaseDir().'\\media\\fedex\\shippinglabels\\'.$serialId.'-ShippingLabel.pdf';
		
		
		$this->bol = Mage::getBaseDir().'\\media\\fedex\\billoflanding\\'.$serialId.'-BillOfLading.pdf';
		
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
			'Recipient' => $recipient,
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
					'Amount' => $catalogproduct->getFinalPrice()
				),
				'LiabilityCoverageDetail' => array(
					'CoverageType' => 'NEW',
					'CoverageAmount' => array(
						'Currency' => 'USD',
						'Amount' => '50'
					)
				),
				'TotalHandlingUnits' => 1,
				'ClientDiscountPercent' => 0,
				'PalletWeight' => array(
					'Units' => 'LB',
					'Value' => 20
				),
				'ShipmentDimensions' => array(
					'Length' => $length,
					'Width' => $width,
					'Height' => $height,
					'Units' => 'IN'
				),
				'LineItems' => array(
					'FreightClass' => 'CLASS_050',
					'ClassProvidedByCustomer' => false,
					'HandlingUnits' => 1,
					'Packaging' => 'PALLET',
					'Pieces' => 1,
					'BillOfLaddingNumber' => 'BOL_12345',
					'PurchaseOrderNumber' => 'PO_12345',
					'Description' => $catalogproduct->getName(),
					'Weight' => array(
						'Value' => $weight,
						'Units' => 'LB'
					),
					'Dimensions' => array(
						'Length' => $boxLength ,
						'Width' => $boxWidth ,
						'Height' => $boxHeight ,
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
		    	//$this->printSuccess($client, $response);
		        // Create PNG or PDF label
		        // Set LabelSpecification.ImageType to 'PNG' for generating a PNG label

		    	$shippingDocuments = $response->CompletedShipmentDetail->ShipmentDocuments;
		    	foreach($shippingDocuments as $key => $value){
		    		$type = $value->Type;
		    		if($type == "OUTBOUND_LABEL"){
		    			$bol =$value->Parts->Image;
		    			
		    			$fp = fopen($this->bol, 'wb');
		    			fwrite($fp, $bol);
		        		fclose($fp);
		        		//echo '<a href="'.$this->bol.'">BILL OF LANDING</a> was generated.<br/>';
		    		}else if($type == "FREIGHT_ADDRESS_LABEL"){
		    			$addressLabel = $value->Parts->Image;

		    			$fp1 = fopen($this->shippingLabel, 'wb');   
		        		fwrite($fp1, $addressLabel);
		        		fclose($fp1);
		        		//echo '<a href="'.$this->shippingLabel.'">Label</a> was generated.<br/>'; 
		    		}
		    	}
		    }else{
		        $this->printError($client, $response);
		    }
			Mage::log($client,nill, "fedex.log");    // Write to log file
		} catch (SoapFault $exception) {
		    $this->printFault($exception, $client);
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