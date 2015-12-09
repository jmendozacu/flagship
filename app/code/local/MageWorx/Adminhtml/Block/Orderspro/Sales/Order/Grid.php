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

class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid extends MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract
{   
    
    public function __construct() {
        parent::__construct();
        $this->setDefaultFilter(array('order_group'=>0)); // Actual
    }
    
    protected function _prepareColumns() {
        $helper = Mage::helper('orderspro');
        if (!$helper->isEnabled()) return parent::_prepareColumns();        
        
        $listColumns = $helper->getGridColumns();        
        $currencyCode = $helper->getCurrentCurrencyCode();
        
        foreach ($listColumns as $column) {
            switch ($column) {
                
                // standard fields
                
                case 'real_order_id':
                    $this->addColumn('real_order_id', array(
                        'header'=> Mage::helper('sales')->__('Order #'),
                        'width' => '80px',
                        'type'  => 'text',
                        'index' => 'increment_id',
                    ));
                break;    

                case 'store_id':
                    if (!Mage::app()->isSingleStoreMode()) {
                        $this->addColumn('store_id', array(
                            'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                            'index'     => 'store_id',
                            'type'      => 'store',
                            'store_view'=> true,
                            'display_deleted' => true,
                        ));
                    }
                break;
                
                case 'created_at':
                    $this->addColumn('created_at', array(
                        'header' => Mage::helper('sales')->__('Purchased On'),
                        'index' => 'created_at',
                        'type' => 'datetime',
                        'width' => '100px',
                    ));
                break;   

                case 'billing_name':
                    $this->addColumn('billing_name', array(
                        'header' => Mage::helper('sales')->__('Bill to Name'),
                        'index' => 'billing_name',
                    ));
                break;    

                case 'shipping_name':
                    $this->addColumn('shipping_name', array(
                        'header' => Mage::helper('sales')->__('Ship to Name'),
                        'index' => 'shipping_name',
                    ));
                break;

                case 'base_grand_total':    
                    $this->addColumn('base_grand_total', array(
                        'header' => Mage::helper('sales')->__('G.T. (Base)'),
                        'index' => 'base_grand_total',
                        'type'  => 'currency',
                        'currency' => 'base_currency_code',
                    ));
                break;    

                    
                case 'grand_total':
                    $this->addColumn('grand_total', array(
                        'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
                        'index' => 'grand_total',
                        'type'  => 'currency',
                        'currency' => 'order_currency_code',
                    ));
                break;    
                    
                
                case 'status':
                    $this->addColumn('status', array(
                        'header' => Mage::helper('sales')->__('Status'),
                        'index' => 'status',
                        'type'  => 'options',
                        'width' => '70px',
                        'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
                    ));
                break;    
                    

                case 'action':    
                    if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
                        $this->addColumn('action',
                            array(
                                'header'    => Mage::helper('sales')->__('Action'),
                                'width'     => '50px',
                                'type'      => 'action',
                                'getter'     => 'getId',
                                'actions'   => array(
                                    array(
                                        'caption' => Mage::helper('sales')->__('View'),
                                        'url'     => array('base'=>'*/sales_order/view'),
                                        'field'   => 'order_id'
                                    )
                                ),
                                'filter'    => false,
                                'sortable'  => false,
                                'index'     => 'stores',
                                'is_system' => true,
                        ));
                    }
                break;
                
                // additional fields
                
                case 'product_names':                
                    $this->addColumn('product_names', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_products',
                        'header' => $helper->__('Product(s) Name(s)'),
                        'index' => 'name'                        
                        ));
                break;
            
                case 'product_skus':                
                    $this->addColumn('product_skus', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_products',
                        'header' => $helper->__('SKU(s)'),
                        'index' => 'skus'                        
                        ));
                break;


                case 'customer_email':
                    $this->addColumn('customer_email', array(
                        'type'  => 'text',
                        'header' => $helper->__('Customer Email'),
                        'index' => 'customer_email'
                        ));
                break;
            

