<?php

namespace App\Console\Commands;

use App\Models\AbsentDevice;
use App\Models\AbsentLastLog;
use App\Models\AbsentLog;
use App\Models\AbsentReceivedLog;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Notif;
use App\Models\User;
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
                    $absent_device = AbsentDevice::where('subscribe_topic', $topic)->first();

                    if ($device) {
                        $device_log = DeviceLog::create(['device_id' => $device->id, 'value' => $message, 'type' => 'subscribe']);
                        $subscribe_expression = $device->subscribe_expression;
                        $subscribe_responses = Device::evalValue($device->id, $device_log->id, $subscribe_expression, $message);

                        // event to NewDataEvent
                        event(new \App\Events\NewDataEvent([
                            'type' => 'dynamic_device',
                            'data' => $subscribe_responses
                        ]));

                        Notif::create([
                            'notif_type' => 'dynamic_device',
                            'notif_status' => 'unread',
                            'device_id' => $device->id,
                            'absent_device_id' => null,
                            'message' => "Device {$device->device_id} has new data."
                        ]);
                    }

                    if ($absent_device) {
                        $user = User::where('user_code', $message)->first();
                        if ($user) {
                            echo "Absent Device\n";
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
                            
                            // event to NewDataEvent
                            event(new \App\Events\NewDataEvent([
                                'type' => 'absent_device',
                                'data' => $absent_received_log->load('absent_device')
                            ]));

                            Notif::create([
                                'notif_type' => 'dynamic_device',
                                'notif_status' => 'unread',
                                'absent_device_id' => $absent_device->id,
                                'device_id' => null,
                                'message' => "Device {$absent_device->absent_device_id} has new data."
                            ]);
                        }
                    }
                });
                echo "Received message success!\n";
            } catch (\Exception $e) {
                echo "Received message failed!\n" . $e->getMessage() . "\n";
            }
        }, 0);
        $mqtt->loop(true);
    }
}
