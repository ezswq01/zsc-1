<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use NotificationChannels\Telegram\TelegramUpdates;

class TelegramUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:telegram-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Telegram updates.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $updates = TelegramUpdates::create()
        ->latest()
        ->limit(2)
        ->options([
            'timeout' => 0,
        ])
        ->get();

        if($updates['ok']) {
            $chatId = $updates['result'][0]['message']['chat']['id'];
            $setting = Setting::first()->update(['chat_id_telegram' => $chatId]);
            if ($setting) {
                $this->info('Chat ID updated successfully.');
            } else {
                $this->error('Chat ID not updated.');
            }
        }

        return 0;
    }
}
