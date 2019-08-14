<?php

/**
 * Rewrites original Mage_Catalog_Model_Product_Api to limit result set.
 *
 * @category    Shopgo
 * @package     Shopgo_FacebookStore
 * @author      ali@shopgo.me
 * @see Mage_Catalog_Model_Product_Api::items
 */
class Shopgo_FacebookStore_Model_Product_Api extends Mage_Catalog_Model_Product_Api
{

  	function Shopgo_FacebookStore_Model_Product_Api() {
  		parent::__construct();
  	}

    public function items($filters = null, $store = null)
    {
        /**
         *
         */
        $visibility = array(
		   Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
		   Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
	    );

        // set current store view
        Mage::app()->setCurrentStore($store);

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addStoreFilter($this->_getStoreId($store))
            ->addAttributeToSelect('*');
        
        $collection->addAttributeToFilter('status', 1);
        $collection->addAttributeToFilter('visibility', $visibility);
        
        // $collection->addAttributeToFilter('type_id', array('eq' => 'simple'));

        $currencyCode   = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currencySymbol = Mage::app()->getLocale()->currency($currencyCode)->getSymbol();

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }

        $result = array();
        foreach ($collection as $product) {

            $price_no_symbol = Mage::getModel('directory/currency')->format(
                $product->getFinalPrice(), 
                array('display'=>Zend_Currency::NO_SYMBOL), 
                false
            );

            $final_price = Mage::helper('core')->currency($product->getFinalPrice(),true,false);

            $result[] = array(
                'product_id' => $product->getId(),
                'sku'        => $product->getSku(),
                'name'       => $product->getName(),
                'url' => $product->getProductUrl(),
                'short_description' => $product->getShortDescription(),
                'description' => $product->getDescription(),
                'price'   => $final_price,
                'currency_code' => $currencyCode,
                'currency_symbol'   => $currencySymbol,
                'img'        => Mage::helper('catalog/image')->init($product, 'image')
                                ->constrainOnly(true)
                                ->keepAspectRatio(true)
                                // ->keepFrame(false)
                                ->resize(450)
                                ->__toString(),
                'set'        => $product->getAttributeSetId(),
                'type'       => $product->getTypeId(),
                'category_ids' => $product->getCategoryIds(),
                'website_ids'  => $product->getWebsiteIds(),
                'cart_url' => Mage::getUrl('checkout/cart/add', array('product' => $product->getId()))
            );
        }
        return $result;
    }

    public function info($productId, $store = null, $attributes = null, $identifierType = null)
    {
        // set current store view
        Mage::app()->setCurrentStore($store);

        $product = $this->_getProduct($productId, $store, $identifierType);

        $final_price = Mage::helper('core')->currency($product->getFinalPrice(),true,false);

        $result = array(
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'name'       => $product->getName(),
            'url' => $product->getProductUrl(),
            'short_description' => $product->getShortDescription(),
            'description' => $product->getDescription(),
            'price'   => $final_price,
            'currency_code' => $currencyCode,
            'currency_symbol'   => $currencySymbol,
            'img'        => Mage::helper('catalog/image')->init($product, 'image')
                            ->constrainOnly(true)
                            ->keepAspectRatio(true)
                            // ->keepFrame(false)
                            ->resize(450)
                            ->__toString(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'category_ids' => $product->getCategoryIds(),
            'website_ids'  => $product->getWebsiteIds(),
            'cart_url' => Mage::getUrl('checkout/cart/add', array('product' => $product->getId()))
        );

	    $currencyInfo = $this->getCurrentCurrencyInfo();

	    $result['internal'] = array(
            'currency' => array(
                'code'   => $currencyInfo['code'],
                'symbol' => $currencyInfo['symbol'],
            )
        );

        return $result;
    }


    /**
     * Returns array of current currency code and symbol.
     *
     * @return array
     */
    public function getCurrentCurrencyInfo()
    {
        $currencyCode   = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currencySymbol = Mage::app()->getLocale()->currency($currencyCode)->getSymbol();

        return array('code' => $currencyCode, 'symbol' => $currencySymbol);
    }
}
