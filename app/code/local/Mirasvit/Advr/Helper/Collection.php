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


class Mirasvit_Advr_Helper_Collection
{
    public function getPeriods($from, $to, $range)
    {
        $intervals = array();
        if (!$from && !$to){
            return $intervals;
        }

        $start = new Zend_Date($from, Varien_Date::DATE_INTERNAL_FORMAT);

        if ($range == '1d') {
            $dateStart = $start;
        }

        if ($range == '1w') {
            $w = date("w", $start->getTimestamp()) - 1;
            $dateStart = new Zend_Date(date("Y-m-d", $start->getTimestamp() - $w * 24 * 60 * 60), Varien_Date::DATE_INTERNAL_FORMAT);
        }

        if ($range == '1m') {
            $dateStart = new Zend_Date(date("Y-m", $start->getTimestamp()), Varien_Date::DATE_INTERNAL_FORMAT);
        }

        if ($range == '1q') {
            $month = intval($start->toString('M') / 4) * 3 + 1;
            $dateStart = new Zend_Date(date("Y-".$month, $start->getTimestamp()), Varien_Date::DATE_INTERNAL_FORMAT);
        }

        if ($range == '1y') {
            $dateStart = new Zend_Date(date("Y", $start->getTimestamp()), Varien_Date::DATE_INTERNAL_FORMAT);
        }

        $dateEnd = new Zend_Date($to, Varien_Date::DATE_INTERNAL_FORMAT);

        while ($dateStart->compare($dateEnd) <= 0) {
            switch ($range) {
                case '1d':
                    $t = $dateStart->toString('yyyy-MM-dd 00:00:00');
                    $dateStart->addDay(1);
                    break;
                case '1w':
                    $t = $dateStart->toString('yyyy-MM-dd 00:00:00');
                    $dateStart->addDay(7);
                    break;
                case '1m':
                    $t = $dateStart->toString('yyyy-MM-01 00:00:00');
                    $dateStart->addMonth(1);
                    break;
                case '1q':
                    $t = $dateStart->toString('yyyy-M-01 00:00:00');
                    $dateStart->addMonth(3);
                    break;
                case '1y':
                    $t = $dateStart->toString('yyyy-01-01 00:00:00');
                    $dateStart->addYear(1);
                    break;
            }

            $intervals[] = $t;
        }

        return  $intervals;
    }

    public function preparePeriodCollection($collection, $from, $to, $rangeType = '1d')
    {
        $intervals = $this->getPeriods($from, $to, $rangeType);

        foreach ($intervals as $interval) {
            $item = Mage::getModel('advr/collection_item');
            $item->setPeriod($interval);
            $item->setIsEmpty();
            $collection->addItem($item);
        }
    }

    public function prepareIntervalCollection($collection, $from, $to, $field)
    {
        for ($i = $from; $i < $to; $i++) {
            $item = Mage::getModel('advr/collection_item');
            $item->setData($field, $i);
            $item->setIsEmpty();
            $collection->addItem($item);
        }
    }

    public function prepareAttributeCollection($collection, $attrCode, $field)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attrCode);

        if ($attribute && $attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $opt) {
                $item = Mage::getModel('advr/collection_item');
                $item->setData($field, $opt['value']);
                $item->setIsEmpty();
                
                $collection->addItem($item);
            }
        }
    }
}