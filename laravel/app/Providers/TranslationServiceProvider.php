<?php

namespace App\Providers;

use Illuminate\Translation\Translator;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class TranslationServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Проверяем, зарегистрирован ли стандартный TranslationServiceProvider
        if (!$this->app->bound('translator')) {
            return;
        }

        // Регистрируем Loader вручную
        $this->app->singleton(Loader::class, function ($app) {
            return new FileLoader(new Filesystem(), $app['path.lang']);
        });

        // Расширяем Translator
        $this->app->extend('translator', function ($translator, $app) {
            $loader = $app->make(Loader::class);

            return new class($loader, $app['config']['app.locale']) extends Translator {
                public function get($key, array $replace = [], $locale = null, $fallback = true)
                {
                    $translation = parent::get($key, $replace, $locale, $fallback);

                    if ($translation === $key) {
                        Log::info("🚨 Отсутствует перевод: $key");
                    }

                    return $translation;
                }
            };
        });
    }
}
