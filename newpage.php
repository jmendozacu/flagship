<?php
$client = new SoapClient('http://magento.aivector.com/api/soap?wsdl');
$session = $client->login('test', 'abcd1234');

echo "done establishing login: $session<br>";

// get a list of all the categories
// $allCategories = $client->call($session, 'category.tree');
// print_r($allCategories);

// $categoryId = 5; // Put here your category id
$storeId = 3; // You can add store level

echo "Pulling products from store: store id: $storeId <br>"; 
$assignedProducts = $client->call($session, 'category.assignedProducts', array(2, 1));

echo "ASSIGNED PRODUCTS <BR><BR>";
var_dump($assignedProducts); // Will output assigned products.

// product ATTS
echo "<BR>ATTS <BR>";
$attributeSets = $client->call($session, 'product_attribute_set.list');
var_dump($attributeSets);
echo "<br>";


foreach ($assignedProducts as $product) {
    print_r($product);
//    echo "Product ID: " . $product['product_id'] . "<br>";
    $productTypes = $client->call($session, 'product.list');
    echo "<br> SKU: " . $product['sku'] . "<br>";

    echo "<br>SPECIAL PRICE INFO<BR>";
    $special_price = $client->call($session, 'product.getSpecialPrice', $product['sku']);
    print "Prince of product: " . $special_price['special_price'] . "<br>";
    print_r($special_price);
    echo "<br>DONE GETTING SPECIAL PRICE INFO<BR>";
    

    // foreach ($productTypes as $atts) {
    //     print_r($atts);
    //     echo "<br>";
   //  }
   $list_arr = Array($product['sku']);
   $media_list = $client->call($session, 'product_media.list', $list_arr);
   $counter = 0;
   foreach ($media_list as $media) {
       print_r($media);
       //  echo $media[$counter];
       // $counter++;
       echo "<br>";
    //     echo "<img width=\"350\" height=\"350\" src=\"" . $media["file"] . "\"><br>";
    }
    
    //print_r($media_list);
}

echo "<br>";
// print_r($media_list);
?>
