<?php
/**
 * @category    Shopgo
 * @package     Shopgo_FacebookStore
 * @author      ali@shopgo.me
 * @see Mage_Core_Model_Store_Api::info
 */
class Shopgo_FacebookStore_Model_Store_Api extends Mage_Core_Model_Store_Api
{

    // function Shopgo_FacebookStore_Model_Store_Api() {
    //   parent::__construct();
    // }

    /**
     * Retrieve stores list
     *
     * @return array
     */
    public function items()
    {
        // Retrieve stores
        $stores = Mage::app()->getStores();

        // Make result array
        $result = array();
        foreach ($stores as $store) {
            $store = Mage::getModel('core/store')->load($store->getId());
            $result[] = array(
                'store_id'    => $store->getId(),
                'code'        => $store->getCode(),
                'website_id'  => $store->getWebsiteId(),
                'group_id'    => $store->getGroupId(),
                'name'        => $store->getName(),
                'sort_order'  => $store->getSortOrder(),
                'is_active'   => $store->getIsActive(),
                'locale'      => Mage::getStoreConfig('general/locale/code', $store->getId())
            );
        }

        return $result;
    }


    /**
     * Retrieve store data
     *
     * @param string|int $storeId
     * @return array
     */
    public function info($storeId)
    {
        // Retrieve store info
        try {
            $store = Mage::app()->getStore($storeId);
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_fault('store_not_exists');
        }

        if (!$store->getId()) {
            $this->_fault('store_not_exists');
        }

        // Basic store data
        $result = array();
        $result['store_id'] = $store->getId();
        $result['code'] = $store->getCode();
        $result['website_id'] = $store->getWebsiteId();
        $result['group_id'] = $store->getGroupId();
        $result['name'] = $store->getName();
        $result['sort_order'] = $store->getSortOrder();
        $result['is_active'] = $store->getIsActive();
        $result['locale'] = Mage::getStoreConfig('general/locale/code', $store->getId());

        return $result;
    }
}
