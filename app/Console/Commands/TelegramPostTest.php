<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Notifications\TriggerTelegramNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class TelegramPostTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:telegram-post-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post test message to Telegram.';

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
        $chat_id = Setting::first()->chat_id_telegram;
        $status_type_name = 'Temperature';
        $value = 30;
        Notification::route('telegram', $chat_id)->notify(
            new TriggerTelegramNotification(
                "Trigger Alert!\nAlert from : $status_type_name with value $value"
            )
        );

        return 0;
    }
}
