<?php

namespace App\WhActionLog;

use App\Models\RwActionLog;

class WhActionLog
{

    public static function saveActionLog($whId, $actionId, $date, $userId, $entityId, $qty)
    {

        RwActionLog::create([
            'al_wh_id'      => $whId,
            'al_action_id'  => $actionId,
            'al_date'       => $date,
            'al_user_id'    => $userId,
            'al_entity_id'  => $entityId,
            'al_qty'        => $qty,
        ]);

    }

}