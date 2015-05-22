<?php
class Ecommerceguys_Inventorymanager_VendorController extends Mage_Core_Controller_Front_Action
{
	protected function _getSession()
    {
        return Mage::getSingleton('inventorymanager/session');
    }
	
	public function preDispatch123()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $openActions = array(
            //'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            //'confirm',
            //'confirmation'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }
	
	
	public function indexAction(){
		
	}
	
	public function loginAction(){
		
		//print_r($this->_getSession()->getVendor()->getData()); exit;
		
		if ($this->_getSession()->isLoggedIn()) {
		
            $this->_redirect('*/*/');
            return;
        }
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('inventorymanager/session');
        $this->_initLayoutMessages('core/session');
        $this->renderLayout();
	}
	
	public function loginpostAction(){
		if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        
        $session = $this->_getSession();
        $coreSession = Mage::getSingleton('core/session');

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost();
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    
                    if ($session->getVendor()->getIsJustConfirmed()) {
                    	//print_r($session->getVendor()->getData()); exit;
                    	Mage::getSingleton('core/session')->setVendor($session->getVendor());
                        $this->_redirect('*/*/index');
                        return;
                    }else{
                    	
                    }
                } catch (Mage_Core_Exception $e) {
                    $message = $e->getMessage();
                    $session->addError($message);
                    $coreSession->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                     Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
                $coreSession->addError($this->__('Login and password are required.'));
            }
        }
        $this->_redirect('*/*/login');
	}
}