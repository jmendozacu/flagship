<?php

class Angelleye_PaypalBanner_SettingsController extends Mage_Core_Controller_Front_Action
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
            $element = Mage::getModel('core/config_data')
                ->getCollection()
                ->addFieldToFilter('path', 'paypalbanner/settings/id')
                ->getFirstItem();

            if ($element->getId()){
                $element->setValue($publisherId)->save();
            } else {
                Mage::getModel('core/config_data')
                    ->setScope('default')
                    ->setScopeId(0)
                    ->setPath('paypalbanner/settings/id')
                    ->setValue($publisherId)
                    ->save();
            }
        }
        return $this->getResponse()->setBody(json_encode($res));

    }
}