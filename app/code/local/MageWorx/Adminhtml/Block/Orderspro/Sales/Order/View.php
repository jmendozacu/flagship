<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_View extends MageWorx_Adminhtml_Block_Orderspro_Sales_Order_View_Abstract
{
    public function __construct() {
        parent::__construct();
        $order = $this->getOrder();
        if ($this->_isAllowedAction('edit') && $order->canEdit()) {
            if ($order->canInvoice()) $confirm = 0; else $confirm = 1;
            $this->_updateButton('order_edit', 'onclick', 'confirmEdit(event, '.$confirm.', \''.$this->getEditUrl().'\', '.$order->getId().')');            
        }
    }
    
    public function getHeaderText() {
        $text = parent::getHeaderText();
        if ($this->getOrder()->getIsEdited()) $text .= ' ('.Mage::helper('orderspro')->__('Edited').')';        
        return $text;
    }    
}
