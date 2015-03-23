<?php
class Rvtech_Purchaseorder_Block_Adminhtml_Purchaseorder_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
        $productId = $row->getId();
        $poId = base64_encode($row->getPurchaseOrder());
		$SerialNumber = Mage::getModel('barcodes/barcodes')
					->load($productId)
					->getData('dzv_serial');
        $po_edit = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'barcode_admin/adminhtml_barcodes/index/filter/'.$poId.'/';
        return '<a href="'.$po_edit.'">View All</a>';
    }
}