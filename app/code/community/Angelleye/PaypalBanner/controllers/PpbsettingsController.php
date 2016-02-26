<?php

class Angelleye_PaypalBanner_PpbsettingsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('PaypalBanners - Setup Finance Portal'));
        $this->renderLayout();
    }

    public function processAction()
    {
        $post = $this->getRequest()->getParams();

        $email = !empty($post['paypal_email']) ? $post['paypal_email'] : '';
        $name = !empty($post['paypal_name']) ? $post['paypal_name'] : '';
        $terms = !empty($post['paypal_terms']) ? $post['paypal_terms'] : '';
        $active = !empty($post['paypal_active']) ? $post['paypal_active'] : 0;
        $container = !empty($post['paypal_container']) ? $post['paypal_container'] : '';

        $messages = array();
        $success = false;
        $publisherId = '';

        if (empty($email) || empty($name)){
            $messages[] = 'Please, enter all required fields';
        }
        if (empty($terms)){
            $messages[] = 'You have to agree with terms and conditions';
        }

        if (!Zend_Validate::is($email, 'EmailAddress')) {
            $messages[] = 'Please, enter valid email';
        }


        if (empty($messages)) {
//            $publisherId = Mage::getSingleton('paypalbanner/client', array($name, $email))->call()->extractPublisherId();
            $publisherId = Mage::getSingleton('paypalbanner/client', array($name, $email))->callProxy()->extractPublisherId();

            if (!empty($publisherId)){
                $success = true;
            }
        }

        $res = array(
            'success'=>$success,
            'msg'=>implode('. ', $messages),
            'publisher_id'=>$publisherId
        );
        if ($success){
            $elementId = Mage::getModel('core/config_data')
                ->getCollection()
                ->addFieldToFilter('path', 'paypalbanner/settings/id')
                ->getFirstItem();

            if ($elementId && $elementId->getId()){
                $elementId->setValue($publisherId)->save();
            } else {
                Mage::getModel('core/config_data')
                    ->setScope('default')
                    ->setScopeId(0)
                    ->setPath('paypalbanner/settings/id')
                    ->setValue($publisherId)
                    ->save();
            }

            $elementActive = Mage::getModel('core/config_data')
                ->getCollection()
                ->addFieldToFilter('path', 'paypalbanner/settings/active')
                ->getFirstItem();

            if ($elementActive && $elementActive->getId()){
                $elementActive->setValue($active)->save();
            } else {
                Mage::getModel('core/config_data')
                    ->setScope('default')
                    ->setScopeId(0)
                    ->setPath('paypalbanner/settings/active')
                    ->setValue($active)
                    ->save();
            }

            $elementContainer = Mage::getModel('core/config_data')
                ->getCollection()
                ->addFieldToFilter('path', 'paypalbanner/settings/container')
                ->getFirstItem();

            if ($elementContainer && $elementContainer->getId()){
                $elementContainer->setValue($container)->save();
            } else {
                Mage::getModel('core/config_data')
                    ->setScope('default')
                    ->setScopeId(0)
                    ->setPath('paypalbanner/settings/container')
                    ->setValue($container)
                    ->save();
            }
        }
        return $this->getResponse()->setBody(json_encode($res));

    }

    public function clearAction()
    {
        $elementId = Mage::getModel('core/config_data')
            ->getCollection()
            ->addFieldToFilter('path', 'paypalbanner/settings/id')
            ->getFirstItem();
        if ($elementId && $elementId->getId()){
            $elementId->delete();
        }

        $elementActive  = Mage::getModel('core/config_data')
            ->getCollection()
            ->addFieldToFilter('path', 'paypalbanner/settings/active')
            ->getFirstItem();
        if ($elementActive && $elementActive->getId()){
            $elementActive->setValue(0)->save();
        }
    }
}