<?php 

class Ecommerceguys_Inventorymanager_Model_Core_Design_Package extends Mage_Core_Model_Design_Package
{
	public function getTheme($type)
    {
        if (empty($this->_theme[$type])) {
            $this->_theme[$type] = Mage::getStoreConfig('design/theme/'.$type, $this->getStore());
            if ($type!=='default' && empty($this->_theme[$type])) {
                $this->_theme[$type] = $this->getTheme('default');
                if (empty($this->_theme[$type])) {
                    $this->_theme[$type] = self::DEFAULT_THEME;
                }

                // ADDED CODE 
                if(Mage::app()->getRequest()->getModuleName() == "inventorymanager"){
                	//$this->_theme["default"] = $this->_theme["template"];
                	$this->_theme["layout"] = "inventorymanager";
                	$this->_theme["template"] = "inventorymanager";
                	$this->_theme["skin"] = "inventorymanager";
                }
                
                // "locale", "layout", "template"
            }
        }

        // + "default", "skin"

        // set exception value for theme, if defined in config
        $customThemeType = $this->_checkUserAgentAgainstRegexps("design/theme/{$type}_ua_regexp");
        if ($customThemeType) {
            $this->_theme[$type] = $customThemeType;
        }

        return $this->_theme[$type];
    }
}