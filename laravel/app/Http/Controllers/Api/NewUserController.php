<?php

namespace App\Http\Controllers\Api;

use App\Models\rwDomain;
use App\Models\rwSettingsProcPacking;
use App\Models\rwSettingsSoa;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Models\SiteAccount;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\FormService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class NewUserController
{
    public function createNewAccount(Request $request)
    {

        $validatedData = $request->validate([
            'language' => 'required|string|max:6',
            'company' => 'required|string|max:100',
            'FName' => 'required|string|max:255',
            'LName' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'massage' => 'nullable|string',
            'fst' => 'required|numeric',
            'mName' => 'nullable|string',
        ]);

        $result = '';

        // Honeypot защита
        if (!empty($validatedData['mName'])) {
            $result = 'spam';
        }

        // Анти-бот защита (проверка времени отправки)
        if (time() - $validatedData['fst'] < 2) {
            $result = 'spam';
        }

        if ($result == '') {
            //Проверяем наличие записи в базе данных
            $existingAccount = SiteAccount::where('sa_email', $validatedData['email'])->first();

            if (!$existingAccount) {
                // Если записи нет, создаём новую
                SiteAccount::create([
                    'sa_lang' => $validatedData['language'],
                    'sa_domain' => $validatedData['company'],
                    'sa_first_name' => $validatedData['FName'],
                    'sa_last_name' => $validatedData['LName'],
                    'sa_email' => $validatedData['email'],
                    'sa_password' => Hash::make($validatedData['password']), // Хешируем пароль
                    'sa_timecash' => time(),
                    'sa_comment' => $validatedData['massage'],
                ]);
                $result = 'success';

                // Создаем домен

                $domainId = rwDomain::insertGetId([
                    'dm_name'   => $validatedData['company'],
                ]);

                // Создаем юзера

                $userId = User::insertGetId([
                    'domain_id'     => $domainId,
                    'lang'          => $validatedData['language'],
                    'wh_id'         => 0,
                    'name'          => $validatedData['FName'].' '.$validatedData['LName'],
                    'email'         => $validatedData['email'],
                    'password'   => Hash::make($validatedData['password']),
                    'permissions'   => '{"platform.systems.attachment":"1","platform.systems.roles":"1","platform.systems.users":"1","platform.index":"1"}',
                ]);

                // Проставляем права у юзера

                DB::table('role_users')->insert([
                    'user_id' => $userId,   // ID пользователя
                    'role_id' => 2    // ID роли
                ]);

                // Создаем ФФ склад

                $whFfId = rwWarehouse::insertGetId([
                    'wh_domain_id'      => $domainId,
                    'wh_user_id'        => $userId,
                    'wh_country_id'     => 1,
                    'wh_type'           => 1,
                    'wh_name'           => 'Main FF Warehouse',
                ]);

                // Создаем Клиентский склад

                $whClientId = rwWarehouse::insertGetId([
                    'wh_domain_id'      => $domainId,
                    'wh_user_id'        => $userId,
                    'wh_parent_id'      => $whFfId,
                    'wh_country_id'     => 1,
                    'wh_type'           => 2,
                    'wh_name'           => 'Main Work Warehouse',
                ]);

                // Обноваляем склад у юзера

                User::where('id', $userId)->update([
                    'wh_id'         => $whFfId,
                ]);

                // Создаем Магазин

                rwShop::insert([
                    'sh_domain_id'    => $domainId,
                    'sh_user_id'      => $userId,
                    'sh_name'         => 'Main shop',
                ]);

                // Создаем Очередь сборки

                rwSettingsSoa::insert([
                    'ssoa_status_id'     => 1,
                    'ssoa_priority'      => 10,
                    'ssoa_domain_id'     => $domainId,
                    'ssoa_wh_id'         => $whClientId,
                    'ssoa_user_id'       => $userId,
                    'ssoa_name'          => 'Base picking queue',
                    'ssoa_finish_place_type'   => 105,
                ]);

                // Создаем очередь упаковки

                rwSettingsProcPacking::insert([
                    'spp_status_id'          => 1,
                    'spp_priority'           => 10,
                    'spp_domain_id'          => $domainId,
                    'spp_wh_id'              => $whClientId,
                    'spp_user_id'            => $userId,
                    'spp_name'               => 'Base packing queue',
                    'spp_start_place_type'   => 105,
                    'spp_packing_type'       => 1,
                ]);

            } else {
                $result = 'exists';
            }
        }

        return response()->json([
            'status'    => 'OK'
        ], 201);
    }

}