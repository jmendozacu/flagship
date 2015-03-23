<?php

class Angelleye_PaypalBanner_Model_System_Config_Position extends Varien_Object
{
    public static $positions = array(
        "left-top",
        "left-bottom",
        "center-top",
        "center-bottom",
        "right-top",
        "right-bottom"
    );

    /**
     * Get options array for configuration field
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach(self::$positions as $position) {
            $options[] = array('value' => $position, 'label' => $position);
        }
        return $options;
    }
}
