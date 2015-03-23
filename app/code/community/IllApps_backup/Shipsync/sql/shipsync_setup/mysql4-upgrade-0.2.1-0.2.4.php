<?php

/**
 * ShipSync Community
 *
 * @category   IllApps
 * @package    IllApps_Shipsync
 * @author     David Kirby (d@kernelhack.com)
 * @copyright  Copyright (c) 2012 EcoMATICS, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->run("ALTER TABLE `{$this->getTable('shipping_shipment_package')}` CHANGE `label_image` `label_image` MEDIUMBLOB NOT NULL");

$installer->endSetup();