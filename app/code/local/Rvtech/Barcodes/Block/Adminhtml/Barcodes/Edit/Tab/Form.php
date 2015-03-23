<?php

class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$collection = Mage::getModel('catalog/product')
				->getCollection()
				->addAttributeToSelect('*')
				//->addAttributeToFilter('status', 1)
				->addAttributeToFilter('type_id', 'simple')
				->addAttributeToSort('sku', 'ASC');
				// ->addAttributeToFilter('visibility', 4);

				

		$productarr[null] = '';
		$productsku[null] = '';
		$upcarray[null] = '';
		foreach ($collection as $product) {
			$productarr[$product->getId()] = $product->getSku() . ' - ' . $product->getName();
			if ($product->getUpc()) {
				$productsku[$product->getUpc()] = $product->getId();
			}
		}
		
		$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
				->addFieldToFilter('attribute_code', 'factory')
				->load();
		$attribute = $attributes->getFirstItem();

		$attr = $attribute->getSource()->getAllOptions(true);

		foreach ($attr as $attval) {
			$factarr[$attval['value']] = $attval['label'];
		}

		$fieldset = $form->addFieldset('barcodes_form', array('legend' => 'Ref information'));
		$fieldset->addField('purchase_order', 'text', array(
			'label' => 'Purchase Order (Invoice #)',
			'class' => 'required-entry',
			'required' => true,
			'name' => 'purchase_order',
		));
		$fieldset->addField('date', 'date', array(
			'label' => 'Order Date',
			'class' => 'required-entry',
			'required' => true,
			'name' => 'date',
			'image' => $this->getSkinUrl('images/grid-cal.gif'),
			//'format'=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
			'format' => 'yyyy-M-dd',
		));
		$fieldset->addField('factory_id', 'select', array(
			'label' => 'Factory',
			'class' => 'required-entry',
			'required' => true,
			'name' => 'factory_id',
			'values' => $factarr,
		));
		$fieldset->addField('upc', 'text', array(
			'label' => 'UPC Search',
			'name' => 'upc',
			'onchange' => 'validateUPC(this.value)',
			//'readonly' => true,
			//'disabled' => true,
		))->setAfterElementHtml("<script type=\"text/javascript\">
			var productSel = " . json_encode($productsku) . ";
			function validateUPC(selectElement) {
				if(selectElement != '') {
					document.getElementById('product_id').value = productSel[selectElement];
					//document.getElementById('upc').select();
				}
			}
		</script>");
		$fieldset->addField('product_id', 'select', array(
			'label' => 'Product',
			'class' => 'required-entry',
			'required' => true,
			'name' => 'product_id',
			'values' => $productarr,
		));
		if (Mage::registry('barcodes_data')->getId()) {
			$fieldset->addField('dzv_serial', 'text', array(
				'label' => 'DZV Serial',
				//'name' => 'dzv_serial',
				'readonly' => true,
				'disabled' => true,
			));
		}
		if (!Mage::registry('barcodes_data')->getId()) {
			$fieldset->addField('quantity', 'text', array(
				'label' => 'Quantity',
				'name' => 'quantity',
				'class' => 'validate-digits validate-digits-range digits-range-0000-9999',
			));
		}
		if (Mage::registry('barcodes_data')->getId()) {
			$fieldset->addField('location', 'text', array(
				'label' => 'Location',
				'required' => false,
				'name' => 'location',
			));
		}
		if (Mage::registry('barcodes_data')->getId()) {
			$form->addField('note', 'note', array(
				'text' => 'Note: The serial number will only be edited if the date or factory is changed.'
			));
		} else {
			$form->addField('note', 'note', array(
				'text' => 'Note: A unique serial will be generated for each quantity of this product. The default quantity is 1.'
			));
		}

		$barcodes_data = Mage::registry('barcodes_data')->getData();
		if (!empty($barcodes_data)) {
			$form->setValues($barcodes_data);
		}
		return parent::_prepareForm();
	}

}