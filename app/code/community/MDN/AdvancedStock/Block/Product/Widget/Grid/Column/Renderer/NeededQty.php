<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_NeededQty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $retour = $row->getNeededQty();
        $retour .= ' (' . $row->getNeededQtyForValidOrders() . ')';
        return $retour;
    }

}