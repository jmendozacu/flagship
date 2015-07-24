<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     439
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advd_Model_Observer extends Varien_Object
{
    public function onControllerActionPredispatch($observer)
    {
        if (Mage::getSingleton('advd/config')->isReplaceDashboardLink()) {
            $menu = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');
     
            $itmDashboad          = null;
            $itmAdvrDashboard     = null;
            $itmAdvrUserDashboard = null;

            foreach ($menu->children() as $key => $children) {
                if ($key == 'dashboard') {
                    $itmDashboad = $children;
                    foreach ($itmDashboad->children->children() as $subKey => $subChildren) {
                        if ($subKey == 'advd_dashboard_global') {
                            $itmAdvrDashboard = $subChildren;
                        }
                        if ($subKey == 'advd_dashboard_user') {
                            $itmAdvrUserDashboard = $subChildren;
                        }
                    }
                }
            }
            
            if ($itmDashboad && $itmAdvrDashboard
                && Mage::getSingleton('admin/session')->isAllowed('dashboard/advd_dashboard_global')) {
                
                $itmDashboad->action = (string) $itmAdvrDashboard->action;
                unset($itmDashboad->children->advd_dashboard_global);
            } elseif ($itmDashboad && $itmAdvrUserDashboard
                && Mage::getSingleton('admin/session')->isAllowed('dashboard/advd_dashboard_user')) {

                $itmDashboad->action = (string) $itmAdvrUserDashboard->action;
                unset($itmDashboad->children->advd_dashboard_user);
            }
        }
    }

    public function notificationJob()
    {
        $emails = Mage::getModel('advd/notification')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($emails as $email) {
            $email = $email->load($email->getId());
            if ($email->canSend()) {
                $email->send();
            }
        }
    }
}