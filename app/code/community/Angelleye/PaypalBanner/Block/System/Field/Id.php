<?php

class Angelleye_Paypalbanner_Block_System_Field_Id
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $element->setType('hidden');
        $value = is_object($element->getValue())  ?  (string) $element->getValue():$element->getValue();
        $success = '<div id="messages_success" style="width: 274px; display: '.( (empty($value)?'none':'block')).'"><ul class="messages"><li id="ppbanner_msg_holder_success" class="success-msg"><ul><li><span>'. $this->__('You\'re all set!') .'</span></li></ul></li></ul></div>';
        $error = '<div id="messages_error"  style="width: 274px;display: '.( (empty($value)?'block':'none')).'"><ul class="messages"><li id="ppbanner_msg_holder_failed" class="notice-msg"><ul><li><span>'. $this->__('You have not yet configured this module.<br /><br />Please fill in your name and email address below as it is in your PayPal account.<br /><br />Then agree to the terms and click the activate button.') .'</span></li></ul></li></ul></div>';
        return $success.$error.parent::_getElementHtml($element);
    }

}
