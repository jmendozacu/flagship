<?php
class Orders_Editorder_Adminhtml_EditorderController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('editordertab')->_title($this->__('Edit Orders'));
        $this->_addLeft($this->getLayout()->createBlock('core/text')->setText('<h1>Orders</h1>'));
        $orderInfo = $this->startTable(); 
        if (isset($_GET['order_entity_id']))
        {
            $order = $this->getOrder($_GET['order_entity_id']);   
            $text = "<h1>Order # " . $order['increment_id'] . "</h1>";
            $block = $this->getLayout()->createBlock('core/text')->setText($text);
            $this->_addContent($block);
        }
        else
        {
            $orders = Mage::getModel('editorder/order')->getCollection()->getData();
            rsort($orders);
            $order = array_shift($orders);
            $text = "<h1>Order # " . $order['increment_id'] . "</h1>";
            $block = $this->getLayout()->createBlock('core/text')->setText($text);
            $this->_addContent($block);
        }
        $billingAddress = Mage::getModel('sales/order_address')->load($order['billing_address_id']);
        $shippingAddress = Mage::getModel('sales/order_address')->load($order['shipping_address_id']);

        if ( isset($_GET['increment_id']) && $_GET['increment_id'] == $order['increment_id'] ) 
        {
            if (!isset($_GET['order_entity_id']) || $_GET['order_entity_id'] == $order['entity_id'])
            {
                $salesAddressModel = Mage::getModel('sales/order_address');
                $editOrderModel = Mage::getModel('editorder/order');

                if (isset($_GET['billing_name']) 
                && (isset($_GET['billing_street'])) 
                && (isset($_GET['billing_city'])) 
                && (isset($_GET['billing_region_dropdown'])) 
                && (isset($_GET['billing_postal_code'])) 
                && (isset($_GET['billing_country'])) 
                && (isset($_GET['billing_telephone'])))
                {
                    $explodedBillingName = explode(' ', $_GET['billing_name']);
                    $this->editBillingAddress($order['billing_address_id'], $salesAddressModel, $explodedBillingName[0], $explodedBillingName[1], $_GET['billing_street'], $_GET['billing_city'], $_GET['billing_region_dropdown'], $_GET['billing_postal_code'], $_GET['billing_country'], $_GET['billing_telephone']);
                    $billingAddress['firstname'] = $explodedBillingName[0];
                    $billingAddress['lastname'] = $explodedBillingName[1]; 
                    $billingAddress['street'] = $_GET['billing_street'];
                    $billingAddress['city'] = $_GET['billing_city'];
                    $billingAddress['postcode'] = $_GET['billing_postal_code'];
                    $billingAddress['telephone'] = $_GET['billing_telephone'];
                    $billingAddress['region_id'] = $_GET['billing_region_dropdown'];
                    $billingAddress['country_id'] = $_GET['billing_country'];
                }

                if (isset($_GET['shipping_name']) 
                && (isset($_GET['shipping_street'])) 
                && (isset($_GET['shipping_city'])) 
                && (isset($_GET['shipping_region_dropdown'])) 
                && (isset($_GET['shipping_postal_code'])) 
                && (isset($_GET['shipping_country'])) 
                && (isset($_GET['shipping_telephone'])))
                {
                    $explodedShippingName = explode(' ', $_GET['shipping_name']);
                    $this->editShippingAddress($order['shipping_address_id'], $salesAddressModel, $explodedShippingName[0], $explodedShippingName[1], $_GET['shipping_street'], $_GET['shipping_city'], $_GET['shipping_region_dropdown'], $_GET['shipping_postal_code'], $_GET['shipping_country'], $_GET['shipping_telephone']);
                    $shippingAddress['firstname'] = $explodedShippingName[0];
                    $shippingAddress['lastname'] = $explodedShippingName[1];
                    $shippingAddress['street'] = $_GET['shipping_street'];
                    $shippingAddress['city'] = $_GET['shipping_city'];
                    $shippingAddress['postcode'] = $_GET['shipping_postal_code'];
                    $shippingAddress['telephone'] = $_GET['shipping_telephone'];
                    $shippingAddress['region_id'] = $_GET['shipping_region_dropdown'];
                    $shippingAddress['country_id'] = $_GET['shipping_country'];
                }

                if (isset($_GET['firstname']) 
                && (isset($_GET['lastname'])) 
                && (isset($_GET['customer_email'])))
                {
                    $this->editAccountInfo($order['entity_id'], $editOrderModel, $_GET['customer_prefix'], $_GET['firstname'], $_GET['lastname'], $_GET['customer_email']);
                    $order['customer_firstname'] = $_GET['firstname'];
                    $order['customer_lastname'] = $_GET['lastname'];
                    $order['customer_prefix'] = $_GET['customer_prefix'];
                    $order['customer_email'] = $_GET['customer_email'];
                }
            }
        }
        $this->createOrderSidebar();

        $orderInfo .= "<form name =" . $order['entity_id'] . ' ' . "action=\"#\"" . ' ' . "method=\"get\">";
        if (isset($_GET['order_entity_id']))
        {
            $orderInfo .= "<input type=\"hidden\" name=\"order_entity_id\" value=" . $_GET['order_entity_id'] . ">";
        }
        $orderInfo .= "<input type=\"hidden\" name=\"increment_id\" value=" . $order['increment_id'] . ">";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("customer_prefix", $order['customer_prefix']);
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("firstname", $order['customer_firstname']);
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("lastname", $order['customer_lastname']);
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("customer_email", $order['customer_email']);
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr><td>&nbsp</td></tr>";    
        $orderInfo .= "<tr><td>&nbsp</td></tr>";    
    
        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";
        $orderInfo .= "</tr>";
      
        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "<b>Billing Address</b>";
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Billing Name";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $fullBillingName = $billingAddress['firstname'] . ' ' . $billingAddress['lastname'];
        $orderInfo .= $this->createTextField("billing_name", "$fullBillingName");
        $orderInfo .= "</td>";
    
        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Billing Street";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $billingStreet = $billingAddress->getStreet();
        $orderInfo .= $this->createTextField("billing_street", $billingStreet[0]);
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Billing City";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("billing_city", $billingAddress->getCity());
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Billing State";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $billingInfo = Mage::getResourceModel('directory/region_collection')->load()->toOptionArray();
        $orderInfo .= "<select name=\"billing_region_dropdown\" style=\"width: 175px\">";
        $orderInfo .= "<option value=\"" . $billingAddress['region_id'] . "\">" . $billingAddress['region'] . "</option>";
        foreach($billingInfo as $billingRegion)
        {
            if ( $billingRegion['value'] != "")
            {
                if ( $billingRegion['value'] == $_GET['billing_region_dropdown'] ) 
                {
                    $orderInfo .= "<option value=\"" . $billingRegion['value'] . "\" selected>" . $billingRegion['label'] . "</option>";
                } 
                else 
                {
                    $orderInfo .= "<option value=\"" . $billingRegion['value'] . "\">" . $billingRegion['label'] . "</option>";
                }
            }
        }
        $orderInfo .= "</select>";
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Billing Postal Code";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("billing_postal_code", $billingAddress->getPostcode());
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Billing Country";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $billingCountries = Mage::getResourceModel('directory/country_collection')->load()->toOptionArray();
        if ($billingAddress['country_id'] != "")
        {
            $originalBillingCountryName = Mage::getModel('directory/country')->loadByCode($billingAddress['country_id'])->getName();
        }
        $orderInfo .= "<select name=\"billing_country\" style=\"width: 175px\">";
        $orderInfo .= "<option value=\"" . $billingAddress['country_id'] . "\">" . $originalBillingCountryName . "</option>";
        foreach ($billingCountries as $billingCountry)
        {
            if ($billingCountry['value'] != "")
            {
                if ( $billingCountry['value'] == $_GET['billing_country'] )
                {
                    $orderInfo .= "<option value=\"" . $billingCountry['value'] . "\" selected>" . $billingCountry['label'] . "</option>";
                }
                else
                {
                    $orderInfo .= "<option value=\"" . $billingCountry['value'] . "\">" . $billingCountry['label'] . "</option>";
                }
            }
        }
        $orderInfo .= "</select>";
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Billing Telephone";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("billing_telephone", $billingAddress->getTelephone());
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr><td>&nbsp</td></tr>";    

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "<b>Shipping Address</b>";
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Shipping Name";
        $orderInfo .= "</td>"; 

        $orderInfo .= "<td>";
        $fullShippingName = $shippingAddress['firstname'] . ' ' . $shippingAddress['lastname'];
        $orderInfo .= $this->createTextField("shipping_name", "$fullShippingName");
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Shipping Street";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $shippingStreet = $shippingAddress->getStreet();
        $orderInfo .= $this->createTextField("shipping_street", $shippingStreet[0]);
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Shipping City";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("shipping_city", $shippingAddress->getCity());
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Shipping State";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $shippingInfo = Mage::getResourceModel('directory/region_collection')->load()->toOptionArray();
        $orderInfo .= "<select name=\"shipping_region_dropdown\" style=\"width: 175px\">";
        $orderInfo .= "<option value=\"" . $shippingAddress['region_id'] . "\">" . $shippingAddress['region'] . "</option>";
        foreach ($shippingInfo as $shippingRegion)
        {
            if ( $shippingRegion['value'] != "")
            {
                if ( $shippingRegion['value'] == $_GET['shipping_region_dropdown'] )
                {
                    $orderInfo .= "<option value=\"" . $shippingRegion['value'] . "\" selected>" . $shippingRegion['label'] . "</option>";
                }
                else
                {
                    $orderInfo .= "<option value=\"" . $shippingRegion['value'] . "\">" . $shippingRegion['label'] . "</option>";
                }
            }
        }
        $orderInfo .= "</select>";
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Shipping Postal Code";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("shipping_postal_code", $shippingAddress->getPostcode());
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";
        $orderInfo .= "<tr>";
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Shipping Country";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $shippingCountries = Mage::getResourceModel('directory/country_collection')->load()->toOptionArray();
        if ($shippingAddress['country_id'] != "")
        {
            $originalShippingCountryName = Mage::getModel('directory/country')->loadByCode($shippingAddress['country_id'])->getName();
        }
        $orderInfo .= "<select name=\"shipping_country\" style=\"width: 175px\">";
        $orderInfo .= "<option value=\"" . $shippingAddress['country_id'] . "\">" . $originalShippingCountryName . "</option>";
        foreach ($shippingCountries as $shippingCountry)
        {
            if ( $shippingCountry['value'] != "")
            {
                if (( $shippingCountry['value'] == $_GET['shipping_country'] ) && (isset($_GET['shipping_country'])))
                {
                    $orderInfo .= "<option value=\"" . $shippingCountry['value'] . "\" selected>" . $shippingCountry['label'] . "</option>";
                }
                else
                {
                    $orderInfo .= "<option value=\"" . $shippingCountry['value'] . "\">" . $shippingCountry['label'] . "</option>";
                }
            }
        }
        $orderInfo .= "</select>";
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr>"; 
        $orderInfo .= "<td></td>";

        $orderInfo .= "<td style=\"font-size: 13px\">";
        $orderInfo .= "Shipping Telephone";
        $orderInfo .= "</td>";

        $orderInfo .= "<td>";
        $orderInfo .= $this->createTextField("shipping_telephone", $shippingAddress->getTelephone());
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr><td>&nbsp</td></tr>";    
        $orderInfo .= "<tr>";
        $orderInfo .= "<td colspan=\"4\" align=\"center\">";
        $orderInfo .= "<input type=\"submit\" value=\"Submit Changes\" />";
        $orderInfo .= "</td>";
        $orderInfo .= "</tr>";

        $orderInfo .= "<tr><td>&nbsp</td></tr>";    
        $orderInfo .= "</form>";

        $orderInfo .= "</table>";
        $this->getTextBlock($orderInfo);
        
        $this->renderLayout();
    }
    
    public function searchOrderData($keyword, $order)
    {
       $lowerCaseKeyword = strtolower($keyword);
       $lowerCaseOrderValue = array_map('strtolower', $order);
       
       foreach($lowerCaseOrderValue as $info)
       {
           if (preg_match("/$lowerCaseKeyword/", $info))
           {
               return true;
           }
       }
    }
    
    public function searchBillingAddress($keyword, $address)
    {
        $lowerCaseKeyword = strtolower($keyword);
        $lowerCaseAddressValue = array_map('strtolower', $address);

        foreach($lowerCaseAddressValue as $info)
        {
            if (preg_match("/$lowerCaseKeyword/", $info))
            {
                return true;
            }
        }
    }
    
    public function searchShippingAddress($keyword, $address)
    {
        $lowerCaseKeyword = strtolower($keyword);
        $lowerCaseAddressValue = array_map('strtolower', $address);
        foreach($lowerCaseAddressValue as $info)
        {
            if (preg_match("/$lowerCaseKeyword/", $info))
            {
                return true;
            }
        }
    }

    public function createOrderSidebar()
    {
        $collectiveOrderData = Mage::getModel('editorder/order')->getCollection()->getData();
        //Display most recent order at the top
        rsort($collectiveOrderData); 

        $searchBar = "<form name=\"search_orders\" method=\"get\"" . ' ' . "action=\"#\">";
        $searchBar .= "<input type=\"text\" name=\"order_search_bar\" size=\"23\" maxlength=\"28\" value=\"Search for an Order\" onFocus=this.value=\"\">";
        $searchBar .= "<input type=\"submit\" value=\"Search\">";
        $searchBar .= "</form>";
        $searchBar .= "<br><br>";
        $this->_addLeft($this->getLayout()->createBlock('core/text')->setText($searchBar));
        if (!isset($_GET['order_search_bar']) || (isset($_GET['show_all_orders'])))
        {  
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $pageResults = array_chunk($collectiveOrderData, 15);
            $pageCount = count($pageResults);
            $paginatedOrders = $this->paginateOrders($pageCount, $pageResults);
        }
        elseif (isset($_GET['order_search_bar']) 
        && ($_GET['order_search_bar'] != "Search for an Order") 
        && ($_GET['order_search_bar'] != ""))
        {
            $this->getSearchHtml($collectiveOrderData, $_GET['order_search_bar']);
        }
        elseif (($_GET['order_search_bar'] == "") || ($_GET['order_search_bar'] == "Search for an Order"))
        {
            $errorText = "<br>Please enter a search term.";
            $this->_addLeft($this->getLayout()->createBlock('core/text')->setText($errorText));
        }
        $showAllOrders = "<br><br>";
        $showAllOrders .= "<form method=\"get\">";
        $showAllOrders .= "<input type=\"submit\" value=\"Show All Orders\">";
        $showAllOrders .= "<input type=\"hidden\" name=\"show_all_orders\" value=\"1\">";
        $showallOrders .= "</form>";
        $showAllOrders .= "<br><br>";
        $this->_addLeft($this->getLayout()->createBlock('core/text')->setText($paginatedOrders));
        $this->_addLeft($this->getLayout()->createBlock('core/text')->setText($showAllOrders));
    }
  
    public function getFormCode($incrementId, $firstname, $lastname, $entityId)
    {
        if ($incrementId != "")
        {
            $form = "<form name=" . $incrementId . ' ' . "action=\"?increment_id=" . $incrementId . "\"" . ' ' . "method=\"get\">";
            $value = "Order # " . $incrementId . ': ' . $firstname . ' ' . $lastname . '  ';
            $form .= "<input type=\"submit\" value=\"$value\" style=\"width: 210px\">";
            $form .= "<input type=\"hidden\" name=\"order_entity_id\" value=" . $entityId . ">";
            if (isset($_GET['page']))
            {
                $form .= "<input type=\"hidden\" name=\"page\" value=" . $_GET['page'] . ">";
            }
            $form .= "</form>";
            return $form;
        }
    }

    public function getSearchHtml($orders, $searchTerm)
    {
        $orderCount = 0;
        $billingCount = 0;
        $shippingCount = 0;
        $count = 0;
        $orderSearchForm = "";
        $billingSearchForm = "";
        $shippingSearchForm = "";
        foreach($orders as $order)
        {
            $orderCaseInsensitive = array_change_key_case($order, CASE_LOWER);
            $orderResults = $this->searchOrderData($searchTerm, $orderCaseInsensitive);
            if ($orderResults == true)
            {
                if ($orderCount < 1)
                {
                    $orderSearchForm .= "<b>Order Search Results:</b><br>";
                }
                $orderSearchForm .= $this->getFormCode($order['increment_id'], $order['customer_firstname'], $order['customer_lastname'], $order['entity_id']);
                $orderCount++;
            }
        }
        foreach($orders as $order)
        {
            $billingAddress = Mage::getModel('sales/order_address')->load($order['billing_address_id'])->getData();
            $billingAddressCaseInsensitive = array_change_key_case($billingAddress, CASE_LOWER);
            $billingAddressResults = $this->searchBillingAddress($searchTerm, $billingAddressCaseInsensitive);
            if ($billingAddressResults == true)
            {
                if ($billingCount < 1)
                {
                    $billingSearchForm .= "<br><br><b>Billing Address Results:</b><br>";
                }
                $billingSearchForm .= $this->getFormCode($order['increment_id'], $order['customer_firstname'], $order['customer_lastname'], $order['entity_id']);
                $billingCount++;
            }
        }
        foreach($orders as $order)
        {
            $shippingAddress = Mage::getModel('sales/order_address')->load($order['shipping_address_id'])->getData();
            $shippingAddressCaseInsensitive = array_change_key_case($shippingAddress, CASE_LOWER);
            $shippingAddressResults = $this->searchShippingAddress($searchTerm, $shippingAddressCaseInsensitive);
            if ($shippingAddressResults == true)
            {
                if ($shippingCount < 1)
                {
                    $shippingSearchForm .= "<br><br><b>Shipping Address Results:</b><br>";
                }
                $shippingSearchForm .= $this->getFormCode($order['increment_id'], $order['customer_firstname'], $order['customer_lastname'], $order['entity_id']);
                $shippingCount++;
            }
        }
        $count = ($orderCount + $billingCount + $shippingCount);
        if ($count == 0)
        {
            $form = "<b>No Results were Found</b>";
            $this->_addLeft($this->getLayout()->createBlock('core/text')->setText($form));
        }
        $this->_addLeft($this->getLayout()->createBlock('core/text')->setText($orderSearchForm));
        $this->_addLeft($this->getLayout()->createBlock('core/text')->setText($billingSearchForm));
        $this->_addLeft($this->getLayout()->createBlock('core/text')->setText($shippingSearchForm));
    }
 
    public function startTable()
    {
        $table = "<table>";
        $table .= "<tr>";
        $table .= "<th>Prefix</th>";
        $table .= "<th>First Name</th>";
        $table .= "<th>Last Name</th>";
        $table .= "<th>Email</th>";
        $table .= "</tr>";
        return $table;
    }

    public function getOrder($entityId)
    {
        $specificOrder = Mage::getModel('editorder/order');
        $specificOrderData = $specificOrder->load($entityId)->getData();
        return $specificOrderData;
    }

    public function getTextBlock($text)
    {
        $block = $this->getLayout()->createBlock('core/text')->setText($text);
        $this->_addContent($block);
    }

    public function createTextField($inputName, $info)
    {
        if ($inputName == "customer_prefix")
        {
            $form = "<input type=\"text\" name=\"$inputName\" size=\"5\" value=\"$info\">";
            $form .= "<br>";
            return $form;
        }
        else
        {
            $form = "<input type=\"text\" name=\"$inputName\" size=\"30\" value=\"$info\">";
            $form .= "<br>";
            return $form;
        }
    }
 
    public function paginateOrders($pageCount, $pageResults)
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $pageLinks = "";
        if ($page != "")
        {
            $arrayValue = ($page - 1);
            foreach($pageResults[$arrayValue] as $order)
            {
                if ($order['increment_id'] != "")
                {
                    if ($order['customer_firstname'] != "")
                    {
                        $pageLinks .= $this->getFormCode($order['increment_id'], $order['customer_firstname'], $order['customer_lastname'], $order['entity_id']);
                    }
                    else
                    { 
                        $billingAddress = Mage::getModel('sales/order_address')->load($order['billing_address_id']);
                        $pageLinks .= $this->getFormCode($order['increment_id'], $billingAddress['firstname'], $billingAddress['lastname'], $order['entity_id']);
                    }
                }
                else
                {
                    continue;
                }
            }
        }
        $pageLinks .= "<br><br>";
        $queryString = "";
        $searchTerm = $_GET['order_search_bar'];
        foreach ($_GET as $key => $value)
        {
            if ($key != "page")
            {
                $queryString .= "$key=$value&amp;";
            }
        }
        for ($i = 1; $i <= $pageCount; $i++)
        {
            $pageLinks .= "<a " . ($i == $page ? "class=\"selected\" " : "");
            if ($searchTerm != "")
            {
                $pageLinks .= "href=\"?{$queryString}page=$i&order_search_bar=$searchTerm";
            }
            else
            {
                $pageLinks .= "href=\"?{$queryString}page=$i";
            }
            $pageLinks .= "\">$i</a> ";
        }
        return $pageLinks;
    }

    public function editBillingAddress($id, $model, $firstName, $lastName, $street, $city, $regionId, $postCode, $countryId, $telephone)
    {
        $model->load($id);
        $model->setFirstname($firstName);
        $model->setLastname($lastName);
        $model->setStreet($street);
        $model->setCity($city);
        $model->setRegionId($regionId);
        $model->setPostcode($postCode);
        $model->setCountryId($countryId);
        $model->setTelephone($telephone);
        $model->save();
    }
   
    public function editShippingAddress($id, $model, $firstName, $lastName, $street, $city, $regionId, $postCode, $countryId, $telephone)
    {
        $model->load($id);
        $model->setFirstname($firstName);
        $model->setLastname($lastName);
        $model->setStreet($street);
        $model->setCity($city);
        $model->setRegionId($regionId);
        $model->setPostcode($postCode);
        $model->setCountryId($countryId);
        $model->setTelephone($telephone);
        $model->save();
    }

    public function editAccountInfo($entityId, $model, $prefix, $firstName, $lastName, $email)
    {
        $model->load($entityId);
        $model->setCustomerPrefix($prefix);
        $model->setCustomerFirstname($firstName);
        $model->setCustomerLastname($lastName);
        $model->setCustomerEmail($email);
        $model->save();
    }
}
?>
