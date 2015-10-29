<?php
class Rvtech_Barcodes_Block_Myblock extends Mage_Core_Block_Template
{
     public function methodblock()
     {
        $codes ='';
     	$collection = Mage::getModel('barcodes/barcodes')
                    ->getCollection()
                    ->setOrder('id','asc');
        foreach($collection as $data)
        {
             $codes .= $data->getData('purchase_order').' '.$data->getData('date')
                     .' '.$data->getData('factory').' '.$data->getData('product').' '.$data->getData('barcode').'<br />';
         }
         //i return a success message to the user thanks to the Session.
         Mage::getSingleton('adminhtml/session')->addSuccess('You got it right Boy !!');
         return $codes;
     }
}