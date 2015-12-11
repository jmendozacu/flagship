<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     439
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advr_Model_Report_Select_Column extends Varien_Object
{
    protected $_filter = null;

    public function _construct()
    {
        $this->setGrid(new Varien_Object());

        return $this;
    }

    public function getFilterHtml()
    {
        return $this->getFilter()->getHtml();
    }

    protected function _getFilterByType()
    {
        $type = strtolower($this->getType());
        
        switch ($type) {
            case 'datetime':
                $filterClass = 'adminhtml/widget_grid_column_filter_datetime';
                break;
            case 'date':
                $filterClass = 'adminhtml/widget_grid_column_filter_date';
                break;
            case 'range':
            case 'number':
            case 'currency':
                $filterClass = 'adminhtml/widget_grid_column_filter_range';
                break;
            case 'price':
                $filterClass = 'adminhtml/widget_grid_column_filter_price';
                break;
            case 'country':
                $filterClass = 'adminhtml/widget_grid_column_filter_country';
                break;
            case 'options':
                $filterClass = 'adminhtml/widget_grid_column_filter_select';
                break;

            case 'massaction':
                $filterClass = 'adminhtml/widget_grid_column_filter_massaction';
                break;

            case 'checkbox':
                $filterClass = 'adminhtml/widget_grid_column_filter_checkbox';
                break;

            case 'radio':
                $filterClass = 'adminhtml/widget_grid_column_filter_radio';
                break;
            case 'store':
                $filterClass = 'adminhtml/widget_grid_column_filter_store';
                break;
            case 'theme':
                $filterClass = 'adminhtml/widget_grid_column_filter_theme';
                break;
            default:
                $filterClass = 'adminhtml/widget_grid_column_filter_text';
                break;
        }

        return $filterClass;
    }

    public function getFilter()
    {
        if (!$this->_filter) {

            $filterClass = $this->_getFilterByType();
            
            $this->_filter = Mage::app()->getLayout()->createBlock($filterClass)
                ->setColumn($this)
                ->setValue($this->getValue());
        }

        return $this->_filter;
    }
}