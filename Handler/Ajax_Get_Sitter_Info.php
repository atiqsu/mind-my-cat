<?php

namespace Mindmycat\Handler;

use Mindmycat\Model\Services;
use Mindmycat\Model\ACF;
use Mindmycat\Model\Calendar;

class Ajax_Get_Sitter_Info
{
    public function __construct()
    {
        add_action('wp_ajax_mmc_get_sitter_info', [$this, 'get_sitter_info'], 99);
        add_action('wp_ajax_nopriv_mmc_get_sitter_info', [$this, 'get_sitter_info'], 99);
    }

    public function get_sitter_info()
    {
        $sitter_id = intval($_POST['sitter']);

        // retirve user requirement for from reqId
        $user_requirement = get_option($_POST['user_req'], []);

        if(empty($user_requirement)) {
            echo json_encode(['error' => 'User requirement not found']);
            wp_die();
        }

        $start_date = $user_requirement['start_date'];
        $end_date = $user_requirement['end_date'];

        $booked_dates = Calendar::getAppointmentsBySitterIdByDateRange($sitter_id, $start_date, $end_date);

        $return_data = [
            'booked_dates' => $booked_dates,
            'user_requirement' => $user_requirement,
            'sitter_availability' => ACF::getSitterAvailabilityInfo($sitter_id),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'service_pricing' => Services::getPricingByServiceId($user_requirement['services'])
        ];


        echo json_encode($return_data);
        wp_die();
    }
}

