<?php

namespace Mindmycat\Handler;

use Mindmycat\Config;
use Mindmycat\Helper;
use Mindmycat\Model\Contract;
use Mindmycat\Model\Users;

class Pet_Booking_Tab_In_My_Acc {

    public function __construct() {

        add_filter('woocommerce_account_menu_items', [$this, 'add_pet_booking_tab']);
        add_action('woocommerce_account_pet-bookings_endpoint', [$this, 'render_pet_booking_tab']);
    }

    public function add_pet_booking_tab($items) {
        $items['pet-bookings'] = 'Pet Bookings';

        return $items;
    }

    public function render_pet_booking_tab() {
       
        $user_id = get_current_user_id();

        if ( Users::isAPetOwner($user_id) ) {

            $contracts = Contract::getUnfinishedContractsByOwnerId($user_id);

        } elseif ( Users::isAPetSitter($user_id) ) {

            $contracts = Contract::getUnfinishedContractsBySitterId($user_id);

        } elseif ( Users::isAnAdmin($user_id) ) {

            $contracts = Contract::getAllUnfinishedContracts();

            echo '<h1>All Unfinished Contracts</h1>';

        } else {

            echo '<p>You are not authorized to view this page</p>';
            return;
        }

        if ( empty($contracts) ) {

            echo '<p>You have 0 ongoing contracts</p>';
            return;
        }


        echo '<table class="tbl mmc-tbl">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__( 'ID', 'petcare-service' ) . '</th>';
        echo '<th>' . esc_html__( 'Status', 'petcare-service' ) . '</th>';
        echo '<th>' . esc_html__( 'Deposit ID', 'petcare-service' ) . '</th>';
        echo '<th>' . esc_html__( 'Payment ID', 'petcare-service' ) . '</th>';
        echo '<th>' . esc_html__( 'View', 'petcare-service' ) . '</th>';
        echo '</tr></thead>';

        foreach ( $contracts as $contract ) {

            // make a table row
            echo '<tr>';
            echo '<td> <a href="' . esc_url( Helper::get_view_contract_url($contract->id) ) . '">#' . $contract->id . '</a> </td>';
            echo '<td>' . Config::getContractStatuses($contract->status) . '</td>';
            echo '<td> #' . $contract->order_id . ' </td>';
            echo '<td> #' . $contract->order_id2 . ' </td>';
            echo '<td> <a href="' . esc_url( Helper::get_view_contract_url($contract->id) ) . '">View</a> </td>';
            echo '</tr>';
        }

        echo '</table>';
    }
}
