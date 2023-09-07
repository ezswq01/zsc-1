<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\DeviceLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;

class MqttSubscribingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt-subscribing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $mqtt = MQTT::connection();
        $mqtt->subscribe("mcc/#", function (string $topic, string $message) {
            echo "Received message on topic: {$topic}\n";
            echo "Received message with payload: {$message}\n";
            try {
                DB::transaction(function () use ($topic, $message) {
                    /* Get Device */
                    $device = Device::where('subscribe_topic', $topic)->first();
                    /* Create Log */
                    $device_log = DeviceLog::create(['device_id' => $device->id, 'value' => $message, 'type' => 'subscribe']);
                    /* Get Expressions */
                    $subscribe_expression = $device->subscribe_expression;
                    /* Device Alert Logic */
                    Device::evalValue($device->id, $device_log->id, $subscribe_expression, $message);
                });
                echo "Received message success!\n";
            } catch (\Exception $e) {
                echo "Received message failed!\n" . $e->getMessage() . "\n";
            }
        }, 0);
        $mqtt->loop(true);
    }
}
