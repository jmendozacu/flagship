<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Purchase_OrdersController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		
	}

	/**
	 * 
	 *
	 */
	public function ListAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 *  
	 *
	 */
	public function EditAction() {

		$this->loadLayout();
		$OrderId = $this->getRequest()->getParam('po_num');
		Mage::register('purchase_order_id', $OrderId);
		$this->renderLayout();
	}

	/**
	 * 
	 *
	 */
	public function NewAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * Create new order
	 *
	 */
	public function createAction() {

		$sup_num = $this->getRequest()->getParam('supplier');

		$order = mage::helper('purchase')->createNewOrder($sup_num);

		//confirm & redirect
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order successfully Created'));
		$this->_redirect('Purchase/Orders/Edit', array('po_num' => $order->getId()));
	}

	/**
	 * Delete order
	 *
	 */
	public function deleteAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$productIds = array();

		//todo : following instructions should be in after delete....
		//Delete stock movements
		$collection = mage::getModel('AdvancedStock/StockMovement')
				->getCollection()
				->addFieldToFilter('sm_po_num', $po_num);
		foreach ($collection as $item) {
			$item->delete();
		}

		//update supply needs and fill productIds array
		$order = mage::getModel('Purchase/Order')->load($po_num);
		foreach ($order->getProducts() as $item) {
			$productId = $item->getpop_product_id();
			$productIds[] = $productId;
		}

		//delete order products
		$collection = mage::getModel('Purchase/OrderProduct')
				->getCollection()
				->addFieldToFilter('pop_order_num', $po_num);
		foreach ($collection as $item) {
			$item->delete();
		}

		//delete order
		$order->delete();

		//update products waiting for delivery, supply date
		foreach ($productIds as $productId) {

			mage::helper('BackgroundTask')->AddTask('Update product delivery date for product #' . $productId, 'purchase/Product', 'updateProductWaitingForDeliveryQty', $productId
			);

			mage::helper('BackgroundTask')->AddTask('Update product waiting for delivery qty for product #' . $productId, 'purchase/Product', 'updateProductDeliveryDate', $productId
			);
		}

		//Confirm & redirect
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order successfully Deleted'));
		$this->_redirect('Purchase/Orders/List');
	}

	/**
	 * Save PO information
	 *
	 */
	public function saveAction() {
		$postData = $this->getRequest()->getPost();
		$order = mage::getModel('Purchase/Order')->load($this->getRequest()->getPost('po_num'));
		$currentTab = $this->getRequest()->getPost('current_tab');
		$successMsg = 'Order successfully Saved';

		// generate serials code starts
		$productIds = $this->getRequest()->getPost('productIdArr');
		if (($currentTab == 'tab_products') && ($this->getRequest()->getPost('serial_check'))) {
			if (($this->getRequest()->getPost('po_date') != "") && ($this->getRequest()->getPost('po_sup_num') != "")) {
				try {
					$invoice = $order->getPoOrderId();
					$date = $order->getPoDate();
					if ($date == '') {
						$date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
					}
					$serial_date = str_replace('-', '', $date);
					$orderId = $order->getPoOrderId();
					$supply_id = $this->getRequest()->getPost('po_sup_num');
					if ($supply_id == 1) {
						$FC = 'FS';
						$factory_id = 20;
					} elseif ($supply_id == 2) {
						$FC = 'JL';
						$factory_id = 21;
					} elseif ($supply_id == 3) {
						$FC = 'SN';
						$factory_id = 22;
					}
					//echo "factory = $FC, date = $serial_date<br>\n";
					if(!$factory_id) {
						Mage::getSingleton('adminhtml/session')->addError("No supplier specified! Supplier required to generate serials.");
					} else {
						// select serial count for this supplier and date
						$serialCollection = Mage::getModel('barcodes/barcodes')->getCollection()
								->addFieldToFilter('dzv_serial', array('like' => $FC . $serial_date . '%'))
								->addExpressionFieldToSelect('max_serial', 'MAX({{dzv_serial}})', 'dzv_serial');
						$max_serialRow = $serialCollection->getFirstItem();
						//echo "max serial: " . $max_serialRow->getMaxSerial() . "<br>\n";
						$max_serial_arr = explode('-', $max_serialRow->getMaxSerial());
						$poSerialCount = !empty($max_serial_arr[1]) ? $max_serial_arr[1] : 0;
						//echo "poSerialCount = $poSerialCount<br>\n";
						foreach ($productIds as $productId) {
							//echo "fetching product $productId<br>\n";
							$productcollection = Mage::getModel('catalog/product')->load($productId);
							$product_sku = $productcollection->getSku();
							//echo "product sku: $product_sku<br>\n";
							$qtySelect = 'qty_val_' . $productId;
							$qtyId = $this->getRequest()->getParam($qtySelect);
							$qtyVal = 'pop_qty_' . $qtyId;
							$quantity = $this->getRequest()->getParam($qtyVal);
							//echo "param quantity: $quantity<br>\n";
							if (empty($quantity) || !is_numeric($quantity) || $quantity < 1) {
								// assume it's 0 or invalid
								//echo "skipping product because its quantity is invalid<br>\n";
								continue;
							}
							
							// select serial count for this product and purchase order
							//echo "selecting serial count for product $productId where dzv_serial LIKE {$FC}{$serial_date}%<br>\n";
							$serialCollection = Mage::getModel('barcodes/barcodes')->getCollection()
									->addFieldToFilter('purchase_order', array('eq' => $invoice))
									->addFieldToFilter('product_id', array('eq' => $productId));
							$serialCount = $serialCollection->getSize();
							//$max_serialRow = $serialCollection->getFirstItem();
							//$max_serial_arr = explode('-', $max_serialRow->getMaxSerial());
							//echo "max_serial_arr: ".implode(', ', $max_serial_arr)."<br>\n";
							//$serialCount = !empty($max_serial_arr[1]) ? $max_serial_arr[1] : 0;
							//echo "existing serial count for product $productId on " . $FC . $serial_date . ": " . intval($serialCount) . "<br>\n";
							if (empty($serialCount) || !is_numeric($serialCount) || $serialCount < 0) {
								//echo "serial count is invalid; setting to 0<br>\n";
								$serialCount = 0;
							}
							
							//for ($i = 0; $i < $quantity; $i++) {
							// WHY IS THIS HERE??? $finalQty = $serialCount + $quantity;
							// serialCount must not exceed quantity
							while ($serialCount++ < $quantity) {
								$sequence = sprintf("%04s", ++$poSerialCount);
								//echo "sequence: $sequence<br>\n";
								$dzvSerial = $FC . $serial_date . '-' . $sequence;
								//echo "dzvSerial: $dzvSerial<br>\n";
								$barcodeModel = Mage::getModel('barcodes/barcodes');
								$barcodeModel->addData($postData)
										->setPurchaseOrder($orderId)
										->setDate($date)
										->setSku($product_sku)
										->setFactoryId($factory_id)
										->setProductId($productId)
										->setDzvSerial($dzvSerial);
								//echo "saving serial record: purchase order = $orderId, date = $date, sku = $product_sku, factory $factory_id, product id $productId, dzvSerial = $dzvSerial<br>\n";
								$barcodeModel->save();
							}
						}
						$successMsg = 'Serials successfully generated';
					}
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			} else {
				Mage::getSingleton('adminhtml/session')->addError("Order Date and Supplier can't be empty. These values are reguired to generate the serials.");
			}
		}
		// generate serials code ends
		//die("finished with serials");
		//init updater
		$purchaseOrderUpdater = mage::getModel('Purchase/Order_Updater')->init($order);

		try {

			$order = mage::getModel('Purchase/Order')->load($this->getRequest()->getPost('po_num'));

			//init data
			$data = $this->getRequest()->getPost();
			$dateFields = array('po_supply_date', 'po_invoice_date', 'po_payment_date');
			foreach ($dateFields as $dateField) {
				if (($data[$dateField] == '') || ($data[$dateField] == '0000-00-00'))
					$data[$dateField] = new Zend_Db_Expr('null');
			}

			//save order data
			foreach ($data as $key => $value)
				$order->setData($key, $value);
			$order->save();

			//update products
			$productLogDatas = $this->productsDataToArray($data['order_product_log']);
			$products = $order->getProducts();
			$hasDeleted = false;
			foreach ($products as $product) {
				//if we have to update datas
				if (isset($productLogDatas[$product->getId()])) {
					$currentProductData = $productLogDatas[$product->getId()];
					if (isset($currentProductData['delete'])) {
						$product->delete();
						$hasDeleted = true;
					} else {
						//update datas
						$dateField = array('pop_delivery_date');
						foreach ($currentProductData as $key => $value) {
							if (in_array($key, $dateField)) {
								if (($value == '') || ($value == '0000-00-00'))
									$value = new Zend_Db_Expr('null');
							}
							$product->setData($key, $value);
						}
						$product->save();
					}
				} else {
					//if currency change rate has changed, just call save on products to update base values
					if ($order->getpo_currency_change_rate() != $order->getOrigData('po_currency_change_rate')) {
						$product->save();
					}
				}
			}
			if ($hasDeleted)
				$order->resetProducts();

			//check if we have to add products
			$productAdded = false;
			if ($this->getRequest()->getPost('add_product') != '') {
				$productsToAdd = $this->_decodeInput($this->getRequest()->getPost('add_product'));
				foreach ($productsToAdd as $key => $value) {
					//retrieves values
					$productId = $key;
					$qty = $value['qty'];
					if ($qty == '')
						$qty = 1;

					//add product
					$order->AddProduct($productId, $qty);
					$productAdded = true;
				}

				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Products added'));
				$currentTab = 'tab_products';
				$order->resetProducts();
			}

			//add supply needs
			$supplyNeedsIds = explode(',', $this->getRequest()->getPost('supply_needs_ids'));
			foreach ($supplyNeedsIds as $supplyNeedId) {
				$supplyNeed = mage::getModel('Purchase/SupplyNeeds')->load($supplyNeedId);
				if ($supplyNeed->getsn_needed_qty() > 0) {

					try {
						$order->AddProduct($supplyNeed->getsn_product_id(), $supplyNeed->getsn_needed_qty());
					} catch (Exception $ex) {
						Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
					}

					$productAdded = true;
				}
			}
			if ($this->getRequest()->getPost('supply_needs_ids') != '') {
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supply Needs added'));
				$currentTab = 'tab_products';
				$order->resetProducts();
			}

			//check for deliveries
			$targetWarehouseId = $this->getRequest()->getPost('add_sm_warehouse_id');
			$targetWarehouse = mage::getModel('AdvancedStock/Warehouse')->load($targetWarehouseId);
			$defectWarehouseId = mage::getStoreConfig('purchase/purchase_order/defect_products_warehouse');
			$deliveryData = $this->deliveryDataToArray($this->getRequest()->getPost('delivery_log'));
			foreach ($products as $item) {
				//skip product if no delivery information
				if (!isset($deliveryData[$item->getId()]))
					continue;

				//retrieve datas
				$qty = (int) $deliveryData[$item->getId()]['delivery_qty'];
				$defectQty = (int) (isset($deliveryData[$item->getId()]['delivery_defect_qty']) ? $deliveryData[$item->getId()]['delivery_defect_qty'] : 0);
				if (($qty == 0) && ($defectQty == 0))
					continue;

				$barcode = (isset($deliveryData[$item->getId()]['delivery_barcode']) ? $deliveryData[$item->getId()]['delivery_barcode'] : '');
				$serials = (isset($deliveryData[$item->getId()]['delivery_serials']) ? $deliveryData[$item->getId()]['delivery_serials'] : '');
				$location = (isset($deliveryData[$item->getId()]['delivery_location']) ? $deliveryData[$item->getId()]['delivery_location'] : '');
				$productId = $item->getpop_product_id();
				$deliveryDate = $this->getRequest()->getPost('add_sm_date');
				$deliveryDescription = mage::helper('purchase')->__('Purchase Order #') . $order->getpo_order_id() . mage::helper('purchase')->__(' from ') . $order->getSupplier()->getsup_name();
				//create delivery
				if ($qty > 0)
					$order->createDelivery($item, $qty, $deliveryDate, $deliveryDescription, $targetWarehouseId);
				if ($defectQty > 0)
					$order->createDelivery($item, $defectQty, $deliveryDate, $deliveryDescription, $defectWarehouseId);

				//save barcode
				if ($barcode)
					mage::helper('AdvancedStock/Product_Barcode')->addBarcodeIfNotExists($productId, $barcode);

				//save location
				//if (!empty($location))
				// serial location should not save to product location; $targetWarehouse->setProductLocation($item->getpop_product_id(), $location);
				//save serials and location
				if (!empty($serials)) {
					$serials = explode(',', $serials);
					// if serial(s) were added to this product delivery, add the submitted location as well
					mage::helper('AdvancedStock/Product_Serial')->addAVSerialsFromDelivery($productId, $order, $serials, $location);
				}
			}

			//If completely delivered, set status to complete
			if ($order->isCompletelyDelivered())
				$order->setpo_status(MDN_Purchase_Model_Order::STATUS_COMPLETE);

			//update missing prices flag
			$order->setpo_missing_price($order->hasMissingPrices());

			//notify supplier
			$Notify = $this->getRequest()->getPost('send_to_customer');
			if ($Notify == 1) {
				$order->notifySupplier($this->getRequest()->getPost('email_comment'));
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supplier notified'));

				//change status
				switch ($this->getRequest()->getPost('change_status')) {
					case 'waiting_for_supplier':
						$order->setpo_status(MDN_Purchase_Model_Order::STATUS_WAITING_FOR_SUPPLIER);
						break;
					case 'waiting_for_delivery':
						$order->setpo_status(MDN_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY);
						break;
				}
			}
			$order->save();

			//apply updater
			$result = $purchaseOrderUpdater->checkForChangesAndLaunchUpdates($order);

			Mage::getSingleton('adminhtml/session')->addSuccess($this->__($successMsg));
		} catch (Exception $ex) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured') . ' : ' . $ex->getMessage());
		}

		//confirm & redirect
		$this->_redirect('Purchase/Orders/Edit', array('po_num' => $order->getId(), 'tab' => $currentTab));
	}

	/**
	 * Convert product changee string to an array
	 */
	protected function productsDataToArray($data) {
		$retour = array();

		$productLogDatas = explode(';', $data);
		foreach ($productLogDatas as $productLogData) {
			//get key & value
			$t = explode('=', $productLogData);
			$dataKey = $t[0];
			$dataValue = (count($t) == 2 ? $t[1] : '');
			$pos = strrpos($dataKey, '_');
			if ($pos > 0) {
				$dataName = substr($dataKey, 0, $pos);
				$dataId = substr($dataKey, $pos + 1);

				if (!isset($retour[$dataId]))
					$retour[$dataId] = array();
				$retour[$dataId][$dataName] = $dataValue;
			}
		}

		return $retour;
	}

	/**
	 * Convert delivery changes string to an array
	 */
	protected function deliveryDataToArray($data) {
		$retour = array();

		$productLogDatas = explode(';', $data);
		foreach ($productLogDatas as $productLogData) {
			//get key & value
			$t = explode('=', $productLogData);
			$dataKey = $t[0];
			$dataValue = (count($t) == 2 ? $t[1] : '');
			$pos = strrpos($dataKey, '_');
			if ($pos > 0) {
				$dataName = substr($dataKey, 0, $pos);
				$dataId = substr($dataKey, $pos + 1);

				if (!isset($retour[$dataId]))
					$retour[$dataId] = array();
				$retour[$dataId][$dataName] = $dataValue;
			}
		}

		return $retour;
	}

	/**
	 * Print 
	 *
	 */
	public function PrintAction() {
		try {
			//get order
			$po_num = $this->getRequest()->getParam('po_num');
			$order = Mage::getModel('Purchase/Order')->load($po_num);

			$obj = mage::getModel('Purchase/Pdf_Order');
			$pdf = $obj->getPdf(array($order));
			$this->_prepareDownloadResponse(mage::helper('purchase')->__('Purchase Order #') . $order->getpo_order_id() . '.pdf', $pdf->render(), 'application/pdf');
		} catch (Exception $ex) {
			die("Erreur lors de la g�n�ration du PDF de bon de commande fournisseur: " . $ex->getMessage());
		}
	}

	/**
	 * M�thode d�bile qui genere les entetes HTTP pour demander � l'utilisateur d'ouvrir ou enregistrer le PDF
	 *
	 * @param unknown_type $fileName
	 * @param unknown_type $content
	 * @param unknown_type $contentType
	 */
	protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null) {
		$this->getResponse()
				->setHttpResponseCode(200)
				->setHeader('Pragma', 'public', true)
				->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
				->setHeader('Content-type', $contentType, true)
				->setHeader('Content-Length', strlen($content))
				->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
				->setBody($content);
	}

	/**
	 * Importer des produits dans une commande a partir des supply needs 
	 *
	 */
	public function ImportFromSupplyNeedsAction() {
		$this->loadLayout();

		//set grid mode to add products in order
		$orderId = $this->getRequest()->getParam('po_num');
		$block = $this->getLayout()->getBlock('importfromsupplyneeds')->setMode('import', $orderId);
		$this->renderLayout();
	}

	/**
	 * Importe dans la commande les produits s�lectionn�s
	 *
	 */
	public function CreateFromSupplyNeedsAction() {
		//Recupere le no de commande
		$po_num = $this->getRequest()->getParam('po_num');
		$order = mage::getModel('Purchase/Order')->load($po_num);
		$Products = $order->getProducts();

		$purchaseOrderUpdater = mage::getModel('Purchase/Order_Updater')->init($order);

		//parcourt les produits � ajouter
		$data = $this->getRequest()->getParams();
		foreach ($data as $key => $value) {
			//Si c une case a cocher
			if (!(strpos($key, 'ch_') === false)) {
				//Recupere les infos
				$ProductId = str_replace('ch_', '', $key);
				$Qty = $this->getRequest()->getParam('qty_' . $ProductId);

				//Verifie que le produit ne soit pas d�ja ajout� a la commande
				$ok = true;
				foreach ($Products as $Product) {
					if ($Product->getpop_product_id() == $ProductId) {
						$ok = false;
						break;
					}
				}

				if (($Qty > 0) && ($ok)) {
					//ajoute a la commande
					$order->AddProduct($ProductId, $Qty);
				}
			}
		}

		//update datas
		$result = $purchaseOrderUpdater->checkForChangesAndLaunchUpdates($order);
	}

	/**
	 * Cree une commande et ajoute des produits dedans
	 *
	 */
	public function CreateOrderAndAddProductsAction() {
		//recupere le fournisseur
		$sup_num = $this->getRequest()->getParam('supplier_create');

		//cree la commande
		$order = mage::getModel('Purchase/Order')
				->setpo_sup_num($sup_num)
				->setpo_date(date('Y-m-d'))
				->setpo_currency(Mage::getStoreConfig('purchase/purchase_order/default_currency'))
				->setpo_tax_rate(Mage::getStoreConfig('purchase/purchase_order/default_shipping_duties_taxrate'))
				->setpo_order_id(mage::getModel('Purchase/Order')->GenerateOrderNumber())
				->save();

		$purchaseOrderUpdater = mage::getModel('Purchase/Order_Updater')->init($order);

		//rajoute les produits
		$data = $this->getRequest()->getParams();
		foreach ($data as $key => $value) {
			//Si c une case a cocher
			if (!(strpos($key, 'ch_') === false)) {
				//Recupere les infos
				$ProductId = str_replace('ch_', '', $key);
				$Qty = $this->getRequest()->getParam('qty_' . $ProductId);
				if ($Qty > 0)
					$order->AddProduct($ProductId, $Qty);
			}
		}

		//update datas
		$result = $purchaseOrderUpdater->checkForChangesAndLaunchUpdates($order);

		//confirme
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order successfully Created') . ' (' . $result . ')');

		//Redirige vers la fiche cr��e
		$this->_redirect('Purchase/Orders/Edit/po_num/' . $order->getId());
	}

	/**
	 * M�thode pour mettre a jour les dates pr�visionnelles d'appro pour les produits (et modifier les dates pr�visionnelles des commandes
	 *
	 */
	public function UpdateProductsDeliveryDateAction() {
		//Recupere la commande
		$po_num = $this->getRequest()->getParam('po_num');
		$order = mage::getModel('Purchase/Order')->load($po_num);

		//met a jour
		if ($order->UpdateProductsDeliveryDate()) {
			//confirme
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Products delivery date successfully Updated'));
		} else {
			//confirme
			Mage::getSingleton('adminhtml/session')->addError($this->__('Delivery date incorrect'));
		}

		//Redirige vers la fiche cr��e
		$this->_redirect('Purchase/Orders/Edit/po_num/' . $order->getId());
	}

	/**
	 * Create serializer block for a grid
	 *
	 * @param string $inputName
	 * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
	 * @param array $productsArray
	 * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer
	 */
	protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray) {
		return $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_ajax_serializer')
						->setGridBlock($gridBlock)
						->setProducts($productsArray)
						->setInputElementName($inputName);
	}

	/**
	 * Output specified blocks as a text list
	 */
	protected function _outputBlocks() {
		$blocks = func_get_args();
		$output = $this->getLayout()->createBlock('adminhtml/text_list');
		foreach ($blocks as $block) {
			$output->insert($block, '', true);
		}
		$this->getResponse()->setBody($output->toHtml());
	}

	protected function _decodeInput($encoded) {
		parse_str($encoded, $data);
		foreach ($data as $key => $value) {
			parse_str(base64_decode($value), $data[$key]);
		}

		return $data;
	}

	/**
	 * Return product selection grid in ahax
	 *
	 */
	public function ProductSelectionGridAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$gridBlock = $this->getLayout()->createBlock('Purchase/Order_Edit_Tabs_ProductSelection')
				->setOrderId($po_num)
				->setGridUrl($this->getUrl('*/*/ProductSelectionGridOnly', array('_current' => true, 'po_num' => $po_num)));
		$serializerBlock = $this->_createSerializerBlock('add_product', $gridBlock, $gridBlock->getSelectedProducts());
		$this->_outputBlocks($gridBlock, $serializerBlock);
	}

	/**
	 * Return deliveries tab for purchase order
	 *
	 */
	public function PurchaseOrderDeliveryTabGridAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$gridBlock = $this->getLayout()->createBlock('Purchase/Order_Edit_Tabs_Deliveries');

		$this->loadLayout();
		$this->getResponse()->setBody($gridBlock->toHtml());
	}

	/**
	 * Return supply needs grid in ajax (first call)
	 *
	 */
	public function SupplyNeedsGridAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$gridBlock = $this->getLayout()->createBlock('Purchase/Order_Edit_Tabs_SupplyNeeds')
				->setOrderId($po_num)
				->setGridUrl($this->getUrl('*/*/SupplyNeedsGridOnly', array('_current' => true, 'po_num' => $po_num)));
		$serializerBlock = $this->_createSerializerBlock('add_product', $gridBlock, $gridBlock->getSelectedProducts());
		$this->_outputBlocks($gridBlock, $serializerBlock);
	}

	public function SupplyNeedsGridOnlyAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$this->loadLayout();
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('Purchase/Order_Edit_Tabs_SupplyNeeds')->setOrderId($po_num)->toHtml()
		);
	}

	public function ProductSelectionGridOnlyAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$this->loadLayout();
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('Purchase/Order_Edit_Tabs_ProductSelection')->setOrderId($po_num)->toHtml()
		);
	}

	public function ProductsDeliveryGridAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$order = mage::getModel('Purchase/Order')->load($po_num);
		$this->loadLayout();
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('Purchase/Order_Edit_Tabs_ProductDelivery')->setOrder($order)->toHtml()
		);
	}

	public function ProductSerialsGridAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$this->loadLayout();
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('Purchase/Order_Edit_Tabs_Serials')->setOrderId($po_num)->toHtml()
		);
	}

	/**
	 * Ajoute un produit a une commande
	 *
	 */
	public function AddProductToOrderAction() {
		//recupere les infos
		$ProductId = $this->getRequest()->getParam('ProductId');
		$OrderId = $this->getRequest()->getParam('OrderId');
		$order = mage::getModel('Purchase/Order')->load($OrderId);

		//Verifie si le produit est d�ja pr�sent dans la commande
		$Products = $order->getProducts();
		$ok = true;
		foreach ($Products as $Product) {
			if ($Product->getpop_product_id() == $ProductId) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('Product already exists in order'));
				$ok = false;
				break;
			}
		}

		//Ajoute le produit a la commande
		if ($ok) {
			try {
				$order->AddProduct($ProductId);

				//Confirme
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Products successfully Added'));
			} catch (Exception $ex) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('Error adding Product'));
			}
		}

		//redirige
		$this->_redirect('Purchase/Orders/Edit/po_num/' . $OrderId);
	}

	/**
	 * Export des commandes au format csv
	 *
	 */
	public function exportCsvAction() {
		$fileName = 'purchase_orders.csv';
		$content = $this->getLayout()->createBlock('Purchase/Order_Grid')
				->getCsv();

		$this->_prepareDownloadResponse($fileName, $content);
	}

	/**
	 * Save product sale price
	 *
	 */
	public function savePriceAction() {
		//retrieve datas
		$productId = $this->getRequest()->getPost('pricer_product_id');
		$salePrice = $this->getRequest()->getPost('pricer_sell_price');
		$salePriceInclTax = $this->getRequest()->getPost('pricer_sell_price_ttc');

		//save price & confirm
		$product = mage::getModel('catalog/product')->load($productId);

		//change price depending of price type (incl tax or excl tax)
		if (mage::getStoreConfig('tax/calculation/price_includes_tax'))
			$priceToUse = $salePriceInclTax;
		else
			$priceToUse = $salePrice;

		$product->setPrice($priceToUse)->save();
	}

	/**
	 * Print barcode labels
	 *
	 */
	public function PrintBarcodeLabelsAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		try {
			$order = mage::getModel('Purchase/Order')->load($po_num);

			//create an array with products
			$products = array();
			foreach ($order->getStockMovements() as $stockMovement) {
				$productId = $stockMovement->getsm_product_id();
				$qty = $stockMovement->getsm_qty();
				if (!isset($products[$productId]))
					$products[$productId] = 0;
				$products[$productId] += $qty;
			}

			//generate PDF
			$obj = mage::getModel('AdvancedStock/Pdf_BarcodeLabels');
			$pdf = $obj->getPdf($products);
			$this->_prepareDownloadResponse(mage::helper('purchase')->__('Barcode labels') . '.pdf', $pdf->render(), 'application/pdf');
		} catch (Exception $ex) {
			die('An error occured : ' . $ex->getMessage());
		}
	}

	/**
	 * Method to refresh orders's products grid using ajax
	 */
	public function ProductsGridAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$this->loadLayout();
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('Purchase/Order_Edit_Tabs_ProductsGrid')->setOrderId($po_num)->toHtml()
		);
	}

	/**
	 * Export PO to csv
	 */
	public function csvExportAction() {
		$po_num = $this->getRequest()->getParam('po_num');
		$po = mage::getModel('Purchase/Order')->load($po_num);
		$model = mage::getModel('Purchase/Order_Csv');
		$model->setOrder($po);

		$content = $model->getCsv();
		$contentType = $model->getContentType();
		$fileName = $model->getFileName();
		$this->_prepareDownloadResponse($fileName, $content, $contentType);
	}

	/**
	 * Display liabilities grid
	 */
	public function LiabilitiesAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

}