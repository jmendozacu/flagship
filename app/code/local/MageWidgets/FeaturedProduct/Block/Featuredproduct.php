<?php
/**
 * Featuredproduct.php
 * MageWidgets @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magewidgets.com/LICENSE-M1.txt
 *
 * @category   Catalog
 * @package    Block_Product_List
 * @copyright  Copyright (c) 2003-2009 MageWidgets @ InterSEC Solutions LLC. (http://www.magewidgets.com)
 * @license    http://www.magewidgets.com/LICENSE-M1.txt
 */ 
 
class MageWidgets_FeaturedProduct_Block_Featuredproduct
extends Mage_Catalog_Block_Product_List
implements Mage_Widget_Block_Interface
{		
		protected $_defaultToolbarBlock = 'featuredproduct/list_toolbar';
		/**
     * Initialize block's cache
     */
		protected function _construct()
    {
				parent::_construct();
        $this->addData(array(
            'cache_lifetime'    => $this->getCacheLifetime(),
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
    }

    /**
     * Retrieve Key for caching block content
     *
     * @return string
     */
    public function getCacheKey()
    {
        return 'MAGEWIDGETS_FEATUREDPRODUCTS_' . Mage::app()->getStore()->getId()
            . '_' . Mage::getDesign()->getPackageName()
            . '_' . Mage::getDesign()->getTheme('template')
            . '_' . Mage::getSingleton('customer/session')->getCustomerGroupId()
            . '_' . md5($this->getTemplate())
            . '_' . $this->getDisplayMode()
            . '_' . $this->getProductsLimit();
    }

    public function getCacheLifetime()
    {
        $time = $this->getData('cache_lifetime');
        if (!$time) {
            $time = Mage::getStoreConfig('featuredproduct/featuredproduct/cache_lifetime');
        }
        if (!$time) {
            $time = 86400;
        }
        return $time;
    }
		
		protected function _beforeToHtml() {
        $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
        return parent::_beforeToHtml();    
    }
		
		public function _toHtml()
    {
        if ($this->_productCollection->count()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
		
		public function isShowOutOfStock() {
        return (bool)Mage::getStoreConfig('featuredproduct/featuredproduct/show_out_of_stock'); //global - system level
    }
		
    public function getProductsLimit()
    {
				$hastoolbar = (bool)$this->getData('has_toolbar'); // widget level		
				if ($hastoolbar) {
					  return $this->getRequest()->getParam('limit');
				}
        if ($this->getData('limit')) {
            return intval($this->getData('limit'));
        } else {
            return $this->getToolbarBlock()->getLimit();
        }
    }
		
		
    public function getProductsSortOrder()
    {
				$hastoolbar = (bool)$this->getData('has_toolbar'); // widget level		
				if ($hastoolbar) {
					  return $this->getRequest()->getParam('order');
				}
				return 'featured';
		}
    public function getProductsSortBy()
    {
				$hastoolbar = (bool)$this->getData('has_toolbar'); // widget level		
				if ($hastoolbar) {
					  return $this->getRequest()->getParam('dir');
				}
				return 'asc';
		}
		
    public function getColumnLimit()
    {
        if ($this->getData('columnlimit')) {
            return intval($this->getData('columnlimit'));
        } else {
            return $this->getColumnCount();
        }
    }

    public function getDisplayMode()
    {
				$mode = $this->getRequest()->getParam('mode');
        if ($mode) {
						return $mode;
        } else {
						return $this->getData('display_mode');
				}
    }
		
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
						$layer = Mage::getModel('catalog/layer');
					  $this->setColumnCount($this->getColumnLimit());
				
						$collection = Mage::getResourceModel('catalog/product_collection');
						
        		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
						
						if ($this->getData('filter_by_category')) {
                if ($this->getData('category_id')) {
                    $category = Mage::getModel('catalog/category')->load($this->getData('category_id'));
                    $collection->addCategoryFilter($category);
                    $collection->addUrlRewrite($category->getId());
                } else {    
                    $collection->addCategoryFilter($layer->getCurrentCategory());
                    $collection->addUrlRewrite($layer->getCurrentCategory()->getId());
                }
            }
						
            if (!$this->isShowOutOfStock()) {
                Mage::getModel('cataloginventory/stock')->addInStockFilterToCollection($collection);
            }
						
        		$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        		$collectionfeaturedproducts = $collection->addStoreFilter(Mage::app()->getStore()->getId())
						->addAttributeToSelect($attributes)
            ->addAttributeToFilter('featured', array('Yes' => true))
						->addAttributeToSort($this->getProductsSortOrder(), $this->getProductsSortBy())
						->addMinimalPrice()
            ->addFinalPrice()
						->setPageSize($this->getProductsLimit());
						
            Mage::getModel('catalog/layer')->prepareProductCollection($collectionfeaturedproducts);

            $this->_productCollection = $collectionfeaturedproducts;
            $this->_productCollection->load();
        }
        return $this->_productCollection;
    }

    /**
     * Translate block sentence
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), 'Mage_Catalog');
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }
}