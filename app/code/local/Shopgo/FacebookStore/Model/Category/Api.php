<?php

/**
 * Rewrites original Mage_Catalog_Model_Product_Api to limit result set.
 *
 * @category    Shopgo
 * @package     Shopgo_FacebookStore
 * @author      ali@shopgo.me
 * @see Mage_Catalog_Model_Product_Api::items
 */
class Shopgo_FacebookStore_Model_Category_Api extends Mage_Catalog_Model_Category_Api
{

	function Shopgo_FacebookStore_Model_Category_Api() {
	   parent::__construct();
	}

	/**
     * Retrieve list of assigned products to category
     *
     * @param int $categoryId
     * @param string|int $store
     * @return array
     */
    public function assignedProducts($categoryId, $store = null)
    {
        // set current store view
        Mage::app()->setCurrentStore($store);

        $category = $this->_initCategory($categoryId);

        $storeId = $this->_getStoreId($store);
        $collection = $category->setStoreId($storeId)->getProductCollection();
        ($storeId == 0)? $collection->addOrder('position', 'asc') : $collection->setOrder('position', 'asc');
        $collection->addAttributeToSelect('*');

        $collection->addAttributeToFilter('status', 1);
        $collection->addAttributeToFilter('visibility', 4);

        $currencyCode   = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currencySymbol = Mage::app()->getLocale()->currency($currencyCode)->getSymbol();

        $result = array();
        foreach ($collection as $product) {
        	$img = Mage::getModel('catalog/product')->load($product->getId())->getImageUrl();

            $price_no_symbol = Mage::getModel('directory/currency')->format(
                $product->getFinalPrice(), 
                array('display'=>Zend_Currency::NO_SYMBOL), 
                false
            );

            $final_price = Mage::helper('core')->currency($product->getFinalPrice(),true,false);

            $result[] = array(
                'product_id' => $product->getId(),
                'type'       => $product->getTypeId(),
                'set'        => $product->getAttributeSetId(),
                'sku'        => $product->getSku(),
                'position'   => $product->getCatIndexPosition(),
                'img'        => Mage::helper('catalog/image')->init($product, 'image')
                                ->constrainOnly(true)
                                ->keepAspectRatio(true)
                                // ->keepFrame(false)
                                ->resize(450)
                                ->__toString(),
                'product_id' => $product->getId(),
                'sku'        => $product->getSku(),
                'name'       => $product->getName(),
                'url' => $product->getProductUrl(),
                'short_description' => $product->getShortDescription(),
                'description' => $product->getDescription(),
                'price'   => $final_price,
                'set'        => $product->getAttributeSetId(),
                'type'       => $product->getTypeId(),
                'category_ids'       => $product->getCategoryIds(),
                'cart_url' => Mage::getUrl('checkout/cart/add', array('product' => $product->getId()))                
            );
        }
        return $result;
    }
}