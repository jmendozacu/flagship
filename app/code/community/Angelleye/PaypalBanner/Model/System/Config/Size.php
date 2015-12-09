<?php

class Angelleye_PaypalBanner_Model_System_Config_Size extends Varien_Object
{
    public static $sizes = array(
        "336x280", "280x280", "300x250",
        "190x100", "170x100", "150x100", "120x100",
        "800x66", "728x90", "540x200", "468x60", "234x60",
        "120x600", "234x400", "250x250", "120x240"
    );

    /**
     * Get options array for configuration field
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach(self::$sizes  as $size) {
            $options[] = array('value' => $size, 'label' => $size);
        }
        return $options;
    }
}
