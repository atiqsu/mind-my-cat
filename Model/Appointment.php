<?php

namespace Mindmycat\Model;

class Appointment extends WPModel
{

    protected string $table = 'appointments';

    
    public static function getAppointmentsByDateBySitterId($sitter_id, $date)
    {
        return (new static)->where('sitter_id', $sitter_id)->where('date', $date)->get();
    }

    public static function getAppointmentsBySitterIdByDateRange($sitter_id, $start_date, $end_date)
    {
        return (new static)->where('sitter_id', $sitter_id)->where('date', '>=', $start_date)->where('date', '<=', $end_date)->get();
    }

    


}

