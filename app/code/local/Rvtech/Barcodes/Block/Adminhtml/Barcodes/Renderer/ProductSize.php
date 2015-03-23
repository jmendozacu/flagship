<?php
class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Renderer_ProductSize extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
    {
        $productVal = Mage::getModel('catalog/product')->load($row->getProductId());
        $attributesSize = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->addFieldToFilter('attribute_code', 'hood_width_inches') 
                            ->load();
         $attributeSize = $attributesSize->getFirstItem();

        $attrSize = $attributeSize->getSource()->getAllOptions(true);

        foreach ($attrSize as $attrSizeval) {
            if($attrSizeval['value']==$productVal->getHoodWidthInches())
             {
                  $SizeName = $attrSizeval['label'];
                  return $SizeName;
              }
          }
         
    }
}