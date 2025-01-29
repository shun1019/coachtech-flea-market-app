<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->environment('local')) {
            try {
                $ngrokApiUrl = 'http://host.docker.internal:4040/api/tunnels';
                $response = Http::get($ngrokApiUrl);
                $data = $response->json();

                if (!empty($data['tunnels'])) {
                    foreach ($data['tunnels'] as $tunnel) {
                        if (isset($tunnel['public_url']) && str_contains($tunnel['public_url'], 'https')) {
                            $ngrokUrl = $tunnel['public_url'];

                            Config::set('app.url', $ngrokUrl);
                            Log::info('ngrokのURLが設定されました: ' . $ngrokUrl);
                            break;
                        }
                    }
                } else {
                    Log::warning('ngrok APIから有効なURLが取得できませんでした。');
                }
            } catch (\Exception $e) {
                Log::error('ngrok URLの取得に失敗しました: ' . $e->getMessage());
            }
        }
    }
}
