<?php

/**
 * GoMage.com
 *
 * GoMage Feed Pro
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2013 GoMage.com (http://www.gomage.com)
 * @author       GoMage.com
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 3.2
 * @since        Class available since Release 3.2
 */

class GoMage_Feed_Model_Amazon  extends Varien_Object {

    protected $url_upc;
    protected $divide_start;
    protected $divide_end;
    
    protected function _construct() {        

        $this->url_upc = 'http://www.checkupc.com/search.php?keyword='; 
        $this->divide_start = '<td width="100%">';
        $this->divide_end = '</td>';
        
	}
    
            public function getContent($url = '')
               {
			   if($url == '')
			   $url = $this->url_upc;
			   
                            $this->sleep();  
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_HEADER, 0); 
                            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_URL, $url);
                            $ret = trim(curl_exec($ch));
                            curl_close($ch);  
                            return $ret;
              }
          
		
              private function sleep() {
               sleep(mt_rand(0, 0.1));
               }
               
               public function getCodeAmazon($product, $type){                                     
           $product_name = $product->getName();      
            $_product_name = str_replace('','+',$product_name);  
            $content = $this->getContent($this->url_upc.$_product_name);           
            preg_match_all("|".$this->divide_start."(.*)".$this->divide_end."|sUSi" , $content, $array);   
            foreach ($array as $value) {
                foreach ($value as $key => $val) { 

                $len = iconv_strlen($product_name);

                    WHILE($len > 0 ){
                    if (preg_match('|'. preg_quote($product_name) .'|i', $value[$key])){
                                $content_upc = $value[$key];  
                                break;
                    }
                    $product_name =  substr($product_name, 0, iconv_strlen($product_name)-5);
                    $len = iconv_strlen($product_name);

                    }

                }
            }         
            
             
           if(!empty($content_upc)){
                              
                    $dom = new DOMDocument;          
                    $dom->loadHTML($content_upc);
                    $xpath = new DOMXPath($dom);
                    $nodes = $xpath->query('//a/@href');
                    foreach($nodes as $href) {
                    $url_upc = $href->nodeValue;                     
                    }
           
            $content = $this->getContent($url_upc);
            preg_match_all("|<table(.*)</table>|sUSi" , $content, $array);
            preg_match_all("|<tr>(.*)</tr>|sUSi" , $array[0][2], $array_upc);

            foreach ($array_upc as  $value) {
                foreach ($value as $key => $val) {
                     
                    switch ($type) {
                        
                      case 'upc':
                       
                         if (preg_match("/>UPC</i", $value[$key])){
                    preg_match_all("|<td class=\"padding\">(.*)</td>|sUSi" , $value[$key], $array_upc_code);
                    $code = str_replace('<td class="padding">', '',$array_upc_code[0][0]); 
                   $code = str_replace('</td>', '', $code);  
                
                    }
                
                      break;  
                        case 'ean':
                        if (preg_match("/>EAN</i", $value[$key])){
                    preg_match_all("|<td class=\"padding\">(.*)</td>|sUSi" , $value[$key], $array_ean_code);
                    $code = str_replace('<td class="padding">', '',$array_ean_code[0][0]); 
                    $code = str_replace('</td>', '', $code);  
                    }
                      break; 
                         case 'mpn':
                           
                      if (preg_match("/>MPN</i", $value[$key])){
                        preg_match_all("|<td class=\"padding\">(.*)</td>|sUSi" , $value[$key], $array_mpn_code);
                    $code = str_replace('<td class="padding">', '',$array_mpn_code[0][0]); 
                    $code = str_replace('</td>', '', $code);  
                      
                    }
                      break;

                    }             
                      
                }

            }
        }else{
          $code = '';  
        }
      return $code;
   }
               
               public function createAttribute($label, $code)
                    {
                  
                $model=Mage::getModel('eav/entity_setup','core_setup');
                $data=array(
                'type'=>'varchar',
                'input'=>'text',
                'label'=>$label,
                'global'=>Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'is_required'=>'0',
                'is_comparable'=>'0',
                'is_searchable'=>'0',
                'is_unique'=>'1',
                'is_configurable'=>'1',
                'use_defined'=>'1'
                );

                $model->addAttribute('catalog_product', $code ,$data);
            }

            public function codeGenerationItem($name, $type,  $condition){
               
         $products = Mage::getModel('catalog/product')->getCollection();
         $products->addAttributeToSelect('name');
         $products->addAttributeToSelect($name);
                foreach($products as $key =>$product) {
                    if($condition == 0){
                  $code = $this->getCodeAmazon($product, $type);                   
                  $product->setData($name, $code)->save();
                        
                    }
                     if($condition == 1){                       
                  $vall_atr =  $product->getData($name);                
                   if(empty($vall_atr)){       
              $code = $this->getCodeAmazon($product, $type);      
                 $product->setData($name, $code)->save();
                   }
                    }
                }
            }
            
            public function saveConfig($array){
                $core_config = Mage::getModel('core/config');
                $core_config->saveConfig('gomage_feedpro/amazon/config', serialize($array), 'default', 0); 
            }
           
}