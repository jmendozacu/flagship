<?php
class Ecommerceguys_Inventorymanager_Block_Adminhtml_Sales_Order_View_Tabs extends Mage_Adminhtml_Block_Sales_Order_View_Tabs
{

    /**
     * Retrieve available order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        }
        Mage::throwException(Mage::helper('sales')->__('Cannot get the order instance.'));
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_view_tabs');
        $this->setDestElementId('sales_order_view');
        $this->setTitle(Mage::helper('sales')->__('Order View'));
    }
	protected function _beforeToHtml()
    {
    	
    	parent::_beforeToHtml();

    	
		//Rajoute l'onglet pour les tasks
        if ($this->getOrder()->getId()) {
        	
        	
            $this->addTab('serials', array(
                'label'     => Mage::helper('inventorymanager')->__('Serials'),
                'title'     => Mage::helper('inventorymanager')->__('Serials'),
                'url'       => $this->getUrl('inventorymanager/adminhtml_label/ordergrid', array('_current' => true)),
                 'class'     => 'ajax',
            ));
        }

        return parent::_beforeToHtml();
    }
}