<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Device extends Model
{
    // use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'device_id',
        'device_type_id',
        'publish_topic',
        'subscribe_topic',
        'branch',
        'building',
        'room',
        'created_at',
        'updated_at'
    ];

    public function device_type()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id', 'id');
    }

    public function device_log()
    {
        return $this->hasMany(DeviceLog::class, 'device_id');
    }

    public function device_status()
    {
        return $this->hasMany(DeviceStatus::class, 'device_id');
    }

    public function publish_action()
    {
        return $this->hasMany(PublishAction::class, 'device_id');
    }

    public function subscribe_expression()
    {
        return $this->hasMany(SubscribeExpression::class, 'device_id');
    }

    public static function subscribeToTopic($mqtt, $topic)
    {
        $mqtt->subscribe($topic, function (string $topic, string $message) {
            echo "Received message on topic: {$topic}\n";
            echo "Received message with payload: {$message}\n";
            try {
                DB::transaction(function () use ($topic, $message) {

                    // Create device log
                    $device = Device::where('subscribe_topic', $topic)->first();
                    $device_log = DeviceLog::create([
                        'device_id' => $device->id,
                        'value' => $message
                    ]);

                    // Subscribe Logic
                    $subscribe_expression = $device->subscribe_expression;
                    Device::evalValue($device->id, $device_log->id, $subscribe_expression, $message);
                });
                echo "Received message success!\n";
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }, 0);
    }

    public static function evalValue($device_id, $device_log_id, $subscribe_expression, $value)
    {
        $status_responses = [];
        foreach ($subscribe_expression as $val) {
            $expression = str_replace("{{value}}", "'$value'", $val->expression);
            if (eval("return $expression;")) {
                $status_response = DeviceStatus::create(
                    [
                        'device_id' => $device_id,
                        'device_log_id' => $device_log_id,
                        'status_type_id' => $val->status_type_id,
                        'marked_as_read' => false,
                        'notes' => null
                    ]
                );
                $status_response = $status_response->load('status_type.status_type_widget', 'device.publish_action');
                $status_responses[] = $status_response;
            }
        }
        return $status_responses;
    }
}
