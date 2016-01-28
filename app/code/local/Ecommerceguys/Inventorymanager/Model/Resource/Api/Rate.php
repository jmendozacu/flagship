<?php
class Ecommerceguys_Inventorymanager_Model_Resource_Api_Rate
{
	public $path_to_wsdl;
	
	public function __construct(){
		$this->path_to_wsdl = Mage::helper('inventorymanager')->wsdlPath() . "ShipService_v17.wsdl";
		ini_set('soap.wsdl_cache_enabled', 0);
		ini_set('soap.wsdl_cache_ttl', 0);
		ini_set("soap.wsdl_cache_enabled", 0);
	}
	
	public function rate(){
		$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

		$request['WebAuthenticationDetail'] = array(
			'ParentCredential' => array(
				'Key' => getProperty('parentkey'),
				'Password' => getProperty('parentpassword')
			),
			'UserCredential' => array(
				'Key' => getProperty('key'), 
				'Password' => getProperty('password')
			)
		); 
		$request['ClientDetail'] = array(
			'AccountNumber' => getProperty('shipaccount'), 
			'MeterNumber' => getProperty('meter')
		);
		$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request using PHP ***');
		$request['Version'] = array(
			'ServiceId' => 'crs', 
			'Major' => '18', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		$request['RequestedShipment']['ServiceType'] = 'INTERNATIONAL_PRIORITY'; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
		$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
		$request['RequestedShipment']['TotalInsuredValue']=array(
			'Ammount'=>100,
			'Currency'=>'USD'
		);
		$request['RequestedShipment']['Shipper'] = addShipper();
		$request['RequestedShipment']['Recipient'] = addRecipient();
		$request['RequestedShipment']['ShippingChargesPayment'] = addShippingChargesPayment();
		$request['RequestedShipment']['PackageCount'] = '1';
		$request['RequestedShipment']['RequestedPackageLineItems'] = addPackageLineItem1();
		
		
		
		try {
			if(setEndpoint('changeEndpoint')){
				$newLocation = $client->__setLocation(setEndpoint('endpoint'));
			}
			
			$response = $client -> getRates($request);
		        
		    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){  	
		    	$rateReply = $response -> RateReplyDetails;
		    	echo '<table border="1">';
		        echo '<tr><td>Service Type</td><td>Amount</td><td>Delivery Date</td></tr><tr>';
		    	$serviceType = '<td>'.$rateReply -> ServiceType . '</td>';
		    	if($rateReply->RatedShipmentDetails && is_array($rateReply->RatedShipmentDetails)){
					$amount = '<td>$' . number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
				}elseif($rateReply->RatedShipmentDetails && ! is_array($rateReply->RatedShipmentDetails)){
					$amount = '<td>$' . number_format($rateReply->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
				}
		        if(array_key_exists('DeliveryTimestamp',$rateReply)){
		        	$deliveryDate= '<td>' . $rateReply->DeliveryTimestamp . '</td>';
		        }else if(array_key_exists('TransitTime',$rateReply)){
		        	$deliveryDate= '<td>' . $rateReply->TransitTime . '</td>';
		        }else {
		        	$deliveryDate='<td>&nbsp;</td>';
		        }
		        echo $serviceType . $amount. $deliveryDate;
		        echo '</tr>';
		        echo '</table>';
		        
		        printSuccess($client, $response);
		    }else{
		        printError($client, $response);
		    } 
		    writeToLog($client);    // Write to log file   
		}catch (SoapFault $exception) {
			printFault($exception, $client);        
		}
	}
	
	
	function getProperty($var){
		if($var == 'key') Return 'XXX'; 
		if($var == 'password') Return 'XXX'; 
		if($var == 'shipaccount') Return 'XXX';
		if($var == 'billaccount') Return 'XXX'; 
		if($var == 'dutyaccount') Return 'XXX'; 
		if($var == 'freightaccount') Return 'XXX';  
		if($var == 'trackaccount') Return 'XXX'; 
		if($var == 'dutiesaccount') Return 'XXX';
		if($var == 'importeraccount') Return 'XXX';
		if($var == 'brokeraccount') Return 'XXX';
		if($var == 'distributionaccount') Return 'XXX';
		if($var == 'locationid') Return 'PLBA';
		if($var == 'printlabels') Return false;
		if($var == 'printdocuments') Return true;
		if($var == 'packagecount') Return '4';
	
		if($var == 'meter') Return 'XXX';
			
		if($var == 'shiptimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));
	
		if($var == 'spodshipdate') Return '2014-07-21';
		if($var == 'serviceshipdate') Return '2017-07-26';
	
		if($var == 'readydate') Return '2014-07-09T08:44:07';
		//if($var == 'closedate') Return date("Y-m-d");
		if($var == 'closedate') Return '2014-07-17';
		if($var == 'pickupdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
		if($var == 'pickuptimestamp') Return mktime(8, 0, 0, date("m")  , date("d")+1, date("Y"));
		if($var == 'pickuplocationid') Return 'XXX';
		if($var == 'pickupconfirmationnumber') Return '1';
	
		if($var == 'dispatchdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
		if($var == 'dispatchlocationid') Return 'XXX';
		if($var == 'dispatchconfirmationnumber') Return '1';		
		
		if($var == 'tag_readytimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));
		if($var == 'tag_latesttimestamp') Return mktime(20, 0, 0, date("m"), date("d")+1, date("Y"));	
	
		if($var == 'expirationdate') Return date("Y-m-d", mktime(8, 0, 0, date("m"), date("d")+15, date("Y")));
		if($var == 'begindate') Return '2014-07-22';
		if($var == 'enddate') Return '2014-07-25';	
	
		if($var == 'trackingnumber') Return 'XXX';
	
		if($var == 'hubid') Return '5531';
		
		if($var == 'jobid') Return 'XXX';
	
		if($var == 'searchlocationphonenumber') Return '5555555555';
		if($var == 'customerreference') Return 'Cust_Reference';
				
		if($var == 'shipper') Return array(
			'Contact' => array(
				'PersonName' => 'Sender Name',
				'CompanyName' => 'Sender Company Name',
				'PhoneNumber' => '1234567890'
			),
			'Address' => array(
				'StreetLines' => array('Address Line 1'),
				'City' => 'Collierville',
				'StateOrProvinceCode' => 'TN',
				'PostalCode' => '38017',
				'CountryCode' => 'US',
				'Residential' => 1
			)
		);
		if($var == 'recipient') Return array(
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
	
		if($var == 'address1') Return array(
			'StreetLines' => array('10 Fed Ex Pkwy'),
			'City' => 'Memphis',
			'StateOrProvinceCode' => 'TN',
			'PostalCode' => '38115',
			'CountryCode' => 'US'
	    );
		if($var == 'address2') Return array(
			'StreetLines' => array('13450 Farmcrest Ct'),
			'City' => 'Herndon',
			'StateOrProvinceCode' => 'VA',
			'PostalCode' => '20171',
			'CountryCode' => 'US'
		);					  
		if($var == 'searchlocationsaddress') Return array(
			'StreetLines'=> array('240 Central Park S'),
			'City'=>'Austin',
			'StateOrProvinceCode'=>'TX',
			'PostalCode'=>'78701',
			'CountryCode'=>'US'
		);
										  
		if($var == 'shippingchargespayment') Return array(
			'PaymentType' => 'SENDER',
			'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => getProperty('billaccount'),
					'Contact' => null,
					'Address' => array('CountryCode' => 'US')
				)
			)
		);	
		if($var == 'freightbilling') Return array(
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
		);
	}	
}