<?php

require 'autoload.php'; // This autoloads RocketShipIt classes

$shipment = new \RocketShipIt\Shipment('fedex');

$shipment->setParameter('toCompany', 'John Doe');
$shipment->setParameter('toName', 'John Doe');
$shipment->setParameter('toPhone', '1231231234');
$shipment->setParameter('toAddr1', '111 W Legion');
$shipment->setParameter('toCity', 'Whitehall');
$shipment->setParameter('toState', 'MT');
$shipment->setParameter('toCode', '59759');

$shipment->setParameter('length', '5');
$shipment->setParameter('width', '5');
$shipment->setParameter('height', '5');
$shipment->setParameter('weight','5');

$response = $shipment->submitShipment();
print_r($response);
?>