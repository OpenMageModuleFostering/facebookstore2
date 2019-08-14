<?php
/**
 * @category    Shopgo
 * @package     Shopgo_FacebookStore
 * @author      ali@shopgo.me
 */
class Shopgo_FacebookStore_Helper_Data extends Mage_Core_Helper_Abstract
{
    // Retrieve current installed version
    public function getExtensionVersion()
    {
      return (string) Mage::getConfig()->getNode()->modules->Shopgo_FacebookStore->version;
    }
}