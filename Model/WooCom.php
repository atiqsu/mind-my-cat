<?php

namespace Mindmycat\Model;

class WooCom
{
    public static function getOrderStatus( $order_id )
    {
        $order = wc_get_order( $order_id );
        return empty( $order ) ? 'No order created' : $order->get_status();
    }

    public static function getPrevisitProductId()
    {
        $product_id = get_option('mindmycat_previsit_product_id');

        if(!$product_id) {
            $product_id = self::createPrevisitProduct();
            self::savePrevisitProductId($product_id);
        }

        return $product_id;
    }

    public static function savePrevisitProductId($product_id)
    {
        update_option('mindmycat_previsit_product_id', $product_id);
    }

    public static function createPrevisitProduct()
    {
        $product = wc_get_product_id_by_name('Pre consultation fee');


        if(!$product) {
            $product = new \WC_Product_Simple();
            $product->name = 'Pre consultation fee';
            $product->description = 'Pre consultation fee';
            $product->price = 80;
            $product->set_virtual(true);
            $product->set_stock_status('instock');
            $product->set_sold_individually(true);
            $product->set_manage_stock(false);
            $product->save();
        }

        return $product->get_id();
    }

    public static function getPrevisitProduct()
    {
        $product_id = self::getPrevisitProductId();
        return wc_get_product($product_id);
    }

    public static function unsetPrevisitProduct()
    {
        delete_option('mindmycat_previsit_product_id');
    }
}
