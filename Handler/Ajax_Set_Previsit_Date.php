<?php

namespace Mindmycat\Handler;

use Mindmycat\Config;
use Mindmycat\Model\Contract;

class Ajax_Set_Previsit_Date
{
    public function __construct()
    {
        add_action('wp_ajax_mmc_set_previsit_date', [$this, 'handle']);
        add_action('wp_ajax_nopriv_mmc_set_previsit_date', [$this, 'handle']);
    }

    public function handle()
    {

        $contract_id = intval($_POST['contract_id']);
        $previsit_date = sanitize_text_field($_POST['previsit_date']);

        if ( empty($previsit_date) ) {
            echo json_encode([
                'error' => 'Please select a date'
            ]);
            wp_die();
        }

        $contract = Contract::find($contract_id);


        if ( empty($contract) ) {

            echo json_encode([
                'error' => 'Contract not found'
            ]);
            wp_die();
        }

        if ( $contract->status == Config::CONTRACT_STATUS_SITTER_REJECTED || $contract->status == Config::CONTRACT_STATUS_PREVISIT_FEE_DEPOSITED ) {

            Contract::update([
                'previsit_date' => $previsit_date,
                'status' => Config::CONTRACT_STATUS_PREVISIT_SCHEDULED
            ],[
                'id' => $contract_id
            ]);
    
    
            echo json_encode([
                'success' => true,
                'message' => 'Previsit date scheduled successfully',
                'contract' => $contract_id,
            ]);
            wp_die();
           
        }

        echo json_encode([
            'error' => 'Invalid contract status'
        ]);
        wp_die();

    }
}

