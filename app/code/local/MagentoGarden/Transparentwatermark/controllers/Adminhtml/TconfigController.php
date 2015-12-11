<?php
/**
 * MagentoGarden
 *
 * @category    controller
 * @package     magentogarden_transparentwatermark
 * @copyright   Copyright (c) 2012 MagentoGarden Inc. (http://www.magentogarden.com)
 * @version		1.3.0
 * @author		MagentoGarden (coder@magentogarden.com)
 */


require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'System'.DS.'ConfigController.php';

class MagentoGarden_Transparentwatermark_Adminhtml_TconfigController extends Mage_Adminhtml_System_ConfigController {
	
	public function indexAction()
    {
    	$this->_forward('edit');
    }
	
	/**
     * Edit configuration section
     *
     */
    public function editAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Configuration'));

        $current = $this->getRequest()->getParam('section');	
	    $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');
        
        Mage::getSingleton('adminhtml/config_data')
            ->setSection($current)
            ->setWebsite($website)
            ->setStore($store);

        $configFields = Mage::getSingleton('adminhtml/config');

        $sections     = $configFields->getSections($current);
        $section      = $sections->$current;
        $hasChildren  = $configFields->hasChildren($section, $website, $store);
        if (!$hasChildren && $current) {
            $this->_redirect('*/*/', array('website'=>$website, 'store'=>$store));
        }

        $this->loadLayout();
		
        $this->_setActiveMenu('system/config');
        $this->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo(array($current));

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'),
            $this->getUrl('*/system'));

        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/system_config_tabs')->initTabs());
			
        if ($this->_isSectionAllowedFlag) {
            $this->_addContent($this->getLayout()->createBlock('adminhtml/system_config_edit')->initForm());

            $this->_addJs($this->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate('system/shipping/ups.phtml'));
            $this->_addJs($this->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate('system/config/js.phtml'));
            $this->_addJs($this->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate('system/shipping/applicable_country.phtml'));
				
			$_label = (string)$section->label;
			if ($current == 'magentogarden_transparentwatermark' || $_label == 'Transparent Watermark') {
				$this->getLayout()->getBlock('head')->addJs('magentogarden/jquery-1.6.4.min.js');
				$this->getLayout()->getBlock('head')->addJs('magentogarden/global.js');
				$this->getLayout()->getBlock('head')->addJs('magentogarden/jquery-ui-1.8.18.custom.min.js');
				$this->getLayout()->getBlock('content')->append($this->getLayout()
								->createBlock('transparentwatermark/adminhtml_js')
								->setTemplate('transparentwatermark/js.phtml'));
			}

            $this->renderLayout();
        }
    }
	
	public function saveAction() {
		$session = Mage::getSingleton('adminhtml/session');
        /* @var $session Mage_Adminhtml_Model_Session */

        $groups = $this->getRequest()->getPost('groups');

        if (isset($_FILES['groups']['name']) && is_array($_FILES['groups']['name'])) {
            /**
             * Carefully merge $_FILES and $_POST information
             * None of '+=' or 'array_merge_recursive' can do this correct
             */
            foreach($_FILES['groups']['name'] as $groupName => $group) {
                if (is_array($group)) {
                    foreach ($group['fields'] as $fieldName => $field) {
                        if (!empty($field['value'])) {
                            $groups[$groupName]['fields'][$fieldName] = array('value' => $field['value']);
                        }
                    }
                }
            }
        }
        
        try {
            if (!$this->_isSectionAllowed($this->getRequest()->getParam('section'))) {
                throw new Exception(Mage::helper('adminhtml')->__('This section is not allowed.'));
            }

            // custom save logic
            $this->_saveSection();
            $section = $this->getRequest()->getParam('section');
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');
            Mage::getModel('adminhtml/config_data')
                ->setSection($section)
                ->setWebsite($website)
                ->setStore($store)
                ->setGroups($groups)
                ->save();

            // reinit configuration
            Mage::getConfig()->reinit();
            Mage::app()->reinitStores();
			
			// action for transparent watermark extension
			if (isset($groups['general']['fields']['enabled_transparentwatermark'])) {
				$_enable = $groups['general']['fields']['enabled_transparentwatermark']['value'];
				if ($_enable == 1) {
					// enable transparent watermark
				} else {
					// disable transparent watermark
				}
			}

            // website and store codes can be used in event implementation, so set them as well
            Mage::dispatchEvent("admin_system_config_changed_section_{$section}",
                array('website' => $website, 'store' => $store)
            );
            $session->addSuccess(Mage::helper('adminhtml')->__('The configuration has been saved.'));
        }
        catch (Mage_Core_Exception $e) {
            foreach(explode("\n", $e->getMessage()) as $message) {
                $session->addError($message);
            }
        }
        catch (Exception $e) {
            $session->addException($e,
                Mage::helper('adminhtml')->__('An error occurred while saving this configuration:') . ' '
                . $e->getMessage());
        }

        $this->_saveState($this->getRequest()->getPost('config_state'));

        $this->_redirect('*/*/edit', array('_current' => array('section', 'website', 'store')));
	}
}
