<?php
class Manv_Responsivebannerslider_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Manv_Responsivebannerslider"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("manv_responsivebannerslider", array(
                "label" => $this->__("Manv_Responsivebannerslider"),
                "title" => $this->__("Manv_Responsivebannerslider")
		   ));

      $this->renderLayout(); 
	  
    }
}