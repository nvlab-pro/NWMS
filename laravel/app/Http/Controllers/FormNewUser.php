<?php

namespace App\Http\Controllers;

use App\Models\SiteAccount;
use Illuminate\Http\Request;
use App\Services\FormService;
use Illuminate\Support\Facades\Hash;

class FormNewUser extends Controller
{
    public function index()
    {
        // Читаем HTML-шаблон напрямую
        return response(file_get_contents(resource_path('views/site/form.html')), 200)
            ->header('Content-Type', 'text/html');
    }

    public function submit(Request $request, FormService $formService)
    {
        $validatedData = $request->validate([
            'language' => 'required|string|max:6',
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
                    'sa_first_name' => $validatedData['FName'],
                    'sa_last_name' => $validatedData['LName'],
                    'sa_email' => $validatedData['email'],
                    'sa_password' => Hash::make($validatedData['password']), // Хешируем пароль
                    'sa_timecash' => time(),
                    'sa_comment' => $validatedData['massage'],
                ]);
                $result = 'success';
            }else {
                $result = 'exists';
            }
        }

        return view('Site.FormNewUser', compact('result'));
    }
}