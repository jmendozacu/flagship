<?php

// store ID 2 = magestore2
// store ID 5 = magestore5
// store ID 6 = magestore3
// store ID 7 = magestore4

$client = new SoapClient('http://prolinestores.com/api/soap?wsdl');
$session = $client->login('test', 'abcd1234');

$allCategories = $client->call($session, 'catalog_category.tree');
foreach ($allCategories as $category) {
    if ( is_array($category) ) {
        foreach ( $category as $cat ) {
            // print_r($cat) . "<br><br>";    
            echo "Name: " . $cat["name"] . "<br>";
            echo "ID: " . $cat["category_id"] . "<br>";
            if ( is_array($cat["children"]) ) {
                foreach ( $cat["children"] as $children ) {
                   echo "Name: " . $children["name"] . "<br>";
                   echo "Category ID: " . $children["category_id"] . "<br>";
                   $assignedProducts4 = $client->call($session, 'category.assignedProducts', array($children["category_id"]));
                   foreach ( $assignedProducts4 as $prod ) {
                       echo "ID for product: " . $prod['product_id'] . "<br>";
                       echo "SKU: " . $prod['sku'] . "<br>";
                   }
                 }
            }
        }
    }
}
// var_dump($allCategories);
return;

$SKU = "PLJW 101.36";
$categoryId = 3;
$storeId = 2;

// get list of everything for store 2


$assignedProducts_magestore2 = $client->call($session, 'category.assignedProducts', array($categoryId, 2,));
$assignedProducts_magestore3 = $client->call($session, 'category.assignedProducts', array($categoryId, 6,));
$assignedProducts_magestore4 = $client->call($session, 'category.assignedProducts', array($categoryId, 7,));
$assignedProducts_magestore5 = $client->call($session, 'category.assignedProducts', array($categoryId, 5,));

echo "<table>";
echo "<tr>";
echo "<th>Store ID</th>";
echo "<th>Product ID</th>";
echo "<th>SKU</th>";
echo "<th>Price</th>";
echo "<th>Image</th>";
echo "</tr>";

foreach ($assignedProducts_magestore2 as $product) {
    if ( $product['sku'] != $SKU ) {
        continue;
    }
    $price = 0;
    $current_store = "magestore2";
    echo "<tr>";
    echo "<td>2</td>";
    echo "<td>" .  $product['product_id'] . "</td>";
    echo "<TD>" . $product['sku'] . "</td>";
    $special_price = $client->call($session, 'product_tier_price.info', $SKU);
    foreach ( $special_price as $pr ) {
        if ( $pr["website"] == $current_store ) {
            echo "<td>" . $pr["price"] . "</td>";
        }
    }
    $media_list = $client->call($session, 'product_media.list', $SKU);
    echo "<td>";
    echo "<img width=\"350\" height=\"350\" src=\"" . $media_list[0]["url"] . "\">";
    echo "</td></tr>";
}

foreach ($assignedProducts_magestore3 as $product) {
    if ( $product['sku'] != $SKU ) {
        continue;
    }
    $price = 0;
    $current_store = "magestore3";
    echo "<tr>";
    echo "<td>3</td>";
    echo "<td>" .  $product['product_id'] . "</td>";
    echo "<TD>" . $product['sku'] . "</td>";
    $special_price = $client->call($session, 'product_tier_price.info', $SKU);
    var_dump($special_price);
    foreach ( $special_price as $pr ) {
        if ( $pr["website"] == $current_store ) {
            echo "<td>" . $pr["price"] . "</td>";
        }
    }
    $media_list = $client->call($session, 'product_media.list', $SKU);
    echo "<td>";
    echo "<img width=\"350\" height=\"350\" src=\"" . $media_list[1]["url"] . "\">";
    echo "</td></tr>";
}

foreach ($assignedProducts_magestore4 as $product) {
    if ( $product['sku'] != $SKU ) {
        continue;
    }
    $price = 0;
    $current_store = "magestore4";
    echo "<tr>";
    echo "<td>4</td>";
    echo "<td>" .  $product['product_id'] . "</td>";
    echo "<TD>" . $product['sku'] . "</td>";
    $special_price = $client->call($session, 'product_tier_price.info', $SKU);
    foreach ( $special_price as $pr ) {
        if ( $pr["website"] == $current_store ) {
            echo "<td>" . $pr["price"] . "</td>";
        }
    }
    $media_list = $client->call($session, 'product_media.list', $SKU);
    echo "<td>";
    echo "<img width=\"350\" height=\"350\" src=\"" . $media_list[2]["url"] . "\">";
    echo "</td></tr>";
}

foreach ($assignedProducts_magestore5 as $product) {
    if ( $product['sku'] != $SKU ) {
        continue;
    }
    $price = 0;
    $current_store = "magestore5";
    echo "<tr>";
    echo "<td>5</td>";
    echo "<td>" .  $product['product_id'] . "</td>";
    echo "<TD>" . $product['sku'] . "</td>";
    $special_price = $client->call($session, 'product_tier_price.info', $SKU);
    foreach ( $special_price as $pr ) {
        if ( $pr["website"] == $current_store ) {
            echo "<td>" . $pr["price"] . "</td>";
        }
    }
    $media_list = $client->call($session, 'product_media.list', $SKU);
    echo "<td>";
    echo "<img width=\"350\" height=\"350\" src=\"" . $media_list[3]["url"] . "\">";
    echo "</td></tr>";
}

echo "</table>";
?>
