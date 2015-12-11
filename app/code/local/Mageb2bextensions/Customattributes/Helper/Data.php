<?php
/**
 * Data.php
 * MageB2BExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageb2bextensions.com/LICENSE-M1.txt
 *
 * @package    Mageb2bextensions_Customattributes
 * @copyright  Copyright (c) 2003-2009 MageB2BExtensions @ InterSEC Solutions LLC. (http://www.mageb2bextensions.com)
 * @license    http://www.mageb2bextensions.com/LICENSE-M1.txt
 */

class Mageb2bextensions_Customattributes_Helper_Data extends Mage_Core_Helper_Abstract
{
	function getStepData($sType, $sMode = 'full')
    {
        if (!$sType) return false;
        
        switch ($sType)
        {
						case 'registerpage':
                $aStepData = array
                (
                    array
                    (
                        'value' => '',
                        'label' => $this->__('None')
                    ),
                    array
                    (
                        'value' => 1,
                        'label' => $this->__('Customer Registration Form')
                    ),
                );
            break;
						case 'customereditpage':
                $aStepData = array
                (
                    array
                    (
                        'value' => '',
                        'label' => $this->__('None')
                    ),
                    array
                    (
                        'value' => 1,
                        'label' => $this->__('Customer Registration Form')
                    ),
                );
            break;
        }
        
        if ($sMode == 'hash')
        {
            $aStepHash = array();
            
            foreach ($aStepData as $aItem)
            {
                if ($aItem['value'])
                {
                    $aStepHash[$aItem['value']] =$aItem['label'];
                }
            }
            
            $aStepData = $aStepHash;   
        }
        
        
        return $aStepData;
    }
    
    function getStepId($sStepType)
    {
        if (!$sStepType) return false;
        
        $aStepIdHash = array
        (
            'customer'      => '1',
        );
        
        if (isset($aStepIdHash[$sStepType]))
        {
            return $aStepIdHash[$sStepType];
        }
        
        return 0;
    }
    
}