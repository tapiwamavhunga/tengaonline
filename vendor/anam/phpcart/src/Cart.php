<?php
namespace Anam\Phpcart;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Session\Session;
use shopist\Models\Option;
use shopist\Models\Post;

class Cart implements CartInterface
{
    const CARTSUFFIX = '_cart';

    /**
     * The Cart session,
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * Manage cart items
     *
     * @var \Anam\Phpcart\Collection
     */
    protected $collection;

    /**
     * Cart name
     *
     * @var string
     */
    protected $name = "phpcart";
    
    /**
     * Manage Settings
     *
     * @var array
     */
    public $settings;
    public $shipping;
    /**
     * Construct the class.
     *
     * @return void
     */
    public function __construct($name = null)
    {
        $this->session = new Session();
        $this->collection = new Collection();
        $get_settings_option = Option :: where('option_name', '_settings_data')->first();
        
        if(!empty($get_settings_option->option_value)){
          $this->settings = unserialize($get_settings_option->option_value);
        }
        
        $get_shipping_data = Option :: where('option_name', '_shipping_method_data')->first();
        
        if(!empty($get_shipping_data->option_value)){
          $this->shipping = unserialize($get_shipping_data->option_value);
        }
        
        if ($name) {
            $this->setCart($name);
        }
    }

    public function setCart($name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Cart name can not be empty.');
        }

