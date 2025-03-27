<?php

namespace Mindmycat;



class Helper
{

    static string $path;
    static string $plugin_url;

    public static function set_plugin_url($file)
    {
        self::$path = $file;
        self::$plugin_url = plugins_url('', $file);
    }

    public static function get_asset_url($rel_file): string  {

        return self::$plugin_url . '/assets/' . $rel_file;
    }

    public static function get_no_of_day($end, $start) {

        $datetime1 = new \DateTime($end);
        $datetime2 = new \DateTime($start);

        $interval = $datetime1->diff($datetime2);

        return $interval->days + 1;
    }

    public static function getDashboardUrl($userId = 0, $slug = 'pet_care_dashboard')
    {
        return admin_url( 'admin.php?page=' . $slug . '&profile_id=' . $userId );
    }


    public static function price_break_down_array(array $priceList, $durationInMin, $petNumbers)
    {

        $slot = ($durationInMin / $priceList['minutes'] );
        $total_pet = array_sum($petNumbers);
        $additional_pet = $total_pet - $priceList['number_of_pets'];

        $first2pet = $priceList['price'];
        $additionalPetPrice = 0;
        $discount_pct = 0;
        $discount = 0;


        if ($additional_pet > 0) {

            $additionalPetPrice = $priceList['price_per_additional_pet'] * $additional_pet;
        } else {

            $additional_pet = 0;
        }

        $sub_total = $first2pet + $additionalPetPrice;
        $totalPrice = $sub_total * $slot;
        $discount = $totalPrice * $discount_pct / 100;
        $total_after_discount = $totalPrice - $discount;

        $ret = [
            'first_max_pet' => $priceList['number_of_pets'],
            'price_min' => $priceList['minutes'],
            'extra_pet_rate' => $priceList['price_per_additional_pet'],
            'total_duration' => $durationInMin,
            'total_pet' => $total_pet,
            'extra_pet' => $additional_pet,
            'slots' => $slot,
            'first2pet' => $first2pet,
            'first_mx_price' => $first2pet,
            'extra_pet_price' => $additionalPetPrice,
            'sub_total' => $sub_total,
            'total_price' => $totalPrice,
            'discount' => $discount,
            'discount' => $discount,
            'discount_pct' => $discount_pct,
            'final_price' => $total_after_discount,
            'meta_' => $priceList,
        ];

        return $ret;
    }

    public static function get_my_account_url() 
    {
        return get_permalink( get_option('woocommerce_myaccount_page_id') );
    }

    public static function get_orders_url() 
    {
        return wc_get_endpoint_url('orders', '', self::get_my_account_url());
    }   

    public static function get_pet_bookings_url() 
    {
        return wc_get_endpoint_url('pet-bookings', '', self::get_my_account_url());
    }  

    public static function get_view_contract_url($idd) 
    {
        return wc_get_account_endpoint_url( 'view-contract' ) . $idd;
    }

    public static function get_order_url($orderId) {
        return wc_get_endpoint_url('orders', '', self::get_my_account_url()) . '/' . $orderId;
    }

    public static function get_order_details_url($orderId) 
    {
        return wc_get_endpoint_url('orders', '', self::get_my_account_url()) . '/' . $orderId;
    }


    public static function print_datalist($data, $key = 'name') 
    {

        foreach($data as $item) {
            echo '<option value="'.$item[$key].'"> ';
        }
    }

    public static function print_service_list($data, $key = 'post_title') 
    {

        foreach($data as $idd => $item) {
            echo '<option value="'. $idd .'"> '. $item[$key] .' </option>';
        }
    }


    public static function add_minutes_to_date($date, $minutes) 
    {

        return date('Y-m-d H:i:s', strtotime($date . ' + ' . $minutes . ' minutes'));
    }

    public static function add_days_to_date($date, $days, $timestamp = true) 
    {

        if($timestamp) {

            return strtotime($date . ' + ' . $days . ' days');
        }

        return date('Y-m-d', strtotime($date . ' + ' . $days . ' days'));
    }

    public static function get_current_page_url() {

        global $wp;

        return home_url($wp->request);
    }

    public static function remove_filter_param($url, $param) {

        $url = remove_query_arg($param, $url);

        return $url;
    }

    public static function get_previsit_fee() {

        return 100;
    }

    public function get_member_discount_pct($member_id) {

        return 0;
    }

}
