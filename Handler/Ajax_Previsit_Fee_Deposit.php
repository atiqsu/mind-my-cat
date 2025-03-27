<?php

namespace Mindmycat\Handler;

use Mindmycat\Helper;
use Mindmycat\Model\Contract;

class Ajax_Previsit_Fee_Deposit
{
    public function __construct()
    {
        add_action('wp_ajax_mmc_process_previsit_deposit', [$this, 'handle'], 99);
    }

    public function handle()
    {
        
        if ( empty($_POST['contract_id']) ) {
           
            wp_send_json_error( array( 'message' => 'Contract not found.' ) );
            wp_die();
        }


        if ( empty($_POST['amount']) ) {

            wp_send_json_error( array( 'message' => 'Deposit amount is not set!' ) );
            wp_die();
        }
        

        if (! WC()->session) {
            WC()->initialize_session();
        }

        $amount = $_POST['amount'] * 1;
        $contract_id = intval($_POST['contract_id']);
        $product_id = Helper::getPrevisitProductId();
        $contract = Contract::find($contract_id);

    
        $order = wc_create_order( array( 'customer_id' => $contract->owner_id ) );
    
        if ( is_wp_error( $order ) ) {
            wp_send_json_error( array( 'message' => $order->get_error_message() ) );
            wp_die();
        }
    

        $order->add_product( wc_get_product( $product_id ), 1 ); 
        $order->calculate_totals();
        $order->update_status('pending');

        $pay_url = $order->get_checkout_payment_url();

        $info = json_decode($contract->service_info, true);

        // $info['__previsit'] = [
        //     'amount' => $amount,
        //     'stage' => 'checkout initiated'
        // ]; 

        $info = json_encode($info);

        Contract::update(['order_id' => $order->get_id(), 'service_info' => $info ], ['id' => $contract->id]);

        echo json_encode([ 
            'pay_url' => $pay_url,
            'contract_id' => $contract->id,
            ]);

        wp_die();
    }
}

