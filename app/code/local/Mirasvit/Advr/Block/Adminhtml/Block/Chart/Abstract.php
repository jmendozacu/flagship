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


class Mirasvit_Advr_Block_Adminhtml_Block_Chart_Abstract extends Mage_Adminhtml_Block_Template
{
    protected $_collection = array();
    protected $_options    = array();
    protected $_columns    = array();

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function addOption($name, $value)
    {
        $this->_options[$name] = $value;

        return $this;
    }

    public function getOptionsAsJson()
    {
        return Zend_Json::encode($this->_options);
    }

    public function addColumn($label, $field, $type = 'string')
    {
        $this->_columns[] = new Varien_Object(array(
            'label' => $label,
            'field' => $field,
            'type'  => $type,
        ));

        return $this;
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function setColumns($columns)
    {
        $this->_columns = $columns;

        return $this;
    }

    public function resetColumns()
    {
        $this->_columns = array();

        return $this;
    }

    protected function _castValue($column, $value)
    {
        switch ($column->getType()) {
            case 'label':
            case 'string':
                $value = ''.$value.'';
                break;
            
            case 'number':
            case 'decimal':
            case 'float':
                $value = floatval($value);
                break;
        }

        return $value;
    }

    public function getColumnTypes()
    {
        $types = array();
        foreach ($this->_columns as $column) {
            if ($this->_isColumnAllowed($column)) {
                $types[] = $column['type'];
            }
        }

        return $types;
    }

    public function getColumnColors()
    {
        $colors = array();
        foreach ($this->_columns as $index => $column) {
            if ($this->_isColumnAllowed($column)) {
                $colors[] = Mage::getSingleton('advr/config')->getChartColumnColor($index);
            }
        }

        return $colors;
    }

    public function getCollection()
    {
        if ($this->_collection == null) {
            $this->_collection = $this->getData('collection');
            $this->_collection->setPageSize(10000)
                ->clear();
        }

        return $this->_collection;
    }

    protected function _isColumnAllowed($column)
    {
        if (!in_array($column->getType(), array('number', 'currency'))) {
            return false;
        }

        if ($column->getChart() === 'none') {
            return false;
        }

        return true;
    }
}