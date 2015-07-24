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


class Mirasvit_Advr_Model_Collection
    extends Varien_Data_Collection
{
    protected $_columnGroupBy       = null;
    protected $_resourceCollection  = null;

    public function setColumnGroupBy($column)
    {
        $this->_columnGroupBy = (string) $column;

        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_setIsLoaded();
        if ($this->_columnGroupBy !== null) {
            $this->_mergeWithEmptyData();
            $this->_groupResourceData();
        }

        return $this;
    }

    public function setResourceCollection($collection)
    {
        $this->_resourceCollection = $collection;

        return $this;
    }

    public function getResourceCollection()
    {
        return $this->_resourceCollection;
    }

    protected function _mergeWithEmptyData()
    {
        if (count($this->_items) == 0) {
            return $this;
        }

        foreach ($this->_items as $key => $item) {
            foreach ($this->_resourceCollection as $dataItem) {
                if ($item->getData($this->_columnGroupBy) == $dataItem->getData($this->_columnGroupBy)) {
                    if (!$this->_items[$key] instanceof Mirasvit_Advr_Model_Collection_Item) {
                        $itm = Mage::getModel('advr/collection_item');
                        $itm->setData($this->_items[$key]->getData());
                        $this->_items[$key] = $itm;
                    }
                    if ($this->_items[$key]->getIsEmpty()) {
                        $this->_items[$key] = $dataItem;
                    } else {
                        $this->_items[$key]->addChild($dataItem);
                    }
                }
            }
        }

        $sortedItems = array();
        
        foreach ($this->_resourceCollection as $dataItem) {
            foreach ($this->_items as $key => $item) {
                if ($item->getData($this->_columnGroupBy) == $dataItem->getData($this->_columnGroupBy)) {
                    $sortedItems[] = $item;
                }
            }
        }

        $this->_items = $sortedItems;

        return $this;
    }

    protected function _groupResourceData()
    {
        if (count($this->_items) == 0) {
            foreach ($this->_resourceCollection as $item) {
                if (isset($this->_items[$item->getData($this->_columnGroupBy)])) {
                    
                    if (!$this->_items[$item->getData($this->_columnGroupBy)] instanceof Mirasvit_Advr_Model_Collection_Item) {
                        $itm = Mage::getModel('advr/collection_item');
                        $itm->setData($this->_items[$item->getData($this->_columnGroupBy)]->getData());
                        $this->_items[$item->getData($this->_columnGroupBy)] = $itm;
                    }

                    $this->_items[$item->getData($this->_columnGroupBy)]->addChild($item->setIsEmpty(false));
                } else {
                    $this->_items[$item->getData($this->_columnGroupBy)] = $item->setIsEmpty(false);
                }
            }
        }

        return $this;
    }


    public function merge($collection)
    {
        $idx = 0;
        foreach ($collection as $item) {
            $children = array();
            $children[] = $item;
            if (isset($this->_items[$idx])) {
                $_item = $this->_items[$idx];

                $_item['period'] .= '|'.$item['period'];
                $_item->setChildren($children);
            }

            $idx++;
        }
    }

    public function concat($collection, $prefix = '')
    {
        foreach ($collection as $item) {
            foreach ($this as $iitem) {
                if ($item['period'] == $iitem['period']) {
                    $iitem->addData($item->getData());
                }
            }
        }
    }

    public function getTotals()
    {
        $totals = array();

        $select = clone $this->_resourceCollection->getSelect();
        $select->limit(1000000);

        $rows = $this->_resourceCollection->getConnection()->fetchAll($select);

        foreach ($rows as $row) {
            foreach ($row as $k => $v) {
                if (!isset($totals[$k])) {
                    $totals[$k] = null;
                }

                $totals[$k] += $v;
                $totals[$k] = round($totals[$k], 2);
            }
        }

        return new Varien_Object($totals);
    }

    
    public function getSize()
    {
        return $this->_resourceCollection->getSize();
    }

    public function setOrder($field, $direction = 'DESC')
    {
        return $this->_resourceCollection->setOrder($field, $direction);
    }

    public function setPageSize($size)
    {
        return $this->_resourceCollection->setPageSize($size);
    }

    public function getPageSize()
    {
        return $this->_resourceCollection->getPageSize();
    }

    public function setCurPage($page)
    {
        return $this->_resourceCollection->setCurPage($page);
    }

    public function getCurPage($displacement = 0)
    {
        return $this->_resourceCollection->getCurPage($displacement);
    }

    public function getLastPageNumber()
    {
        return $this->_resourceCollection->getLastPageNumber();
    }

    public function addFieldToFilter($field, $condition = null)
    {
        return $this->_resourceCollection->addFieldToFilter($field, $condition);
    }
}
