<?php 
$storeCode = Mage::app()->getStore()->getCode();
if($storeCode == "directrangehoods"){
	require_once(Mage::getModuleDir('controllers','AnattaDesign_AwesomeCheckout').DS.'OnepageController.php');
	class AnattaDesign_AwesomeCheckout_ExtendController extends AnattaDesign_AwesomeCheckout_OnepageController
	{
		
	}
}else{
	require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'OnepageController.php');
	class AnattaDesign_AwesomeCheckout_ExtendController extends Mage_Checkout_OnepageController
	{
		
	}
}