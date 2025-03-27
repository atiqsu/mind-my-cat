<?php

namespace Mindmycat;

class Config
{
    const CONTRACT_STATUS_READY_FOR_PREVISIT_DEPOSIT = 'ready_for_deposit';
    const CONTRACT_STATUS_PREVISIT_FEE_DEPOSITED = 'previsit_deposited';
    const CONTRACT_STATUS_PREVISIT_SCHEDULED = 'previsit_scheduled';
    const CONTRACT_STATUS_SESSION_STARTED = 'session_started';
    const CONTRACT_STATUS_SESSION_ENDED = 'session_ended';
    const CONTRACT_STATUS_SITTER_REJECTED = 'sitter_rejected';
    const CONTRACT_STATUS_SITTER_ACCEPTED = 'sitter_accepted';
    const CONTRACT_STATUS_PENDING = 'pending';
    const CONTRACT_STATUS_ACCEPTED = 'accepted';
    const CONTRACT_STATUS_REJECTED = 'rejected';
    const CONTRACT_STATUS_CANCELLED = 'cancelled';
    const CONTRACT_STATUS_COMPLETED = 'completed';
    const CONTRACT_STATUS_PRE_VISIT = 'pre_visit';
    const CONTRACT_STATUS_POST_VISIT = 'post_visit';


    public static function getContractStatuses($status) {
        
        switch ($status) {
            case self::CONTRACT_STATUS_READY_FOR_PREVISIT_DEPOSIT:
                return 'Ready for Previsit Deposit';
            case self::CONTRACT_STATUS_PREVISIT_FEE_DEPOSITED:
                return 'Previsit Fee Deposited';
            case self::CONTRACT_STATUS_PREVISIT_SCHEDULED:
                return 'Previsit Scheduled';
            case self::CONTRACT_STATUS_SITTER_REJECTED:
                return 'Sitter Rejected';
            case self::CONTRACT_STATUS_SITTER_ACCEPTED:
                return 'Sitter Accepted';
            case self::CONTRACT_STATUS_PENDING:
                return 'Pending';
            case self::CONTRACT_STATUS_ACCEPTED:
                return 'Accepted';
            case self::CONTRACT_STATUS_REJECTED:
                return 'Rejected';
            case self::CONTRACT_STATUS_CANCELLED:
                return 'Cancelled';
            case self::CONTRACT_STATUS_COMPLETED:
                return 'Completed';
            case self::CONTRACT_STATUS_PRE_VISIT:
                return 'Pre-Visit';
            case self::CONTRACT_STATUS_POST_VISIT:
                return 'Post-Visit';
            default:
                return $status;
        }
    }
    
}