                case 'customer_group':
                    $this->addColumn('customer_group', array(
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',
                        'type'  => 'options',
                        'options' => $this->getCustomerGroups(),
                        'header' => $helper->__('Customer Group'),
                        'index' => 'customer_group_id',
                        'align' => 'center'
                        ));
                break;    
                    

                case 'payment_method':
                    $this->addColumn('payment_method', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',
                        'type'  => 'options',
                        'options' => $helper->getAllPaymentMethods(),
                        'header' => $helper->__('Payment Method'),
                        'index' => 'method',
                        'align' => 'center'
                        ));
                break;
                    

                case 'total_refunded':
                    $this->addColumn('total_refunded', array(                            
                        'type'  => 'currency',
                        'currency_code' => $currencyCode,                
                        'header' => $helper->__('Total Refunded'),
                        'index' => 'total_refunded',
                        'total' => 'sum'
                        ));
                break;    
                    

                case 'shipping_method':
                    $this->addColumn('shipping_method', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',
                        'type'  => 'options',
                        'options' => $helper->getAllShippingMethods(),
                        'header' => $helper->__('Shipping Method'),
                        'index' => 'shipping_method',
                        'align' => 'center'
                        ));
                break;
            
            
                case 'shipped':
                    $this->addColumn('shipped', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',                
                        'type'  => 'options',
                        'options' => $helper->getShippedStatuses(),
                        'header' => $helper->__('Shipped'),
                        'index' => 'shipped',
                        'align' => 'center'
                        ));
                break;                    
                    
                case 'order_group':
                    $this->addColumn('order_group', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',                
                        'type'  => 'options',
                        'options' => $helper->getOrderGroups(),
                        'header' => $helper->__('Group'),
                        'index' => 'order_group_id',
                        'align' => 'center',                        
                        ));
                break;


                case 'qnty':
                    $this->addColumn('qnty', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_qnty',                
                        'filter'    => false,
                        'sortable'  => false,                
                        'header' => $helper->__('Qnty'),
                        'index' => 'total_qty',                
                        ));
                break;    

                    
                case 'tax_amount':
                    $this->addColumn('tax_amount', array(                            
                        'type'  => 'currency',
                        'currency_code' => $currencyCode,                
                        'header' => $helper->__('Tax Amount'),
                        'index' => 'tax_amount'
                        ));
                break;    
                    

