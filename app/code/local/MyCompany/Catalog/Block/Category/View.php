<?php
        class MyCompany_Catalog_Block_Category_View extends Mage_Catalog_Block_Category_View
        {
            public function getFeaturedProductHtml()
            {
                return $this->getBlockHtml('product_featured');
            }
        }
    ?>
