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


class Mirasvit_Advr_Helper_Callback
{
    public function period($value, $row, $column)
    {
        $result = array();

        $values = explode('|', $value);

        foreach ($values as $val) {
            switch ($column->getGrid()->getFilterData()->getRange()) {
                default:
                case '1d':
                    $result[] = date('d M, Y', strtotime($val));
                    break;

                case '1w':
                    $result[] = date('d M, Y', strtotime($val) - 7 * 24 * 60 * 60).' – '.date('d M, Y', strtotime($val)).' ('.(date('W', strtotime($val)) - 1).')';
                    break;

                case '1m':
                    $result[] = date('M, Y', strtotime($val));
                    break;

                case '1q':
                    $result[] = date('M, Y', strtotime($val)).' – '.date('M, Y', strtotime($val) + 80 * 24 * 60 * 60);
                    break;

                case '1y':
                    $result[] = date('Y', strtotime($val));
            }
        }

        return implode('<br>', $result);
    }

    public function hour($value, $row, $column)
    {
        if (strlen($value) == 1) {
            $value = '0'.$value;
        }

        return $value.':00';
    }

    public function day($value, $row, $column)
    {
        $value += 1;
        
        return date('D', strtotime("Sunday +$value days"));
    }

    public function time($value, $row, $column)
    {
        $s = $value % 60;
        $m = floor(($value % 3600) / 60);
        $h = floor(($value % 86400) / 3600);
        $d = floor(($value % 2592000) / 86400);
        $M = floor($value / 2592000);

        $output = array();

        if ($M > 0) {
            $output []= "$M ".($M > 1 ? 'months' : 'month');
        }
        if ($d > 0) {
            $output []= "$d ".($d > 1 ? 'days' : 'day');
        }
        if ($h > 0) {
            $output []= "$h ".($h > 1 ? 'hours' : 'hour');
        }
        if ($m > 0) {
            $output []= "$m ".($m > 1 ? 'mins' : 'min');
        }

        return implode(' ', $output);
    }

    public function _percent($value, $row, $column)
    {
        $totals = $column->getGrid()->getTotals();

        $total = $totals->getData($column->getIndex()) ? $totals->getData($column->getIndex()) : 1;

        $result = $value / $total * 100;

        return sprintf("%.1f", $result);
    }

    public function percent($value, $row, $column)
    {
        return sprintf("%.1f %%", $this->_percent($value, $row, $column));
    }

    public function percentOf($value, $row, $column)
    {
        $of = $row->getData($column->getPercentOf());

        $of = $of ? $of : 1;

        $result = $value / $of;

        return '&nbsp;<small class="discount">'.sprintf("%.1f %%", $result * 100).'</small>'.$value;
    }

    public function discount($value, $row, $column)
    {
        $from = $row->getData($column->getData('discount_from'));
        $discount = $row->getData($column->getIndex());

        $percent = 0;
        if ($from > 0) {
            $percent = round($discount / $from * 100, 2);
        }

        if (abs($percent) > 100) {
            $width = 100;
        } else {
            $width = abs($percent);
        }

        return '<div class="percent-bar" style="width: '.abs($width).'%;"></div><div class="percent-value"><small class="discount">'.$percent.'%</small>&nbsp;&nbsp;&nbsp;'.$value.'</div>';
    }

    public function country($value, $row, $column)
    {
        $value = $row->getCountryId();
        
        $img = '';
        if ($value) {
            $img = '<img style="height:13px;width:19px;" src="http://www.geonames.org/flags/x/'.strtolower($value).'.gif">&nbsp;&nbsp;&nbsp;#';
        }
        return $img.Mage::app()->getLocale()->getCountryTranslation($value);
    }

    public function region($value, $row, $column)
    {
        if (intval($value) > 0) {
            $value = Mage::getModel('directory/region')->load($value)->getName();
        }

        return $value;
    }

    public function paymentMethod($value, $row, $column)
    {
        $methods = $this->_getPaymentMethods();
        if (isset($methods[$value])) {
            $value = $methods[$value];
        }   
        $value = strip_tags($value);
        
        return $value;
    }

    protected function _getPaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = $paymentTitle;
        }

        return $methods;
    }

    public function category($value, $row, $column)
    {
        $level = (int) $row->getCategoryLevel();

        $value = str_repeat('&nbsp;', $level * 5).$value;

        return $value;
    }

    public function multiCategory($value, $row, $column)
    {
        $result = array();
        $value = explode(',', $row->getData($column->getIndex()));
        foreach ($column->getOptions() as $val => $label){
            if (in_array($val, $value)) {
                $result[] = str_replace('-', '', $label);
            }
        }

        return implode(', ', $result);
    }

    public function linkToCustomer($value, $row, $column)
    {
        $link = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId()));

        return '<a href="'.$link.'">'.$value.'</a>';
    }

    public function rowUrl($url, $row, $filters = array())
    {
        $filter = array();
        foreach ($filters as $field) {
            if ($field == 'period') {
                $period = explode('|', $row->getData('period'));
                $periodFrom = strtotime($period[0]);
                $periodTo = $periodFrom;

                switch ($row->getData('range')) {
                    case '1w':
                        $periodTo += 7 * 24 * 60 * 60;
                        break;

                    case '1m':
                        $periodTo += 30 * 24 * 60 * 60;
                        break;

                    case '1q':
                        $periodTo += 80 * 24 * 60 * 60;
                        break;

                    case '1y':
                        $periodTo += 365 * 24 * 60 * 60;
                }

                $format = Mage::getSingleton('advr/config')->dateFormat();

                $from = new Zend_Date($periodFrom, null, Mage::app()->getLocale()->getLocaleCode());
                $to   = new Zend_Date($periodTo, null, Mage::app()->getLocale()->getLocaleCode());

                $filter = array(
                    'from' => $from->toString($format),
                    'to'   => $to->toString($format)
                ); 
            } else {
                $filter[$field] = $row->getData($field);
            }
        }

        $filter = base64_encode(http_build_query($filter));

        return Mage::helper('adminhtml')->getUrl($url, array('filter' => $filter));
    }
}