<?php 
class MagentoGarden_Transparentwatermark_Adminhtml_TwcategoryController extends Mage_Adminhtml_Controller_Action {
	
	protected function _initTwcategory($idFieldName = 'id') {
		$this->_title($this->__('Twcategory'))->_title($this->__('Manage Twcategorys'));
		$_twcategory_id = (int) $this->getRequest()->getParam($idFieldName);
		$_twcategory = Mage::getModel('transparentwatermark/twcategory');
		if ($_twcategory_id) {
			$_twcategory->load($_twcategory_id);
		}
		Mage::register('current_twcategory', $_twcategory);
		return $this;
	}
	
	public function indexAction() {
		$this->_title($this->__('MagentoGarden Twcategory'))->_title($this->__('Manage Twcategory'));
		
		$this->loadLayout();
		$this->_setActiveMenu('magentogarden/transparentwatermark');
		
		$this->_addContent(
			$this->getLayout()->createBlock('transparentwatermark/adminhtml_twcategory', 'twcategory')
		);
		
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('MagentoGarden Twcategory'), Mage::helper('adminhtml')->__('MagentoGarden Twcategory'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Twcategory'), Mage::helper('adminhtml')->__('Manage Twcategory'));
		
		$this->renderLayout();
	}
	
	public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('transparentwatermark/adminhtml_twcategory_grid')->toHtml());
    }
	

	public function editAction() {
		$this->_initTwcategory();
		$this->loadLayout();
		$_twcategory = Mage::registry('current_twcategory');
		
		$this->_title($_twcategory->getData('entity_id') ? $_twcategory->getData('category_id') : $this->__('New Category Watermark'));
		$this->_setActiveMenu('transparentwatermark/new');
		$this->renderLayout();
	}	
	
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function saveAction() {
		$_data = $this->getRequest()->getParams();
		
		// upload image file
		// todo process different type
		$_filenames = array();
		$_prefixs = array('base', 'small', 'thumbnail');
		$_path = Mage::helper('transparentwatermark/data')->getMediaDir();
		foreach ($_prefixs as $_prefix) {
			$_idx = $_prefix.'_watermark';
			if (strlen($_FILES[$_idx]['name']) > 0) {
				$_filenames[$_idx] = rand(0, 10000).$_FILES[$_idx]['name'];
				$_uploader = new Varien_File_Uploader($_idx);
				$_uploader->save($_path, $_filenames[$_idx]);
			}
		}
		
		if ($_data) {
			$_twcategory = Mage::getModel('transparentwatermark/twcategory');
			$_is_create = false;
			
			if (isset($_data['twcategory_id'])) {
				$_twcategory -> load ($_data['twcategory_id']);
			} else {
				$_twcategory -> setData('category_id', $_data['category_id']);
				$_twcategory -> setData('created_time', date('Y-m-d H:i:s',time()));
				$_is_create = true;
			}
			
			// todo set data
			foreach ($_prefixs as $_prefix) {
				$_idx = $_prefix.'_watermark';
				if (isset($_filenames[$_idx])) {
					$_twcategory -> setData($_idx, 'MagentoGarden'.DS.'TransparentWatermark'.DS.$_filenames[$_idx]);
				}
				$_twcategory -> setData($_prefix.'_position_type', $_data[$_prefix.'_position_type']);
				$_twcategory -> setData($_prefix.'_default_position_type', $_data[$_prefix.'_default_position_type']);
				$_twcategory -> setData($_prefix.'_custom_position_x', $_data[$_prefix.'_custom_position_x']);
				$_twcategory -> setData($_prefix.'_custom_position_y', $_data[$_prefix.'_custom_position_y']);
			}
			$_twcategory -> setData('update_time', date('Y-m-d H:i:s',time()));
			$_twcategory -> setData('store_view', implode(',', $_data['stores']));
			$_twcategory -> setData('is_active', $_data['is_active']);
			$_twcategory -> setData('disable_watermark', $_data['disable_watermark']);
			$_twcategory -> save();
		}
		$this->getResponse()->setRedirect($this->getUrl('*/twcategory'));
	}

	public function deleteAction() {
		
	}
}
