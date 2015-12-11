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

if (Mage::getConfig()->getModuleConfig('Mage_CanPostExport')->is('active', true)) {
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Mage_CanPostExport_Block_Sales_Order_Grid {}
} else if (Mage::getConfig()->getModuleConfig('Magemaven_OrderComment')->is('active', true)) {
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Magemaven_OrderComment_Block_Adminhtml_Sales_Order_Grid {}
} else if ((string)Mage::getConfig()->getModuleConfig('Extended_Ccsave')->active=='true'){
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Extended_Ccsave_Block_Adminhtml_Sales_Order_Grid {}
} else if ((string)Mage::getConfig()->getModuleConfig('Amasty_Email')->active=='true'){
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Amasty_Email_Block_Adminhtml_Sales_Order_Grid {}
} else if ((string)Mage::getConfig()->getModuleConfig('Amasty_Flags')->active=='true'){
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Amasty_Flags_Block_Rewrite_Adminhtml_Order_Grid {
        protected function _prepareAmastyColumns() {
            $flagCollection = Mage::getModel('amflags/flag')->getCollection();
            $flagFilterOptions = array();
            if ($flagCollection->getSize() > 0) {
                foreach ($flagCollection as $flag) {
                    $flagFilterOptions[$flag->getPriority()] = $flag->getAlias();
                }
            }

            $flagColumn = $this->getLayout()->createBlock('adminhtml/widget_grid_column')
                    ->setData(array(
                        'header' => Mage::helper('amflags')->__('Flag'),
                        'index' => 'priority',
                        'width' => '80px',
                        'align' => 'center',
                        'renderer' => 'amflags/adminhtml_renderer_flag',
                        'type' => 'options',
                        'options' => $flagFilterOptions,
                    ))
                    ->setGrid($this)
                    ->setId('flag_id');
            // adding flag column to the beginning of the columns array
            $flagColumnArray = array('flag_id' => $flagColumn);
            $this->_columns = $flagColumnArray + $this->_columns;
        }
    }
} else {
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Mage_Adminhtml_Block_Sales_Order_Grid {}    
}