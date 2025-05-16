<?php

namespace App\Orchid\Services;

use App\Models\rwUserAction;
use Carbon\Carbon;

class WarehouseUserActionService
{
    public static function logAction(array $data): rwUserAction
    {
        // 1. Автозаполнение времени начала, если не указано
        if (!isset($data['ua_time_start'])) {
            $data['ua_time_start'] = now();
        }

        // 2. Валидация обязательных полей
        $requiredFields = ['ua_user_id','ua_lat_id','ua_domain_id',
            'ua_wh_id','ua_entity_type','ua_doc_id','ua_entity_id',
            'ua_quantity','ua_time_start'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        // 3. Закрытие предыдущей незавершенной записи
        $lastAction = rwUserAction::where('ua_user_id', $data['ua_user_id'])
            ->whereNull('ua_time_end')
            ->orderByDesc('ua_time_start')
            ->first();

        if ($lastAction && Carbon::parse($lastAction->ua_time_start)->toDateString() === now()->toDateString()) {
            $lastAction->ua_time_end = $data['ua_time_start'];
            $lastAction->save();
        }

        // 4. Создание новой записи действия пользователя
        $action = rwUserAction::create($data);

        // 5. Вернуть созданную модель
        return $action;
    }
}