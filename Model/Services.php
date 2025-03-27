<?php

namespace Mindmycat\Model;

class Services
{

    public static function getAllServiceInfo()
    {
        $ret = [];
     
        $services = get_posts([
            'post_type' => 'service',
            'post_status' => 'publish',
            'numberposts' => -1
          ]);

          foreach ($services as $service) {

            $terms = wp_get_object_terms($service->ID, 'pet-type');

            $ret[$service->ID] = [
                'post_title' => $service->post_title,
                'post_name' => $service->post_name,
                'idd' => $service->ID,
                '_taxo_' => [
                    'pet_type' => $terms
                ]
            ];
          }

          return $ret;
    }

    public static function getAllServiceInfoWithPricing() 
    {

        $ret = [];

        $services = get_posts([
            'post_type' => 'service',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby'    => 'title',
            'order'    => 'ASC'
          ]);

          foreach ($services as $service) {

            $pricing = ACF::get_service_pricing($service->ID);
            $terms = wp_get_object_terms($service->ID, 'pet-type');

            $ret[$service->ID] = [
                'post_title' => $service->post_title,
                'post_name' => $service->post_name,
                'idd' => $service->ID,
                '_pet_type' => $terms,
                '_pricing' => $pricing
            ];
          }

          return $ret;
    }

    public static function getDurationFromPricingIndex($index, $pricingList, $default = 30) {

      $idx = $index - 1;

      return $pricingList[$idx]['minutes'] ?? $default;
    }

    public static function getPriceBreakdown($serviceId, $priceIdx, $timeSlots, $pets) {

      $pricing = ACF::get_service_pricing($serviceId);

      $priceList = $pricing['prices'][$priceIdx - 1];

   
      $basePetNumber = $priceList['number_of_pets'];
      $petNumbers = array_sum($pets);

      $numberOfExtraPet = $petNumbers - $basePetNumber;
      $numberOfExtraPet = $numberOfExtraPet > 0 ? $numberOfExtraPet : 0;

      //$extraPetRate = $priceList['price_per_additional_pet'];
      //$extraPetPrice = $extraPetRate * $numberOfExtraPet;

      $pricePerDay = [];

      $totalPrice = 0;

      foreach($timeSlots as $date => $slot) {

        $priceList = $pricing['prices'][$priceIdx - 1];

        $tmp  = [
          'date' => $date,
          'rate' => $priceList['price'],
          'extra_pet_rate' => $priceList['price_per_additional_pet'],

          'price' => 1 * $priceList['price'],
          'extra_price' => $basePetNumber * $priceList['price_per_additional_pet'],
          'total_price' => ($priceList['price'] + $priceList['price_per_additional_pet'] & $numberOfExtraPet),
        ];

        $totalPrice += $tmp['total_price'];

        $pricePerDay[$date] = $tmp;
      }


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


    public static function getPricingByServiceId($serviceId) 
    {
      return ACF::get_service_pricing($serviceId);
    }

}

