<?php

namespace App\Console\Commands;

use App\Events\NewDataEvent;
use App\Jobs\TriggerJob;
use App\Models\AbsentDevice;
use App\Models\AbsentLastLog;
use App\Models\AbsentLog;
use App\Models\AbsentReceivedLog;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Notif;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\TriggerTelegramNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use PhpMqtt\Client\Facades\MQTT;

class MqttSubscribingCommand extends Command
{
	protected $signature = 'mqtt-subscribing';
	protected $description = 'Command description';
	protected $mqtt;

	public function __construct() {
		parent::__construct();
		$this->mqtt = MQTT::connection();
	}

	public function handle() {
		$mqtt_main_topic = Setting::first()->mqtt_main_topic ?? "mcc";
		$this->mqtt->subscribe("{$mqtt_main_topic}/#", function (string $topic, string $message) {
			echo "Received message on topic: {$topic}\n";
			echo "Received message with payload: {$message}\n";
			if (strpos($topic, 'cambymcc') !== false) {
				echo "Received message on cam topic: {$topic}\n";
				return;
			}
			try {
				DB::transaction(function () use ($topic, $message) {
					Log::info("Received message on topic: {$topic}");


                    $lowercase_topic = strtolower($topic);
                    $lowercase_message = strtolower($message);
					if (str_contains($lowercase_topic, "getactivehour") || str_contains($lowercase_topic, "getinactivehour")) {
                        if (str_contains($lowercase_topic, "getactivehour")) {
                            echo "Received getactivehour\n";
                            $room = explode('/', $topic)[3];
                            $last_active_hour = explode(":", $message);
                            $hour = $last_active_hour[0];
                            $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
                            $minute = $last_active_hour[1];
                            $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);
                            Device::where('room', $room)->update([
                                'active_hour' => $hour . ":" . $minute
                            ]);
                        }
                        if (str_contains($lowercase_topic, "getinactivehour")) {
                            echo "Received getinactivehour\n";
                            $room = explode('/', $topic)[3];
                            $last_inactive_hour = explode(":", $message);
                            $hour = $last_inactive_hour[0];
                            $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
                            $minute = $last_inactive_hour[1];
                            $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);
                            Device::where('room', $room)->update([
                                'inactive_hour' => $hour . ":" . $minute
                            ]);
                        }
                        NewDataEvent::dispatch([
                            'type' => 'gethourbyroom',
                            'topic' => $topic,
                            'plain_payload' => $message,
                        ]);
                        return;
                    }


					// is active logic
					$lowercase_message = strtolower($message);
					if ($lowercase_message === "mainpingbyhost") {
						echo "Received mainpingbyhost\n";
						$room = explode('/', $topic)[3];
						Device::where('room', $room)->update([
							'is_online' => true,
							'last_ping_at' => now()
						]);
						return;
					}

					$device = Device::where('subscribe_topic', $topic)->first();
					$absent_device = AbsentDevice::where('subscribe_topic', $topic)->first();
					
					if ($device) {
						if (!isset($device->cam_topic)) {
								$cam_topic = implode('/', array(
									Setting::first()->mqtt_main_topic ?? "mcc",
									str_replace(" ","-", strtolower($device->branch)),
									str_replace(" ","-", strtolower($device->building)),
									str_replace(" ","-", strtolower($device->room)),
									str_replace(" ","-", strtolower($device->device_id)),
									"cambymcc"
								));
								$device->cam_topic = $cam_topic;
								$device->save();
						}
						$device_log = DeviceLog::create([
							'device_id' => $device->id, 
							'value' => $message, 
							'type' => 'subscribe'
						]);
						$subscribe_expression = $device->subscribe_expression;
						$subscribe_responses = Device::evalValue(
							$device->id,
							$device_log->id,
							$subscribe_expression,
							$message,
							$device->device_id,
                            $this->mqtt,
                            $device->cam_topic
						);
						try {
							Log::info("Event to NewDataEvent");
							NewDataEvent::dispatch([
								'type' => 'dynamic_device',
								'topic' => $topic,
								'device' => $device,
								'plain_payload' => $message,
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
									$setting = Setting::first();
									Notification::route('telegram', $setting->chat_id_telegram)->notify(
										new TriggerTelegramNotification(
											"Request Open!\nAlert from : $message"
										)
									);
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

					// stream listener logic
					if (str_contains($topic, "/stream")) {
						try {
							Log::info($topic);
							Log::info("Event to - Stream Listener");
							NewDataEvent::dispatch([
								'type' => 'stream_listener',
								'topic' => $topic,
								'plain_payload' => $message,
							]);
							Log::info("Event Done - Stream Listener");
						} catch (\Exception $e) {
							Log::error($e->getMessage());
						}
					}

				});
				echo "Received message success!\n";
			} catch (\Exception $e) {
				echo "Received message failed!\n" . $e->getMessage() . "\n";
				Log::error($e->getMessage());
			}
		}, 0);
		$this->mqtt->loop(true);
	}
}
