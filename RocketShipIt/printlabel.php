<?php

if($_POST){

	$toCompany = $_POST['toCompany'];
	$toName = $_POST['toName'];
	$toPhone = $_POST['toPhone'];
	$toAddr1 = $_POST['toAddr1'];
	$toCity = $_POST['toCity'];
	$toState = $_POST['toState'];
	$toCode = $_POST['toCode'];
	$length = $_POST['length'];
	$width = $_POST['width'];
	$height = $_POST['height'];
	$weight = $_POST['weight'];

	if(!$toCompany){
		return "Company name is required";
	}elseif(!$toName){
		return "Name is required";
	}elseif(!$toPhone){
		return "Phone is required";
	}elseif(!$toAddr1){
		return "Address1 is required";
	}elseif(!$toCity){
		return "City is required";
	}elseif(!$toState){
		return "State is required";
	}elseif(!$toCode){
		return "PinCode is required";
	}elseif(!$length){
		return "Package Length is required";
	}elseif(!$width){
		return "Package width is required";
	}elseif(!$height){
		return "Package height is required";
	}elseif(!$weight){
		return "Package weight is required";
	}else{

		require 'autoload.php'; // This autoloads RocketShipIt classes
		$shipment = new \RocketShipIt\Shipment('fedex');

		$shipment->setParameter('toCompany',$toCompany);
		$shipment->setParameter('toName', $toName);
		$shipment->setParameter('toPhone', $toPhone);
		$shipment->setParameter('toAddr1', $toAddr1);
		$shipment->setParameter('toCity', $toCity);
		$shipment->setParameter('toState', $toState);
		$shipment->setParameter('toCode', $toCode);

		$shipment->setParameter('length',$length);
		$shipment->setParameter('width',$width);
		$shipment->setParameter('height',$height);
		$shipment->setParameter('weight',$weight);

		$response = $shipment->submitShipment();
	//	print_r($response);

		if($response['tracking_id']){
			$data = base64_decode($response['label_img']);
			header('Content-type: application/pdf');
			header('Content-Disposition: attachment; filename="my.pdf"');
			echo $data;
			exit;
		}
	}
}

?>