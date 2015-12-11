<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
        $productId = $row->getId();
		$SerialNumber = Mage::getModel('barcodes/barcodes')
					->load($productId)
					->getData('dzv_serial');
        $productEdit = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'barcode_admin/adminhtml_barcodes/edit/id/'.$productId;
        return '<a href="'.$productEdit.'">'.$SerialNumber.'</a>';
    }
}