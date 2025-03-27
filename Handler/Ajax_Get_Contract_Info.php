<?php

namespace Mindmycat\Handler;

use Mindmycat\Helper;
use Mindmycat\Model\Contract;

class Ajax_Get_Contract_Info
{
    public function __construct()
    {
        add_action('wp_ajax_mmc_get_contract_details', [$this, 'handle']);
        add_action('wp_ajax_nopriv_mc_get_contract_details', [$this, 'handle']);
    }

    public function handle()
    {

        $contract_id = intval($_POST['contract_id']);

        $contract = Contract::find($contract_id);

        echo json_encode([
            'contract' => $contract,
            '__fee' => Helper::get_previsit_fee()
        ]);
        wp_die();
    }
}

