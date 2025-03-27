<?php

namespace Mindmycat\Handler;

use Mindmycat\Model\Contract;

class Ajax_Save_Owner_Scheduled_Info
{
    public function __construct()
    {
        add_action('wp_ajax_mmc_update_previsit_info', [$this, 'update_previsit_info'], 99);
        add_action('wp_ajax_nopriv_mmc_update_previsit_info', [$this, 'update_previsit_info'], 99);
    }

    public function update_previsit_info()
    {
        
        $sitter_id = intval($_POST['sitter_id']);
        $contract_id = $_POST['contract_id'];

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

        if(empty($contract_id)) {

            try {
                $contract = Contract::create([
                    'owner_id' => $owner_id,
                    'sitter_id' => $sitter_id,
                    'schedule' => json_encode($scheduled),
                    'service_info' => json_encode($user_requirement),
                ]);

                echo json_encode(['success' => 'Contract created successfully', 'contract_id' => $contract]);
                wp_die();

            } catch (\Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
                wp_die();
            }
        }

        $contract = Contract::find($contract_id);

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
                'id' => $contract_id
            ]);

            echo json_encode(['success' => 'Contract updated successfully']);
            wp_die();

        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
            wp_die();
        }
    }
}

