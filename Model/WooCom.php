<?php

namespace Mindmycat\Model;

use Mindmycat\Helper;

class WooCom
{

    public static function getOrder( $order_id )
    {
        return wc_get_order( $order_id );
    }

    public static function getOrderStatus( $order_id )
    {
        $order = wc_get_order( $order_id );
        return empty( $order ) ? 'No order created' : $order->get_status();
    }

    public static function getPreVisitProductId($def = 0)
    {
        return get_option('mindmycat_previsit_product_id', $def);
    }

    public static function savePreVisitProductId($product_id)
    {
        return update_option('mindmycat_previsit_product_id', $product_id);
    }

    public static function createPreVisitProduct()
    {

        $productId = wc_get_product_id_by_sku('pre-consultation-fee');

        if(!$productId) {
            $product = new \WC_Product_Simple();
            $product->set_name('Pre consultation fee');
            $product->set_description('Pre consultation fee');
            $product->set_regular_price(80);
            $product->set_sku('pre-consultation-fee');
            $product->set_virtual(true);
            $product->set_stock_status('instock');
            $product->set_sold_individually(true);
            $product->set_manage_stock(false);
            $product->save();

            return $product->get_id();
        }

        return $productId;
    }

    public static function unsetPrevisitProduct()
    {
        delete_option('mindmycat_previsit_product_id');
    }

    public static function getProductByTitle($product_title, $default = null) {

        $products = wc_get_products( array(
            'name' => $product_title
        ) );

        if ( count( $products ) > 0 ) {
            return $products[0];
        }
        
        return $default;
    }


    public static function createPreVisitOrder($product, $owner_id) {

        $consultation_fee = Helper::get_previsit_fee();
        
        $product->set_price( $consultation_fee );

        $order = wc_create_order();
        $order->add_product($product, 1);
        $order->set_customer_id($owner_id);
        $order->calculate_totals();
        $order->set_status('pending');
        $order->save();

        return $order;
    }
}
