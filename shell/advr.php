<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     439
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


require_once 'abstract.php';

class Mirasvit_Shell_Advr extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('display_errors', 1);
        ini_set('memory_limit', '16000M');
        error_reporting(E_ALL);
        set_time_limit(36000);

        if ($this->getArg('notify')) {
            $this->_notify();
        } elseif ($this->getArg('geo-copy-unknown')) {
            Mage::getSingleton('advr/postcode')->copyUnknown(true);
        } elseif ($this->getArg('geo-update')) {
            Mage::getSingleton('advr/postcode')->batchUpdate(true);
        } elseif ($this->getArg('geo-merge')) {
            Mage::getSingleton('advr/postcode')->batchMerge(true);
        } elseif ($this->getArg('geo-export')) {
            Mage::getSingleton('advr/postcode')->exportAll(true);
        } elseif ($this->getArg('test')) {
            $this->_test();
        } elseif ($this->getArg('import-postcodes')) {
            $this->_importPostcodes();
        } elseif ($this->getArg('import-postcodes2')) {
            $this->_importPostcodes2();
        }  elseif ($this->getArg('posttest')) {
            $this->_posttest();
        } elseif ($this->getArg('posttest2')) {
            $this->_posttest2();
        } elseif ($this->getArg('posttest3')) {
            $this->_posttest3();
        } elseif ($this->getArg('geo-import')) {
            $this->_postCN();
            die(__LINE__);
        } else { 
            echo $this->usageHelp();
        }
    }

    protected function _notify()
    {
        $emails = Mage::getModel('advd/notification')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($emails as $email) {
            $email = $email->load($email->getId());
            $email->send();

            echo $email->getRecipientEmail().' OK'.PHP_EOL;
        }
    }

    protected function _getCopyFromOrders()
    {
        Mage::getSingleton('advr/postcode')->copyFromOrders(true);
    }

    protected function _update()
    {
        
    }

    protected function _test()
    {
        $emails = Mage::getModel('advd/notification')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($emails as $email) {
            $email = $email->load($email->getId());

            $gmt = Mage::getSingleton('core/date')->gmtTimestamp();
            $local = Mage::getSingleton('core/date')->timestamp();

            echo 'GMT:   '.date('M, d h:i a', $gmt).PHP_EOL;
            echo 'Local: '.date('M, d h:i a', $local).PHP_EOL;
            echo $email->canSend($gmt);

            echo PHP_EOL.PHP_EOL;
        }
    }

    protected function _importPostcodes()
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        // $connection->query('TRUNCATE TABLE '.$resource->getTableName('advr/postcode'));

        $files = array('allCountries.txt');
        // $files = array('gb.txt');

        $query = 'INSERT INTO '.$resource->getTableName('advr/postcode').' SET
            country_id = :country_id,
            postcode = :postcode,
            place = :place,
            state = :state,
            province = :province,
            community = :community,
            lat = :lat,
            lng = :lng';

        foreach ($files as $file) {
            $file = fopen($file, 'r');
            $members = array();
            while (!feof($file)) {
                $line = fgets($file);
                $row = explode(chr(9), $line);

                // echo $line.PHP_EOL;
                if (empty($row[9]) || empty($row[10])) {
                    continue;
                }

                // [0] => GB
                // [1] => AB10 1AA
                // [2] => George St/Harbour Ward
                // [3] => Scotland
                // [4] => SCT
                // [5] => 
                // [6] => 
                // [7] => Aberdeen City
                // [8] => S12000033
                // [9] => 57.1482280891232
                // [10] => -2.09664786079318
                // [11] => 6
                // country code      : iso country code, 2 characters
                // postal code       : varchar(20)
                // place name        : varchar(180)
                // admin name1       : 1. order subdivision (state) varchar(100)
                // admin code1       : 1. order subdivision (state) varchar(20)
                // admin name2       : 2. order subdivision (county/province) varchar(100)
                // admin code2       : 2. order subdivision (county/province) varchar(20)
                // admin name3       : 3. order subdivision (community) varchar(100)
                // admin code3       : 3. order subdivision (community) varchar(20)
                // latitude          : estimated latitude (wgs84)
                // longitude         : estimated longitude (wgs84)
                // accuracy          : accuracy of lat/lng from 1=estimated to 6=centroid
                // die();
                // if($row[1] != 'EC1V 0DY') {
                //     continue;
                // }
                $binds = array(
                    'postcode'     => preg_replace("/[^A-Z0-9]/", "", strtoupper($row[1])),
                    'country_id' => $row[0],
                    'lat'          => $row[9],
                    'lng'          => $row[10],
                    'place'        => $row[2],
                    'state'        => $row[3],
                    'province'     => $row[5],
                    'community'    => $row[7],

                );
                // print_r($binds);
                
                try {
                    echo $connection->query($query, $binds);
                    echo $binds['postcode'].PHP_EOL;
                } catch (Exception $e) {
                    // echo $e;
                }
            }

            fclose($file);
        }
    }

    protected function _importPostcodes2()
    {
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $files = array('allCountries.txt');

        foreach ($files as $file) {
            if (!file_exists($file)) {
                die('File not exists');
            }
            $file = fopen($file, 'r');
            $members = array();

            while (!feof($file)) {
                $line = fgets($file);

                echo $idx++.PHP_EOL;

                if ($idx < 1025075) {
                    continue;
                }

                $row = explode(chr(9), $line);

                $countryId = $row[0];
                $postcode = $row[1];

                $model = Mage::getSingleton('advr/postcode')->loadByCode($countryId, $postcode);
                if ($model) {
                    $original = $model->getData('original');
                    $original = json_decode($original, true);
                    $original['dictionary'] = $row;
                    $model->setOriginal(json_encode($original))
                        ->save();

                    echo $model.PHP_EOL;
                }

            }

            fclose($file);
        }
    }

    protected function _postCN()
    {
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $files = array('cn.csv');

        foreach ($files as $file) {
            if (!file_exists($file)) {
                die('File not exists');
            }
            $file = fopen($file, 'r');
            $members = array();

            while (!feof($file)) {
                $line = fgets($file);
            
                $row = explode(';', $line);

                $countryId = 'CN';
                $postcode = $row[0];


                $model = Mage::getSingleton('advr/postcode')->loadByCode($countryId, $postcode);
                if (!$model) {
                    try {
                    $model = Mage::getModel('advr/postcode');
                    $model->setCountryId($countryId)
                        ->setPostcode($postcode)
                        ->save();

                    echo $model.PHP_EOL;
                    } catch (Exception $e) {}
                }

            }

            fclose($file);
        }
    }


    // protected function _importPostcodes2()
    // {
    //     $resource = Mage::getSingleton('core/resource');
    //     $connection = $resource->getConnection('core_write');

    //     $files = array('uk-post-codes-2009');

    //     $query = 'INSERT INTO '.$resource->getTableName('advr/postcode').' SET
    //         country_id = :country_id,
    //         postcode = :postcode,
    //         place = :place,
    //         state = :state,
    //         province = :province,
    //         community = :community,
    //         lat = :lat,
    //         lng = :lng';

    //     foreach ($files as $file) {
    //         $file = fopen($file, 'r');
    //         $members = array();
    //         $idx = 0;
    //         $num = 0;
    //         while (!feof($file)) {
    //             $line = fgets($file);
    //             if ($idx == 0) {
    //                 $idx++;
    //                 continue;
    //             }
    //             $row = explode(',', $line);

    //             $binds = array(
    //                 'postcode'     => preg_replace("/[^A-Z0-9]/", "", strtoupper($row[0])),
    //                 'country_id'   => 'GB',
    //                 'lat'          => str_replace('"', '', $row[13]),
    //                 'lng'          => str_replace('"', '', $row[14]),
    //                 'place'        => '',
    //                 'state'        => '',
    //                 'province'     => '',
    //                 'community'    => '',

    //             );

    //             try {
    //                 $connection->query($query, $binds);
    //                 echo $num++.PHP_EOL;
    //             } catch (Exception $e) {}
    //         }

    //         fclose($file);
    //     }
    // }

    public function _validate() {}

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f advr.php -- [options]

  --notify               Send notifications to all subscribed users
  --geo-import           Import post codes from file to database
  --geo-export           Export post codes from database to file
  --geo-copy-unknown     Import post codes (in shipping address) to post codes table
  --geo-update           Fetch information for post codes without information
  --geo-merge            Update post codes without information

USAGE;
    }
}

$shell = new Mirasvit_Shell_Advr();
$shell->run();