//                case 'tax_percent':
//                    $this->addColumn('tax_percent', array(
//                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_percent',
//                        'type'  => 'number',                
//                        'header' => $helper->__('Tax Percent'),
//                        'index' => 'tax_percent', 
//                        'align' => 'right'
//                        ));
//                break;    
                    
                    
                case 'discount_amount':
                    $this->addColumn('discount_amount', array(                            
                        'type'  => 'currency',
                        'currency_code' => $currencyCode,                
                        'header' => $helper->__('Discount'),
                        'index' => 'discount_amount'                
                        ));
                break;
            
                case 'internal_credit':
                    if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                        $this->addColumn('internal_credit', array(
                            'type'  => 'currency',
                            'currency_code' => $currencyCode,
                            'header' => $helper->__('Internal Credit'),
                            'index' => 'customer_credit_amount'
                            ));
                    }
                break;
                
                case 'billing_company':
                    $this->addColumn('billing_company', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Bill to Company'),
                        'index' => 'billing_company',
                        'align' => 'center'
                        ));
                break;
            
                case 'shipping_company':
                    $this->addColumn('shipping_company', array(
                        'type'  => 'text',
                        'header' => $helper->__('Ship to Company'),
                        'index' => 'shipping_company',
                        'align' => 'center'
                        ));
                break;
            
                case 'billing_street':
                    $this->addColumn('billing_street', array(                            
                        'type'  => 'text',
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_street',
                        'header' => $helper->__('Bill to Street'),
                        'index' => 'billing_street',
                        'align' => 'center'
                        ));
                break;            
                case 'shipping_street':
                    $this->addColumn('shipping_street', array(
                        'type'  => 'text',
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_street',
                        'header' => $helper->__('Ship to Street'),
                        'index' => 'shipping_street',
                        'align' => 'center'
                        ));
                break;
            
            
                case 'billing_city':
                    $this->addColumn('billing_city', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Bill to City'),
                        'index' => 'billing_city',
                        'align' => 'center'
                        ));
                break;            
                case 'shipping_city':
                    $this->addColumn('shipping_city', array(
                        'type'  => 'text',
                        'header' => $helper->__('Ship to City'),
                        'index' => 'shipping_city',
                        'align' => 'center'
                        ));
                break;
            
                case 'billing_region':
                    $this->addColumn('billing_region', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Bill to State'),
                        'index' => 'billing_region',
                        'align' => 'center'
                        ));
                break;            
                case 'shipping_region':
                    $this->addColumn('shipping_region', array(
                        'type'  => 'text',
                        'header' => $helper->__('Ship to State'),
                        'index' => 'shipping_region',
                        'align' => 'center'
                        ));
                break;
            
                case 'billing_country':
                    $this->addColumn('billing_country', array(                            
                        'type'  => 'options',
                        'options' => $this->getCountryNames(),
                        'header' => $helper->__('Bill to Country'),
                        'index' => 'billing_country_id',
                        'align' => 'center'
                        ));
                break;
                case 'shipping_country':
                    $this->addColumn('shipping_country', array(
                        'type'  => 'options',
                        'header' => $helper->__('Ship to Country'),
                        'options' => $this->getCountryNames(),
                        'index' => 'shipping_country_id',
                        'align' => 'center'
                        ));
                break;
            
            
                case 'billing_postcode':
                    $this->addColumn('billing_postcode', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Billing Postcode'),
                        'index' => 'billing_postcode',
                        'align' => 'center'
                        ));
                break;
            
                case 'shipping_postcode':
                    $this->addColumn('shipping_postcode', array(
                        'type'  => 'text',
                        'header' => $helper->__('Shipping Postcode'),
                        'index' => 'shipping_postcode',
                        'align' => 'center'
                        ));
                break;
            
                case 'coupon_code':
                    $this->addColumn('coupon_code', array(
                        'type'  => 'text',
                        'header' => $helper->__('Coupon Code'),
                        'align' => 'center',
                        'index' => 'coupon_code'       
                        ));
                break;
            
                case 'is_edited':
                    $this->addColumn('is_edited', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',                
                        'type'  => 'options',
                        'options' => $helper->getEditedStatuses(),
                        'header' => $helper->__('Edited'),
                        'index' => 'is_edited',
                        'align' => 'center'
                        ));
                break;
            
            }            
        }
        
        if (method_exists($this, '_prepareAmastyColumns')) $this->_prepareAmastyColumns();
        
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
        
        $this->sortColumnsByOrder();
                
        return $this;
    }        
    
    
    
    public function getCountryNames() {
        if (Mage::registry('country_names')) return Mage::registry('country_names');
        
        $countryNames = array (
          'AD' => 'Andorra',
          'AE' => 'United Arab Emirates',
          'AF' => 'Afghanistan',
          'AG' => 'Antigua and Barbuda',
          'AI' => 'Anguilla',
          'AL' => 'Albania',
          'AM' => 'Armenia',
          'AN' => 'Netherlands Antilles',
          'AO' => 'Angola',
          'AR' => 'Argentina',
          'AT' => 'Austria',
          'AU' => 'Australia',
          'AW' => 'Aruba',
          'AX' => 'Aland Island (Finland)',
          'AZ' => 'Azerbaijan',
          'BA' => 'Bosnia-Herzegovina',
          'BB' => 'Barbados',
          'BD' => 'Bangladesh',
          'BE' => 'Belgium',
          'BF' => 'Burkina Faso',
          'BG' => 'Bulgaria',
          'BH' => 'Bahrain',
          'BI' => 'Burundi',
          'BJ' => 'Benin',
          'BM' => 'Bermuda',
          'BN' => 'Brunei Darussalam',
          'BO' => 'Bolivia',
          'BR' => 'Brazil',
          'BS' => 'Bahamas',
          'BT' => 'Bhutan',
          'BW' => 'Botswana',
          'BY' => 'Belarus',
          'BZ' => 'Belize',
          'CA' => 'Canada',
          'CC' => 'Cocos Island (Australia)',
          'CD' => 'Congo, Democratic Republic of the',
          'CF' => 'Central African Republic',
          'CG' => 'Congo, Republic of the',
          'CH' => 'Switzerland',
          'CI' => 'Cote d Ivoire (Ivory Coast)',
          'CK' => 'Cook Islands (New Zealand)',
          'CL' => 'Chile',
          'CM' => 'Cameroon',
          'CN' => 'China',
          'CO' => 'Colombia',
          'CR' => 'Costa Rica',
          'CU' => 'Cuba',
          'CV' => 'Cape Verde',
          'CX' => 'Christmas Island (Australia)',
          'CY' => 'Cyprus',
          'CZ' => 'Czech Republic',
          'DE' => 'Germany',
          'DJ' => 'Djibouti',
          'DK' => 'Denmark',
          'DM' => 'Dominica',
          'DO' => 'Dominican Republic',
          'DZ' => 'Algeria',
          'EC' => 'Ecuador',
          'EE' => 'Estonia',
          'EG' => 'Egypt',
          'ER' => 'Eritrea',
          'ES' => 'Spain',
          'ET' => 'Ethiopia',
          'FI' => 'Finland',
          'FJ' => 'Fiji',
          'FK' => 'Falkland Islands',
          'FM' => 'Micronesia, Federated States of',
          'FO' => 'Faroe Islands',
          'FR' => 'France',
          'GA' => 'Gabon',
          'GB' => 'Great Britain and Northern Ireland',
          'GD' => 'Grenada',
          'GE' => 'Georgia, Republic of',
          'GF' => 'French Guiana',
          'GH' => 'Ghana',
          'GI' => 'Gibraltar',
          'GL' => 'Greenland',
          'GM' => 'Gambia',
          'GN' => 'Guinea',
          'GP' => 'Guadeloupe',
          'GQ' => 'Equatorial Guinea',
          'GR' => 'Greece',
          'GS' => 'South Georgia (Falkland Islands)',
          'GT' => 'Guatemala',
          'GW' => 'Guinea-Bissau',
          'GY' => 'Guyana',
          'HK' => 'Hong Kong',
          'HN' => 'Honduras',
          'HR' => 'Croatia',
          'HT' => 'Haiti',
          'HU' => 'Hungary',
          'ID' => 'Indonesia',
          'IE' => 'Ireland',
          'IL' => 'Israel',
          'IN' => 'India',
          'IQ' => 'Iraq',
          'IR' => 'Iran',
          'IS' => 'Iceland',
          'IT' => 'Italy',
          'JM' => 'Jamaica',
          'JO' => 'Jordan',
          'JP' => 'Japan',
          'KE' => 'Kenya',
          'KG' => 'Kyrgyzstan',
          'KH' => 'Cambodia',
          'KI' => 'Kiribati',
          'KM' => 'Comoros',
          'KN' => 'Saint Kitts (St. Christopher and Nevis)',
          'KP' => 'North Korea (Korea, Democratic People\'s Republic of)',
          'KR' => 'South Korea (Korea, Republic of)',
          'KW' => 'Kuwait',
          'KY' => 'Cayman Islands',
          'KZ' => 'Kazakhstan',
          'LA' => 'Laos',
          'LB' => 'Lebanon',
          'LC' => 'Saint Lucia',
          'LI' => 'Liechtenstein',
          'LK' => 'Sri Lanka',
          'LR' => 'Liberia',
          'LS' => 'Lesotho',
          'LT' => 'Lithuania',
          'LU' => 'Luxembourg',
          'LV' => 'Latvia',
          'LY' => 'Libya',
          'MA' => 'Morocco',
          'MC' => 'Monaco (France)',
          'MD' => 'Moldova',
          'MG' => 'Madagascar',
          'MK' => 'Macedonia, Republic of',
          'ML' => 'Mali',
          'MM' => 'Burma',
          'MN' => 'Mongolia',
          'MO' => 'Macao',
          'MQ' => 'Martinique',
          'MR' => 'Mauritania',
          'MS' => 'Montserrat',
          'MT' => 'Malta',
          'MU' => 'Mauritius',
          'MV' => 'Maldives',
          'MW' => 'Malawi',
          'MX' => 'Mexico',
          'MY' => 'Malaysia',
          'MZ' => 'Mozambique',
          'NA' => 'Namibia',
          'NC' => 'New Caledonia',
          'NE' => 'Niger',
          'NG' => 'Nigeria',
          'NI' => 'Nicaragua',
          'NL' => 'Netherlands',
          'NO' => 'Norway',
          'NP' => 'Nepal',
          'NR' => 'Nauru',
          'NZ' => 'New Zealand',
          'OM' => 'Oman',
          'PA' => 'Panama',
          'PE' => 'Peru',
          'PF' => 'French Polynesia',
          'PG' => 'Papua New Guinea',
          'PH' => 'Philippines',
          'PK' => 'Pakistan',
          'PL' => 'Poland',
          'PM' => 'Saint Pierre and Miquelon',
          'PN' => 'Pitcairn Island',
          'PT' => 'Portugal',
          'PY' => 'Paraguay',
          'QA' => 'Qatar',
          'RE' => 'Reunion',
          'RO' => 'Romania',
          'RS' => 'Serbia',
          'RU' => 'Russia',
          'RW' => 'Rwanda',
          'SA' => 'Saudi Arabia',
          'SB' => 'Solomon Islands',
          'SC' => 'Seychelles',
          'SD' => 'Sudan',
          'SE' => 'Sweden',
          'SG' => 'Singapore',
          'SH' => 'Saint Helena',
          'SI' => 'Slovenia',
          'SK' => 'Slovak Republic',
          'SL' => 'Sierra Leone',
          'SM' => 'San Marino',
          'SN' => 'Senegal',
          'SO' => 'Somalia',
          'SR' => 'Suriname',
          'ST' => 'Sao Tome and Principe',
          'SV' => 'El Salvador',
          'SY' => 'Syrian Arab Republic',
          'SZ' => 'Swaziland',
          'TC' => 'Turks and Caicos Islands',
          'TD' => 'Chad',
          'TG' => 'Togo',
          'TH' => 'Thailand',
          'TJ' => 'Tajikistan',
          'TK' => 'Tokelau (Union) Group (Western Samoa)',
          'TL' => 'East Timor (Indonesia)',
          'TM' => 'Turkmenistan',
          'TN' => 'Tunisia',
          'TO' => 'Tonga',
          'TR' => 'Turkey',
          'TT' => 'Trinidad and Tobago',
          'TV' => 'Tuvalu',
          'TW' => 'Taiwan',
          'TZ' => 'Tanzania',
          'UA' => 'Ukraine',
          'UG' => 'Uganda',
          'UY' => 'Uruguay',
          'UZ' => 'Uzbekistan',
          'VA' => 'Vatican City',
          'VC' => 'Saint Vincent and the Grenadines',
          'VE' => 'Venezuela',
          'VG' => 'British Virgin Islands',
          'VN' => 'Vietnam',
          'VU' => 'Vanuatu',
          'WF' => 'Wallis and Futuna Islands',
          'WS' => 'Western Samoa',
          'YE' => 'Yemen',
          'YT' => 'Mayotte (France)',
          'ZA' => 'South Africa',
          'ZM' => 'Zambia',
          'ZW' => 'Zimbabwe',
        );
        asort($countryNames);
        Mage::register('country_names', $countryNames);
        return $countryNames;
    }
    
    protected function _prepareMassaction() {
        parent::_prepareMassaction();
        $block = $this->getMassactionBlock();
        
        if (Mage::helper('orderspro')->isEnabled()) {                
            
            if (Mage::helper('orderspro')->isEnableInvoiceOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/invoice')) {            
                $block->addItem('invoice_order', array(
                     'label'=> Mage::helper('orderspro')->__('Invoice'),
                     'url'  => $this->getUrl('*/sales_order/massInvoice'),
                ));
            }
            
            if (Mage::helper('orderspro')->isEnableShipOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/ship')) {            
                $block->addItem('ship_order', array(
                     'label'=> Mage::helper('orderspro')->__('Ship'),
                     'url'  => $this->getUrl('*/sales_order/massShip'),
                ));
            }
            
            if (Mage::helper('orderspro')->isEnableInvoiceOrders() && Mage::helper('orderspro')->isEnableShipOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/invoice_and_ship')) {            
                $block->addItem('invoice_and_ship_order', array(
                     'label'=> Mage::helper('orderspro')->__('Invoice+Ship'),
                     'url'  => $this->getUrl('*/sales_order/massInvoiceAndShip'),
                ));
            }
            
            if (Mage::helper('orderspro')->isEnableArchiveOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/archive')) {            
                $this->getMassactionBlock()->addItem('archive_order', array(
                     'label'=> Mage::helper('orderspro')->__('Archive'),
                     'url'  => $this->getUrl('*/sales_order/massArchive'),
                ));
            }
        

            if (Mage::helper('orderspro')->isEnableDeleteOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/delete')) {
                $block->addItem('delete_order', array(
                     'label'=> Mage::helper('orderspro')->__('Delete'),
                     'url'  => $this->getUrl('*/sales_order/massDelete'),
                ));
            }
            
            if (Mage::helper('orderspro')->isEnableDeleteOrdersCompletely() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/delete_completely')) {
                $block->addItem('delete_order_completely', array(
                     'label'=> Mage::helper('orderspro')->__('Delete Completely'),
                     'url'  => $this->getUrl('*/sales_order/massDeleteCompletely'),
                ));
            }
            
            
            if ((Mage::helper('orderspro')->isEnableArchiveOrders() || Mage::helper('orderspro')->isEnableDeleteOrders()) && (Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/archive') || Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/delete'))) {
                $block->addItem('restore_order', array(
                     'label'=> Mage::helper('orderspro')->__('Restore'),
                     'url'  => $this->getUrl('*/sales_order/massRestore'),
                ));
            }    
            
        }
        
        
        if (method_exists($this, '_prepareAmastyColumns')) {
            $block->addItem('amflags_separator_pre' . $i, array(
                'label'=> '---------------------',
                'url'  => '' 
            )); 

            $collection = Mage::getModel('amflags/flag')->getCollection();
            foreach ($collection as $flag) {
                $block->addItem('amflags_apply_' . $flag->getEntityId(), array(
                    'label'      => 'Apply "' . $flag->getAlias() . '" Flag',
                    'url'        => Mage::helper('adminhtml')->getUrl('amflags/adminhtml_flag/massApply', array('flag' => $flag->getEntityId())), 
                ));
            }

            $block->addItem('amflags_remove', array(
                'label'      => 'Remove Flag',
                'url'        => Mage::helper('adminhtml')->getUrl('amflags/adminhtml_flag/massApply', array('flag' => 0)), 
            ));

            $block->addItem('amflags_separator_post' . $i, array(
                'label'=> '---------------------',
                'url'  => '' 
            )); 
        }
        
        return $this;
    }
    

}   