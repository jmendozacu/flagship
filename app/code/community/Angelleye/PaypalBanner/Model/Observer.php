<?php
class Angelleye_PaypalBanner_Model_Observer
{
    /**
     * Update page layout, add banner code.
     *
     * @param Varien_Event_Observer $observer
     * @return Leiribits_Invoicexpress_Model_Observer
     */
    public function layoutGenerateBlocksBefore(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig('paypalbanner/settings/active')) {
            return '';
        }

        if (Mage::app()->getStore()->isAdmin()) {
            return;
        }
        $config = Mage::helper('paypalbanner')->getSectionConfig();

        if ($config->getDisplay()) {
            switch ($config->getPositionHorizontal()) {
                case 'left': $name = 'left'; break;
                case 'right': $name = 'right'; break;
                default: $name = 'content'; break;
            }
            $vertical = ('top'==$config->getPositionVertical() ? 'before="-"' : 'after="+"');
            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addUpdate(
                '<reference name="'.$name.'">
                    <block type="paypalbanner/banner" name="paypalbanner" template="paypalbanner/snippet.phtml" '.$vertical.'/>
                </reference>');
        }

        return $this;
    }
}