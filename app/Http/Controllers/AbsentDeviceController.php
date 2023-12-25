<?php

namespace App\Http\Controllers;

use App\Models\AbsentDevice;
use App\Models\AbsentLastLog;
use App\Models\AbsentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;

class AbsentDeviceController extends Controller
{
    public function publish(Request $request)
    {
        // $subscribe_expression = $device->subscribe_expression;

        // create mqtt connection
        $mqtt = MQTT::connection();

        // publish message
        $mqtt->publish($request->publish_topic, 'Open', 1);
        $mqtt->loop(true, true);

        DB::transaction(function () use ($request) {

            $absent_device = AbsentDevice::where('publish_topic', $request->publish_topic)->first();

            // save publish action to device log
            $absent_log = AbsentLog::create([
                'absent_device_id' => $absent_device->id,
                'value' => 'Open',
                'status' => 'Open'
            ]);

            // dashboard action only.
            // delete current device_status to point that i handled.
            if (!$request->is_testing) {
                AbsentLastLog::updateOrCreate(
                    ['absent_device_id' => $absent_device->id],
                    ['value' => 'Open', 'absent_log_id' => $absent_log->id, 'status' => 'Open']
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Published successfully.',
        ]);
    }
}
