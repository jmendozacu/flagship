<?php
/*
 *  Created on Aug 16, 2011
 *  Author Ivan Proskuryakov - volgodark@gmail.com
 *  Copyright Proskuryakov Ivan. Ip.com © 2011. All Rights Reserved.
 *  Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
 */
?>
<?php
class Ip_Robots_Model_Item extends Mage_Core_Model_Abstract
{
    const CACHE_TAG     = 'robots_admin_item';
    protected $_cacheTag= 'robots_admin_item';

    protected function _construct()
    {
        $this->_init('robots/item');
    }
    
    public function BuildRobotsData()
    {

    	$robots ='';$rules = '';
    	$type=array(
    				'0'=>'Disallow',
    				'1'=>'Allow'
    				);
		/* 
		 * rules
		 */		
		$rulesdata = Mage::getModel('robots/item')->getCollection();
		foreach ($rulesdata as $r) {
			if ($r->getIsActive()) {
				$rules .= $type[$r['type']].': '.$r['url'].'<br>';
			}
		}		
		
		/* 
		 * all
		 */
		if (Mage::getStoreConfig('robots/options/all')) {
	    	$robots.= '# ------------------ ALL CRAWLERS [ENABLED] ------------------'.'<br>';
			$robots.= 'User-agent: *'.'<br>';
	    	$delay = Mage::getStoreConfig('robots/options/delay'); 			
	 		if ( $delay != 'none') {
				$delay ='Crawl-delay: '.$delay.'<br>' ;
				$robots.=$delay;   
	 		}	
			$robots.= 'Disallow: '.'<br>';

			
	 		$robots.=$rules; 		
		} else {
	    	$robots.= '# ------------------ ALL CRAWLERS [DISABLED] ------------------'.'<br>';
			$robots.= 'User-agent: *'.'<br>';
			$robots.= 'Disallow: /'.'<br>';
		}
		
		$robots.=Mage::getSingleton('robots/data')->AdditionalRobotsCrawlers();
		return $robots;
    }
    
   
    
	public function InstallRobots()
    {
	    $coreResource = Mage::getSingleton('core/resource') ;
    	$write = $coreResource->getConnection('core_write');
	    $table = $coreResource->getTableName('ip_robots_item');
		$sql ="TRUNCATE `".$table."`";
		$write->query($sql);
		
		$sql ="
			-- ------------------------------------------------------
			INSERT INTO `".$table."` (`type`, `url`, `comment`, `is_active`) VALUES
			(0, '/404/', '# Directories', 1),
			(0, '/app/', '# Directories', 1),
			(0, '/manage/', '# Directories', 1),
			(0, '/cgi-bin/', '# Directories', 1),
			(0, '/downloader/', '# Directories', 1),
			(0, '/includes/', '# Directories', 1),
			(0, '/js/', '# Directories', 1),
			(0, '/lib/', '# Directories', 1),
			(0, '/magento/', '# Directories', 1),
			(0, '/media/', '# Directories', 1),
			(0, '/pkginfo/', '# Directories', 1),
			(0, '/report/', '# Directories', 1),
			(0, '/skin/', '# Directories', 1),
			(0, '/stats/', '# Directories', 1),
			(0, '/var/', '# Directories', 1),
		";  
		$paths = "
			(0, '/catalog/product_compare/', '# Paths', 1),
			(0, '/catalog/category/view/', '# Paths', 1),
			(0, '/catalog/product/view/', '# Paths', 1),
			(0, '/catalog/product/gallery/', '# Paths', 1),
			(0, '/catalogsearch/', '# Paths', 1),
			(0, '/checkout/', '# Paths', 1),
			(0, '/control/', '# Paths', 1),
			(0, '/contacts/', '# Paths', 1),
			(0, '/customer/', '# Paths', 1),
			(0, '/customize/', '# Paths', 1),
			(0, '/newsletter/', '# Paths', 1),
			(0, '/poll/', '# Paths', 1),
			(0, '/review/', '# Paths', 1),
		";			
		if (Mage::getStoreConfig('web/url/use_store')) {
			$stores = Mage::getModel('core/store')->getCollection();
			foreach ($stores as $r) {
				if ($r->getIsactive()) {
					$store_code = $r->getCode();
					$data = str_replace("0, '/", "0, '/".$store_code."/", $paths);
					$sql.=$data;					
				}
			}	
		} else {
			$sql.=$paths;	
		}
		
		$sql.= "
		(0, '/sendfriend/', '# Paths', 1),
		(0, '/tag/', '# Paths', 1),
		(0, '/wishlist/', '# Paths', 1),
		(0, '/index.php', '# Paths', 1),
		(0, '/cron.php', '# Files', 1),
		(0, '/cron.sh', '# Files', 1),
		(0, '/error_log', '# Files', 1),
		(0, '/install.php', '# Files', 1),
		(0, '/LICENSE.html', '# Files', 1),
		(0, '/LICENSE.txt', '# Files', 1),
		(0, '/LICENSE_AFL.txt', '# Files', 1),
		(0, '/STATUS.txt', '# Files', 1),
		(0, '/get.php', '# Files (magento 1.5+ only)', 1),
		(0, '/.js$', '# Clean urls', 1),
		(0, '/?___from_store=', '# Clean urls', 1),
		(0, '*___from_store=', '# Clean urls', 1),
		(0, '/?mode=', '# Clean urls', 1),
		(0, '/?limit=', '# Clean urls', 1),
		(0, '/?dir=', '# Clean urls', 1),
		(0, '/.css$', '# Clean urls', 1),
		(0, '/.php$', '# Clean urls', 1),
		(0, '/?p=*&', '# Clean urls', 1),
		(0, '/?SID=', '# Clean urls', 1),
		(0, '/.php$', '# Clean urls', 1),
		(0, '/rss*', '# Clean urls', 1);
		";
		$write->query($sql);
    }    
    
    

}
