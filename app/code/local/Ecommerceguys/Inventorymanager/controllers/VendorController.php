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
		if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/login');
            return;
        }
		$this->loadLayout();
		$this->renderLayout();
	}
	
    public function profileAction(){
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function vendorsaveAction() {

        
        if ($data = $this->getRequest()->getPost()) {
           
           $vendorId = Mage::getSingleton('core/session')->getVendor()->getId();
           
           /*
           echo "<pre>";
            print_r($data);
           exit;
            */
            $model = Mage::getModel('inventorymanager/vendor');     
            $model->setData($data)
                ->setId($vendorId);
            
            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }   
                
                $model->save();
                

                Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventorymanager')->__('Vendor saved successfully'));
                Mage::getSingleton('core/session')->setFormData(false);
                Mage::getSingleton('core/session')->setVendor($model);
                //Mage::getModel('inventorymanager/session')->setVendor($model);
                $this->_redirect('inventorymanager/vendor/profile');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::getSingleton('core/session')->setFormData($data);
                $this->_redirect('*/*/profile');
                return;
            }
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('inventorymanager')->__('Unable to find vendor to save'));
        $this->_redirect('inventorymanager/adminuser/vendorprofiles');
    }

	public function productsAction(){
		if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/login');
            return;
        }
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function loginAction(){
		
		if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        
        if($this->_getSession()->isAdminUser()){
        	$this->_redirect('*/adminuser/vendors');
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
                    if($session->login($login['username'], $login['password'])){
                    	
	                    if($coreSession->getUserType() == "admin"){
	                    	$this->_redirect('*/adminuser/vendors');
	                    	return;
	                        exit;
	                    }
	                    if ($session->getVendor()->getIsJustConfirmed()) {
	                    	Mage::getSingleton('core/session')->setVendor($session->getVendor());
	                        $this->_redirect('*/*/index');
	                        return;
	                    }else{
	                    	
	                    }
                    }else{
                    	throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'));
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
	
    
	public function logoutAction(){
		$this->_getSession()->logout()
            ->renewSession();

        $this->_redirect('*/*/login');
	}
	
	public function locationsAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveLocationAction(){
		$data = $this->getRequest()->getParams();
		$session = $this->_getSession();
		if(isset($data['location']) && $data['location'] != ""){
			$labelResource = Mage::getResourceModel('inventorymanager/label');
			$labelResource->addLocation($data['location']);
			$session->addSuccess(Mage::helper('inventorymanager')->__("Location added."));
			$this->_redirect("inventorymanager/vendor/locations");
			return $this;
		}
		$session->addError(Mage::helper('inventorymanager')->__("Invalid data"));
		$this->_redirect("inventorymanager/vendor/locations");
		return $this;
	}
	
	public function deleteLocationAction(){
		$data = $this->getRequest()->getParams();
		$session = $this->_getSession();
		if(isset($data['location']) && $data['location'] != ""){
			$labelResource = Mage::getResourceModel('inventorymanager/label');
			$labelResource->removeLocation($data['location']);
			$session->addSuccess(Mage::helper('inventorymanager')->__("Location removed."));
			$this->_redirect("inventorymanager/vendor/locations");
			return $this;
		}
		$session->addError(Mage::helper('inventorymanager')->__("Invalid data"));
		$this->_redirect("inventorymanager/vendor/locations");
		return $this;
	}
}