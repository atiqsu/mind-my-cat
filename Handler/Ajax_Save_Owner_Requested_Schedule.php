<?php

namespace Mindmycat\Handler;

use Mindmycat\Model\Contract;
use Mindmycat\Config;
use Mindmycat\Helper;
use Mindmycat\Model\WooCom;

class Ajax_Save_Owner_Requested_Schedule
{
    public function __construct()
    {
        add_action('wp_ajax_mmc_save_owner_requested_schedule', [$this, 'save_owner_requested_schedule'], 99);
        add_action('wp_ajax_nopriv_mmc_save_owner_requested_schedule', [$this, 'save_owner_requested_schedule'], 99);
    }

    public function save_owner_requested_schedule()
    {
        
        $sitter_id = intval($_POST['sitter_id']);
        $owner_id = get_current_user_id();
        $scheduled = $_POST['scheduled'];
        $filter_id = $_POST['filter_id'];

        # todo - proper validation 
        if(empty($sitter_id) || empty($owner_id) || empty($scheduled) || empty($filter_id)) {
            echo json_encode(['error' => 'Invalid data']);
            wp_die();
        }

        $user_requirement = get_option($filter_id, []);

        if(empty($user_requirement)) {
            echo json_encode(['error' => 'User requirement not found']);
            wp_die();
        }

        # todo - proper discount business logic
        $user_requirement['__discount'] = [
            'pct' => 0,
            'limit' => -1
        ];

        $user_requirement['__filter_id'] = $filter_id;


        if(empty($_POST['contract_id'])) {

            try {
                $contract = Contract::create([
                    'owner_id' => $owner_id,
                    'sitter_id' => $sitter_id,
                    'schedule' => json_encode($scheduled),
                    'service_info' => json_encode($user_requirement),
                    'status' => Config::CONTRACT_STATUS_READY_FOR_PREVISIT_DEPOSIT,
                ]);

                $product_id = WooCom::getPreVisitProductId();

                $product = wc_get_product( $product_id );

                if(empty($product)) {
                    echo json_encode(['error' => 'Product not found']);
                    wp_die();
                }

                $order = WooCom::createPreVisitOrder($product, $owner_id);
                
                if(empty($order)) {
                    echo json_encode(['error' => 'Order could not be created']);
                    wp_die();
                }

                Contract::update([
                    'order_id' => $order->get_id(),
                ], [
                    'id' => $contract
                ]);

                echo json_encode([
                    'success' => 'Contract created successfully', 
                    'contract_id' => $contract,
                    'order_id' => $order->get_id(),
                    'order_url' => Helper::get_order_url($order->get_id()),
                    'payment_url' => $order->get_checkout_payment_url(),
                ]);

                wp_die();

            } catch (\Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
                wp_die();
            }
        }

        $contract = Contract::find(intval($_POST['contract_id']));

        if(empty($contract)) {
            echo json_encode(['error' => 'Contract not found']);
            wp_die();
        }

        if($contract->owner_id !== $owner_id) {

            echo json_encode(['error' => 'Contract not found']);
            wp_die();
        }

        if($contract->sitter_id !== $sitter_id) {

            // todo - proper calendar delete for changed sitter
        }

        try {

            Contract::update([
                'schedule' => json_encode($scheduled),
                'service_info' => json_encode($user_requirement),
                'sitter_id' => $sitter_id,
            ], [
                'id' => $contract->id
            ]);

            echo json_encode(['success' => 'Contract updated successfully']);
            wp_die();

        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
            wp_die();
        }
    }
}

