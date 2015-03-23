<?php

class Ip_Robots_Block_Developer extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $content.= '<div class="developer">';
        $content.= '</div>';
        return $content;
    }
}
