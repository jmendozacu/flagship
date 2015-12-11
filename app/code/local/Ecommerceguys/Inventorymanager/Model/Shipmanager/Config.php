<?php
class Ecommerceguys_Inventorymanager_Model_Shipmanager_Config extends Mage_Core_Model_Config
{
	public function saveConfig($path, $value, $scope = 'default', $scopeId = 0)
    {
        $resource = $this->getResourceModel();
        $resource->saveConfig(rtrim($path, '/'), $value, $scope, $scopeId);
 
        return $this;
    }
}