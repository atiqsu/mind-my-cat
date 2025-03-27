<?php

namespace Mindmycat\Handler;

use Mindmycat\Config;
use Mindmycat\Model\Contract;

class Ajax_Session_End
{
    public function __construct()
    {
        add_action('wp_ajax_mmc_session_end', [$this, 'handle']);
    }

    public function handle()
    {

        if ( empty($_POST['contract_id']) ) {
            echo json_encode([
                'error' => 'Invalid contract'
            ]);
            wp_die();
        }

        $contract = Contract::find(intval($_POST['contract_id']));


        if ( empty($contract) ) {

            echo json_encode([
                'error' => 'Contract not found'
            ]);
            wp_die();
        }

        if ( $contract->status == Config::CONTRACT_STATUS_SESSION_STARTED ) {

            $meta = $contract->metadata;
            $meta = empty($meta) ? [] : json_decode($meta, true);

            $meta['session_ended'] = true;
            $meta['session_ended_on'] = time();

            $meta = json_encode($meta);

            Contract::update([
                'metadata' => $meta,
                'status' => Config::CONTRACT_STATUS_SESSION_ENDED
            ],[
                'id' => $contract->id
            ]);
    
            echo json_encode([
                'success' => true,
                'message' => 'Session ended successfully',
                'contract' => $contract->id,
            ]);
            wp_die();
           
        }

        echo json_encode([
            'error' => 'Invalid contract status'
        ]);
        wp_die();
    }
}

