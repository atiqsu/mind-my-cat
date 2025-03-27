<?php

namespace Mindmycat\Handler;

use Mindmycat\Config;
use Mindmycat\Model\Contract;

class Ajax_Confirm_Previsit_Date
{
    public function __construct()
    {
        add_action('wp_ajax_mmc_confirm_previsit_date', [$this, 'handle']);
    }

    public function handle()
    {

        $contract_id = intval($_POST['contract_id']);
        $contract = Contract::find($contract_id);


        if ( empty($contract) ) {

            echo json_encode([
                'error' => 'Contract not found'
            ]);
            wp_die();
        }

        if ( $contract->status != Config::CONTRACT_STATUS_PREVISIT_SCHEDULED ) {

            echo json_encode([
                'error' => 'Contract is not in previsit scheduled status'
            ]);
            wp_die();
        }

        if ( $contract->sitter_id != get_current_user_id() ) {

            echo json_encode([
                'error' => 'Invalid request.'
            ]);
            wp_die();
        }


        Contract::update([
            'status' => Config::CONTRACT_STATUS_SITTER_ACCEPTED
        ],[
            'id' => $contract_id
        ]);


        echo json_encode([
            'success' => true,
            'message' => 'Previsit date confirmed successfully',
            'contract' => $contract_id,
        ]);
        wp_die();
    }
}