        $this->name = $name . self::CARTSUFFIX;
    }

    public function getCart()
    {
        return $this->name;
    }

    /**
     * Set the current cart name
     *
     * @param  string  $instance  Cart instance name
     * @return StudentVIP\Cart
     */
    public function named($name)
    {
        $this->setCart($name);

        return $this;
    }

    /**
     * Add an item to the cart.
     *
     * @param  Array  $product
     * @return \Anam\Phpcart\Collection
     */
    public function add(Array $product)
    {
        $product_id = 0;
        $this->collection->validateItem($product);
        
        if(isset($product['variation_id']) && $product['variation_id'] > 0){
          $post = Post::where(['id' => $product['variation_id']])->get()->first();
          
          if(!empty($post)){
            $product_id = $post->parent_id;
          }
        }
        else{
          $product_id = $product['id'];
        }
        
        // If item already added, increment the quantity
        if ( $this->has($product['id']) && ( get_product_type($product_id) == 'simple_product' || get_product_type($product_id) == 'configurable_product' ) ) {
            $item = $this->get($product['id']);
            
            return $this->updateQty($item->id, $item->quantity + $product['quantity']);
        }

        $this->collection->setItems($this->session->get($this->getCart(), []));

        $items = $this->collection->insert($product);

        $this->session->set($this->getCart(), $items);

        return $this->collection->make($items);
    }

    /**
     * Update an item.
     *
     * @param  Array  $product
     * @return \Anam\Phpcart\Collection
     */
    public function update(Array $product)
    {
        $this->collection->setItems($this->session->get($this->getCart(), []));

        if (! isset($product['id'])) {
            throw new Exception('id is required');
        }

//        if (! $this->has($product['id'])) {
//            throw new Exception('There is no item in shopping cart with id: ' . $product['id']);
//        }
        
        if(get_product_type($product['id']) === 'customizable_product')
        {
          $item = array_merge((array) $this->get($product['acces_token']), $product);
        }
        else
        {
          $item = array_merge((array) $this->get($product['id']), $product);
        }
        

        $items = $this->collection->insert($item);

        $this->session->set($this->getCart(), $items);

        return $this->collection->make($items);
    }

    /**
     * Update quantity of an Item.
     *
     * @param mixed $id
     * @param int $quantity
     *
     * @return \Anam\Phpcart\Collection
     */
    public function updateQty($id, $quantity)
    {
        $item = (array) $this->get($id);

        $item['quantity'] = $quantity;

        return $this->update($item);
    }


    /**
     * Update price of an Item.
     *
     * @param mixed $id
     * @param float $price
     *
     * @return \Anam\Phpcart\Collection
     */
    public function updatePrice($id, $price)
    {
        $item = (array) $this->get($id);

        $item['price'] = $price;

        return $this->update($item);
    }

    /**
     * Remove an item from the cart.
     *
     * @param  int $id
     * @return $this
     */
    public function remove($id)
    {
        $items = $this->session->get($this->getCart(), []);

        unset($items[$id]);

        $this->session->set($this->getCart(), $items);

        return $this->collection->make($items);
    }

    /**
     * Helper wrapper for cart items.
     *
     * @return \Anam\Phpcart\Collection
     */
    public function items()
    {
        return $this->getItems();
    }

    /**
     * Get all the items.
     *
     * @return \Anam\Phpcart\Collection
     */
    public function getItems()
    {
        return $this->collection->make($this->session->get($this->getCart()));
    }

    /**
     * Get a single item.
     * @param  $id
     *
     * @return Array
     */
    public function get($id)
    {
        $this->collection->setItems($this->session->get($this->getCart(), []));

        return $this->collection->findItem($id);
    }

    /**
     * Check an item exist or not.
     * @param  $id
     *
     * @return boolean
     */
    public function has($id)
    {
        $this->collection->setItems($this->session->get($this->getCart(), []));

        return $this->collection->findItem($id)? true : false;
    }

    /**
     * Get the number of Unique items in the cart
     *
     * @return int
     */

    public function count()
    {
        $items = $this->getItems();
        return $items->count();
    }

    /**
     * Get the total amount
     *
     * @return float
     */

    public function getTotal()
    {
        $items = $this->getItems();

        return $items->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Get the total quantities of items in the cart
     *
     * @return int
     */

    public function totalQuantity()
    {
        $items = $this->getItems();

        return $items->sum(function($item) {
            return $item->quantity;
        });
    }

    /**
     * Clone a cart to another
     * 
     * @param  mix $cart
     * 
     * @return void
     */

    public function copy($cart)
    {
        if (is_object($cart)) {
            if (! $cart instanceof \Anam\Phpcart\Cart) {
                throw new InvalidArgumentException("Argument must be an instance of " . get_class($this));
            }

            $items = $this->session->get($cart->getCart(), []);
        } else {
            if (! $this->session->has($cart . self::CARTSUFFIX)) {
                throw new Exception('Cart does not exist: ' . $cart);
            }

            $items = $this->session->get($cart . self::CARTSUFFIX, []);
        }

        $this->session->set($this->getCart(), $items);

    }

    /**
     * Alias of clear (Deprecated)
     *
     * @return void
     */

    public function flash()
    {
        $this->clear();
    }
    
    /**
     * Cart row price calculation  
     *
     * @return float
     */

    public function getRowPrice($qty, $price)
    {
        return $qty * $price;
    }

    /**
     * Empty cart
     *
     * @return void
     */

    public function clear()
    {
        $this->session->remove($this->getCart());
        $this->shippingRemove();
    }
    
    /**
     * Cart Tax Calculation
     *
     * @return float
     */

    public function getTax()
    {
      $taxRate = 0;
      if($this->settings['general_settings']['taxes_options']['enable_status'] && $this->settings['general_settings']['taxes_options']['tax_amount'])
      {
        if($this->settings['general_settings']['taxes_options']['apply_tax_for'] == 'order_total')
        {
          $taxRate = $this->getTotal() * ($this->settings['general_settings']['taxes_options']['tax_amount'] / 100.0);
        }
        elseif($this->settings['general_settings']['taxes_options']['apply_tax_for'] == 'per_product')
        {
          $getItem = $this->getItems();
          
          foreach($getItem as $val)
          {
            if($val->tax)
            {
              $taxRate += ($val->price * ($this->settings['general_settings']['taxes_options']['tax_amount'] / 100.0)) * $val->quantity;
            }
          }
        }
      }
      
      return $taxRate;
    }
    
    public function getSubTotalAndTax()
    {
      return $this->getTotal() + $this->getTax();
    }
    
    public function getLocalDeliveryShippingPercentageTotal()
    {
      return $this->getSubTotalAndTax()  * ($this->shipping['local_delivery']['delivery_fee'] / 100.0);
    }
    
    public function getLocalDeliveryShippingPerProductTotal()
    {
      return $this->totalQuantity() * $this->shipping['local_delivery']['delivery_fee'];
    }
    
    public function getCartTotal()
    {
      if($this->is_coupon_applyed()){
        return ($this->getSubTotalAndTax() + $this->getShippingCost()) - $this->couponPrice();
      }
      else{
        return ($this->getSubTotalAndTax() + $this->getShippingCost()) + $this->couponPrice();
      }
    }
    
    public function setShippingMethod($shipping_data = array())
    {
      if(!$this->session->has('eBazar_shipping_method'))
      {
        $this->session->set('eBazar_shipping_method', $shipping_data);
      }
      elseif($this->session->has('eBazar_shipping_method'))
      {
        $this->session->remove('eBazar_shipping_method');
        $this->session->set('eBazar_shipping_method', $shipping_data);
      }
      
      if($this->session->has('eBazar_shipping_method'))
      {
        return true;
      }
    }
    
    public function getShippingMethod()
    {
      if(!$this->shipping['shipping_option']['enable_shipping'] || ($this->shipping['shipping_option']['enable_shipping'] && !$this->shipping['flat_rate']['enable_option'] && !$this->shipping['free_shipping']['enable_option'] && !$this->shipping['local_delivery']['enable_option']))
      {
        if($this->session->has('eBazar_shipping_method'))
        {
          $this->shippingRemove();
          return false;
        }
      }
      elseif(($this->shipping['shipping_option']['enable_shipping']) && ($this->shipping['flat_rate']['enable_option'] || $this->shipping['free_shipping']['enable_option'] || $this->shipping['local_delivery']['enable_option']))
      {
        if(!$this->session->has('eBazar_shipping_method'))
        {
          if($this->shipping['flat_rate']['enable_option'] && $this->shipping['flat_rate']['method_cost'])
          {
            
            $this->setShippingMethod( array('shipping_method' => 'flat_rate', 'shipping_cost' => $this->shipping['flat_rate']['method_cost']) );
            
            if($this->session->has('eBazar_shipping_method'))
            {
              return $this->session->get('eBazar_shipping_method');
            }
          }
          elseif($this->shipping['free_shipping']['enable_option'] && ( Cart::getSubTotalAndTax() >= $this->shipping['free_shipping']['order_amount'] ))
          {
            $this->setShippingMethod( array('shipping_method' => 'free_shipping', 'shipping_cost' => 0) );
            
            if($this->session->has('eBazar_shipping_method'))
            {
              return $this->session->get('eBazar_shipping_method');
            }
          }
          elseif($this->shipping['local_delivery']['enable_option'] && $this->shipping['local_delivery']['fee_type'] === 'fixed_amount' && $this->shipping['local_delivery']['delivery_fee'])
          {
            $this->setShippingMethod( array('shipping_method' => 'local_delivery', 'shipping_cost' => $this->shipping['local_delivery']['delivery_fee']) );
            
            if($this->session->has('eBazar_shipping_method'))
            {
              return $this->session->get('eBazar_shipping_method');
            }
          }
          elseif($this->shipping['local_delivery']['enable_option'] && $this->shipping['local_delivery']['fee_type'] === 'cart_total' && $this->shipping['local_delivery']['delivery_fee'])
          {
            $this->setShippingMethod( array('shipping_method' => 'local_delivery', 'shipping_cost' => $this->getLocalDeliveryShippingPercentageTotal()) );
            
            if($this->session->has('eBazar_shipping_method'))
            {
              return $this->session->get('eBazar_shipping_method');
            }
          }
          elseif($this->shipping['local_delivery']['enable_option'] && $this->shipping['local_delivery']['fee_type'] === 'per_product' && $this->shipping['local_delivery']['delivery_fee'])
          {
            $this->setShippingMethod( array('shipping_method' => 'local_delivery', 'shipping_cost' => $this->getLocalDeliveryShippingPerProductTotal()) );
            
            if($this->session->has('eBazar_shipping_method'))
            {
              return $this->session->get('eBazar_shipping_method');
            }
          }
        }
        elseif ($this->session->has('eBazar_shipping_method')) 
        {
          $data = $this->session->get('eBazar_shipping_method');
          if($this->shipping['local_delivery']['enable_option'] && $this->shipping['local_delivery']['fee_type'] === 'per_product' && $this->shipping['local_delivery']['delivery_fee'] && isset($data['shipping_method']) && $data['shipping_method'] == 'local_delivery')
          {
            $this->setShippingMethod( array('shipping_method' => 'local_delivery', 'shipping_cost' => $this->getLocalDeliveryShippingPerProductTotal()) );
            
            if($this->session->has('eBazar_shipping_method'))
            {
              return $this->session->get('eBazar_shipping_method');
            }
          }
          else{
            return $this->session->get('eBazar_shipping_method');
          }
        }
      }
    }
    
    public function getShippingCost()
    {
      $shipping_cost = 0;
     
      if($this->getShippingMethod())
      {
        $getShippingData = $this->getShippingMethod();
        $shipping_cost = $getShippingData['shipping_cost'];
      }
      
      return $shipping_cost;
    }
    
    public function shippingRemove()
    {
      if($this->session->has('eBazar_shipping_method'))
      {
        $this->session->remove('eBazar_shipping_method');
      }
    }
    
    public function calculationCoupon($amount, $type, $coupon_code)
    {
      $is_coupon_set = false;
      $get_val = 0;
      
      if($type == 'discount_from_product'){
        $get_val = $this->totalQuantity() * $amount;
      }
      elseif($type == 'percentage_discount_from_product'){
        if(!empty($this->items())){
          foreach($this->items() as $item){
             $get_val +=  $item->quantity * ($item->price * ($amount/100));
          }
        }
      }
      elseif($type == 'discount_from_total_cart'){
        $get_val = $amount;
      }
      elseif($type == 'percentage_discount_from_total_cart'){
        $get_val = $this->getTotal() * ($amount/100);
      }
      
      if($get_val && $get_val > 0 && $this->getTotal() > $get_val){
        if($this->session->has('applyed_coupon_price')){
          $this->session->remove('applyed_coupon_price');
          $this->session->set('applyed_coupon_price', $get_val);
        }
        else{
          $this->session->set('applyed_coupon_price', $get_val);
        }
        
        if($this->session->has('applyed_coupon_code')){
          $this->session->remove('applyed_coupon_code');
          $this->session->set('applyed_coupon_code', $coupon_code);
        }
        else{
          $this->session->set('applyed_coupon_code', $coupon_code);
        }
      }
      else{
        $this->remove_coupon();
      }
      
      if($this->session->has('applyed_coupon_price') && $this->session->has('applyed_coupon_code')){
        $is_coupon_set = true;
      }
      
      return $is_coupon_set;
    }
    
    public function  couponPrice(){
      $price = 0;
      
      if($this->session->has('applyed_coupon_price')){
        $price = $this->session->get('applyed_coupon_price');
      }
      
      return $price;
    }
    
    public function  couponCode(){
      $code = '';
      
      if($this->session->has('applyed_coupon_code')){
        $code = $this->session->get('applyed_coupon_code');
      }
      
      return $code;
    }
    
    public function is_coupon_applyed(){
      if($this->session->has('applyed_coupon_price') && $this->session->has('applyed_coupon_code')){
        return true;
      }
    }
    
    public function remove_coupon(){
      if($this->session->has('applyed_coupon_price') && $this->session->has('applyed_coupon_code')){
        $this->session->remove('applyed_coupon_price');
        $this->session->remove('applyed_coupon_code');
        return true;
      }
    }
}