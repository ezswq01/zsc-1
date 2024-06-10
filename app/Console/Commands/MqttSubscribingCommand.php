<?php

namespace App\Console\Commands;

use App\Events\NewDataEvent;
use App\Jobs\TriggerJob;
use App\Mail\TriggerMail;
use App\Models\AbsentDevice;
use App\Models\AbsentLastLog;
use App\Models\AbsentLog;
use App\Models\AbsentReceivedLog;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Notif;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        $mqtt_main_topic = Setting::first()->mqtt_main_topic ?? "mcc";
        $mqtt = MQTT::connection();
        $mqtt->subscribe("{$mqtt_main_topic}/#", function (string $topic, string $message) {
            echo "Received message on topic: {$topic}\n";
            echo "Received message with payload: {$message}\n";
            try {
                DB::transaction(function () use ($topic, $message) {

                    Log::info("Received message on topic: {$topic}");

                    /* Get Device */
                    $device = Device::where('subscribe_topic', $topic)->first();
                    $absent_device = AbsentDevice::where('subscribe_topic', $topic)->first();

                    if ($device) {
                        $device_log = DeviceLog::create(['device_id' => $device->id, 'value' => $message, 'type' => 'subscribe']);
                        $subscribe_expression = $device->subscribe_expression;
                        $subscribe_responses = Device::evalValue(
                            $device->id,
                            $device_log->id,
                            $subscribe_expression,
                            $message,
                            $device->device_id
                        );

                        try {
                            Log::info("Event to NewDataEvent");
                            NewDataEvent::dispatch([
                                'type' => 'dynamic_device',
                                'data' => $subscribe_responses
                            ]);
                            Log::info("Event Done");
                        } catch (\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }

                    if ($absent_device) {
                        $user = User::where('user_code', $message)->first();
                        if ($user) {
                            $absent_log = AbsentLog::create(
                                ['absent_device_id' => $absent_device->id, 'value' => $message, 'status' => 'Request Open']
                            );
                            AbsentLastLog::updateOrCreate(
                                ['absent_device_id' => $absent_device->id],
                                ['value' => $message, 'absent_log_id' => $absent_log->id, 'status' => 'Request Open']
                            );
                            $absent_received_log = AbsentReceivedLog::create(
                                [
                                    'absent_device_id' => $absent_device->id,
                                    'absent_log_id' => $absent_log->id,
                                    'value' => $message,
                                    'status' => 'Request Open',
                                    'notes' => null,
                                    'marked_as_read' => false
                                ]
                            );

                            try {
                                Log::info("Event to NewDataEvent");
                                TriggerJob::dispatch($message, 'Request Open');
                                NewDataEvent::dispatch([
                                    'type' => 'absent_device',
                                    'data' => $absent_received_log->load('absent_device', 'user')
                                ]);
                                Log::info("Event Done");
                            } catch (\Exception $e) {
                                Log::error($e->getMessage());
                            }

                            Notif::create([
                                'notif_type' => 'dynamic_device',
                                'notif_status' => 'unread',
                                'absent_device_id' => $absent_device->id,
                                'device_id' => null,
                                'message' => "Device {$absent_device->absent_device_id} has new Access Request."
                            ]);
                        }
                    }
                });
                echo "Received message success!\n";
            } catch (\Exception $e) {
                echo "Received message failed!\n" . $e->getMessage() . "\n";
                Log::error($e->getMessage());
            }
        }, 0);
        $mqtt->loop(true);
    }
}
