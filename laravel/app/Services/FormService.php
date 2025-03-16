<?php

namespace App\Services;

class FormService
{
    public function processForm($data)
    {
        // Можно сохранить в БД, отправить email и т. д.
        // Например, сохраняем в лог
        \Log::info('Form submitted', $data);

        return ['message' => 'Form submitted successfully!'];
    }
}