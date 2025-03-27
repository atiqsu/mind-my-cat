<?php

namespace Mindmycat\Handler;

use Mindmycat\Helper;

/**
 * todo - if have time add nonce check
 */
class Handle_Search_Submit
{
    public function __construct()
    {
        add_action('wp_ajax_mmc_search_submit', [$this, 'search_submit']);
        add_action('wp_ajax_nopriv_mmc_search_submit', [$this, 'search_submit']);

    }
    
    public function search_submit()
    {
        //check_ajax_referer('search_submit', 'nonce');

        if(isset($_POST['data']['req_idd'])) {

            // todo validate here ..... later

            $data = $_POST['data'];
            $userReqId = $data['req_idd'];

            $data['_service_info'] = [
                $data['services'] => $data['service_title']
            ];

          
            update_option( $userReqId, $data ); 

            
            $result = [
                'success' => true,
                'req_id' => $userReqId,
            ];
            
            echo json_encode($result);

        
            wp_die();

        }

        echo json_encode([
            'error' => 'invalid data'
        ]);
        
        wp_die();
        
    }
}
