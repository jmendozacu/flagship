<?php
$wsdl = '/var/www/html/magento.aivector.com/html/app/code/core/Mage/Usa/etc/wsdl/FedEx/RateService_v9.wsdl';
$client = new SoapClient($wsdl, array('trace' => $trace));
$client->__setLocation('https://gatewaybeta.fedex.com/GatewayDC');

echo "Client: $client\n";
?>
