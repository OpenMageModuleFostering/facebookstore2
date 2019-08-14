<?php
class Shopgo_FacebookStore_IndexController extends Mage_Core_Controller_Front_Action
{
  public function indexAction()
  {

  	 if ($this->getRequest()->getParams()) {
      // Mage::getModel('core/cookie')->set("beetailer_ref", $this->getRequest()->getParam('fb_ref'));
      // Mage::getModel('core/cookie')->set("beetailer_ref_date", time());

      /* Fill shopping cart */
      $cart = Mage::getSingleton('checkout/cart');
      $product_Ids = explode(',',$this->getRequest()->getParam('Product_Ids'));
      $product_Qtys = explode(',',$this->getRequest()->getParam('Product_Qtys'));

      $sview = $this->getRequest()->getParam('store_view');
      foreach ($product_Ids as $key => $id) {
     
        $product = Mage::getModel('catalog/product')
          ->setStoreId(Mage::app()->getStore($sview)->getId())
          ->load($id);
          //var_dump($product);

        
        try{
          $cart->addProduct($product, array(
            'qty' => $product_Qtys[$key]
          ));
        }catch (Mage_Core_Exception $e) { }
      }


      $cart->save();
      Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

      $this->_redirect('checkout/cart?utm_source=FB&utm_medium=ShopgoFBStore&utm_campaign=FBStore');
            

    }else{
      $this->_redirect('/');
    }
  

}
  public function ownAction(){
    $pro_id = $this->getRequest()->getParam('id');
    $product =  Mage::getModel('catalog/product')->load($pro_id);

    if($this->getRequest()->getParam('fb_action_ids')){
     header("Location: ".$product->getProductUrl()."");
    die();
    }

    if($product->getName()=== NULL){
      die('No Product with this id !');
    }

    echo '
    <!DOCTYPE html>
      <head>
        <meta class="type" property="og:type"   content="shopgo_store:store_item" /> 
        <meta class="url" property="og:url"    content="'.Mage::helper('core/url')->getCurrentUrl().'" /> 
        <meta class="title" property="og:title"  content="'.$product->getName().'" /> 
        <meta class="image" property="og:image"  content="'.$product->getImageUrl().'" />
        <meta class="description" property="og:description"  content="'.$product->getData('description').'" /> 

      </head>
      <body></body>
    </html>
    ';

  }

  public function wantAction(){
    $pro_id = $this->getRequest()->getParam('id');
    $product =  Mage::getModel('catalog/product')->load($pro_id);

    if($this->getRequest()->getParam('fb_action_ids')){
     header("Location: ".$product->getProductUrl()."");
    die();
    }

    if($product->getName()=== NULL){
      die('No Product with this id !');
    }

    echo '
    <!DOCTYPE html>
      <head>
        <meta class="type" property="og:type"   content="shopgo_store:store_item" /> 
        <meta class="url" property="og:url"    content="'.Mage::helper('core/url')->getCurrentUrl().'" /> 
        <meta class="title" property="og:title"  content="'.$product->getName().'" /> 
        <meta class="image" property="og:image"  content="'.$product->getImageUrl().'" /> 
        <meta class="description" property="og:description"  content="'.$product->getData('description').'" /> 

      </head>
      <body>
      </body>
      </html>
      ';



}
}