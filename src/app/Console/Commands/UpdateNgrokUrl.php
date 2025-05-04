<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateNgrokUrl extends Command
{
    protected $signature = 'ngrok:update';
    protected $description = 'Update .env file with the latest ngrok URL';

    public function handle()
    {
        $ngrokApiUrl = 'http://127.0.0.1:4040/api/tunnels';

        try {
            $response = file_get_contents($ngrokApiUrl);
            $data = json_decode($response, true);
            $ngrokUrl = $data['tunnels'][0]['public_url'] ?? null;

            if (!$ngrokUrl) {
                $this->error('ngrokのURL取得に失敗しました。ngrokが実行されているか確認してください。');
                return;
            }

            $envPath = base_path('.env');
            $envContent = File::get($envPath);
            $envContent = preg_replace('/^APP_URL=.*/m', "APP_URL={$ngrokUrl}", $envContent);
            $envContent = preg_replace('/^STRIPE_WEBHOOK_URL=.*/m', "STRIPE_WEBHOOK_URL={$ngrokUrl}/stripe/webhook", $envContent);
            File::put($envPath, $envContent);

            $this->info("APP_URL と STRIPE_WEBHOOK_URL を {$ngrokUrl} に更新しました。");
        } catch (\Exception $e) {
            $this->error("ngrokのURL取得に失敗しました: " . $e->getMessage());
        }
    }
}