<?php
class Ecommerceguys_Inventorymanager_Model_Observer
{
	public function addMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
       // echo get_class($block) . "<br/>";
        if((get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction')
            && ($block->getRequest()->getControllerName() == 'orderspro_order' || $block->getRequest()->getControllerName() == 'sales_order'))
        {
            $block->addItem('print_order', array(
                'label' => 'Print Order',
                'url' => Mage::app()->getStore()->getUrl('inventorymanager/adminhtml_order/print'),
            ));
        }
    }
}