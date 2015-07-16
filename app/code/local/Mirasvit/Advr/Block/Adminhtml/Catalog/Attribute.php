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


class Mirasvit_Advr_Block_Adminhtml_Catalog_Attribute 
    extends Mirasvit_Advr_Block_Adminhtml_Catalog_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $this->setHeaderText(Mage::helper('advr')->__('Sales by Attribute'));

        return $this;
    }

    protected function _prepareChart()
    {
        $this->setChartType('pie');

        $this->_initChart()
            ->setNameField('product_attribute_'.$this->_getAttributeCode())
            ->setValueField('sum_item_row_total')
            ;

        return $this;
    }

    protected function _prepareGrid()
    {
        $this->_initGrid()
            ->setDefaultSort('sum_item_row_total')
            ->setDefaultDir('desc')
            ->setDefaultLimit(1000)
            ->setPagerVisibility(false)
            ;

        return $this;
    }

    protected function _prepareToolbar()
    {
        $this->_initToolbar()
            ;

        $this->getToolbar()->getForm()->addField('group_by_attribute', 'select', array(
            'name'    => 'group_by_attribute',
            'label'   => Mage::helper('advr')->__('Group By Attribute'),
            'values'  => Mage::getSingleton('advr/system_config_source_productAttribute')->toOptionArray(),
            'value'   => $this->_getAttributeCode(),
        ));

        $this->getToolbar()->getForm()->addField('include_child', 'checkbox', array(
            'name'    => 'include_child',
            'label'   => Mage::helper('advr')->__('Include child products'),
            'value'   => 1,
            'checked' => $this->getIncludeChild(),
        ));

        return $this;
    }

    protected function _prepareCollection()
    {
        $attribute = $this->getFilterData()->getGroupByAttribute();

        if (!$attribute) {
            $attribute = 'status'; 
        }

        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('catalog/product')
            ->setFilterData($this->getFilterData())
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('product_attribute_'.$attribute)
            ;

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'product_attribute_'.$this->_getAttributeCode() => array(
                'header'          => $this->_getAttribute()->getFrontendLabel(),
                'type'            => 'text',
                'totals_label'    => 'Total',
                'frame_callback'  => array($this, 'frameCallbackAttribute'),
                'chart'           => true,
                'export_callback' => array($this, 'frameCallbackAttribute'),
            ),
        );

        $columns += $this->getBaseProductColumns(true);

        $columns['actions'] = array(
            'header'       => 'Actions',
            'actions'      => array(
                array(
                    'caption'  => Mage::helper('advr')->__('Detail'),
                    'callback' => array($this, 'detailUrlCallback')
                ),
            ),
        );


        return $columns;
    }

    public function frameCallbackAttribute($value, $row, $column)
    {
        $attribute = $this->_getAttribute();
        if ($attribute && $attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
            foreach ($options as $opt) {
                if ($opt['value'] == $value) {
                    return $opt['label'];
                }
            }

            return Mage::helper('advr')->__('not set');
        }
        
        return Mage::helper('core/string')->truncate($value, 50);
    }

    protected function _getAttributeCode()
    {
        $code = $this->getFilterData()->getGroupByAttribute();
        
        if (!$code) {
            $code = 'status';
        } 

        return $code;
    }

    protected function _getAttribute()
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $this->_getAttributeCode());

        return $attribute;
    }

    public function detailUrlCallback($row)
    {
        if ($value = $row->getData('product_attribute_'.$this->_getAttributeCode())) {
            $url = $this->getUrl('advradmin/adminhtml_catalog/attributeDetail', array(
                    'attribute_code'  => $this->_getAttribute()->getAttributeCode(),
                    'attribute_value' => $value,
                )
            );

            return $url;
        }

        return false;
    }

    public function getIncludeChild()
    {
        if (!$this->getFilterData()->getIncludeChild()) {
            return 0;
        }

        return $this->getFilterData()->getIncludeChild();
    }
}