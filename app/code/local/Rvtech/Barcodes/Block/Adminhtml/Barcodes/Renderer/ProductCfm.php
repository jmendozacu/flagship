<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductCfm extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
        $productVal = Mage::getModel('catalog/product')->load($row->getProductId());
        $attributesCfm = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->addFieldToFilter('attribute_code', 'cfm') 
                            ->load();
           $attributeCfm = $attributesCfm->getFirstItem();

          $attrCfm = $attributeCfm->getSource()->getAllOptions(true);

          foreach ($attrCfm as $attrCfmval) {
              if($attrCfmval['value']==$productVal->getCfm())
               {
                   $CfmName = $attrCfmval['label'];
                    return $CfmName;
                }
            }
    }
}