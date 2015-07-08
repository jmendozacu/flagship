<?php
class Ecommerceguys_Inventorymanager_Adminhtml_LabelController extends Mage_Adminhtml_Controller_action
{
	public function serialgridAction(){
		 $productGridBlock = $this->getLayout()->createBlock('inventorymanager/adminhtml_product_labels', 'label_product');
		 //$this->getResponse()->setBody($productGridBlock->toHtml());
		 echo $productGridBlock->toHtml();
	}
}