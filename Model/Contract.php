<?php

namespace Mindmycat\Model;

use Mindmycat\Config;

class Contract extends WPModel
{

    protected string $table = 'contracts';

    public static function getContractByOrderId($order_id) {
        
        $model = new Contract;

        return $model->where('order_id', $order_id)->first();
    }

    public static function getUnfinishedContractsByOwnerId($owner_id) {

        $model = new Contract;

        return $model->where('owner_id', $owner_id)
        ->where('status', Config::CONTRACT_STATUS_COMPLETED, '!=')
        ->where('status', Config::CONTRACT_STATUS_CANCELLED, '!=')
        ->orderBy('id', 'desc')
        ->get();
    }

    public static function getUnfinishedContractsBySitterId($sitter_id) {

        $model = new Contract;

        return $model->where('sitter_id', $sitter_id)
        ->where('status', Config::CONTRACT_STATUS_COMPLETED, '!=')
        ->where('status', Config::CONTRACT_STATUS_CANCELLED, '!=')
        ->orderBy('id', 'desc')
        ->get();
    }

    public static function getAllUnfinishedContracts() {

        $model = new Contract;

        return $model->where('status', Config::CONTRACT_STATUS_COMPLETED, '!=')
        ->where('status', Config::CONTRACT_STATUS_CANCELLED, '!=')
        ->orderBy('id', 'desc')
        ->get();
    }

    public static function getContractById($id) {

        return Contract::find($id);
    }
}

