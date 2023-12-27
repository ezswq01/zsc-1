<?php

namespace App\Http\Controllers;

use App\Models\AbsentDevice;
use App\Models\AbsentLastLog;
use App\Models\AbsentLog;
use App\Models\AbsentReceivedLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;

class AbsentDeviceController extends Controller
{
    public function publish(Request $request)
    {
        $absent_received_log = AbsentReceivedLog::find($request->absent_device_received_log_id);
        $absent_device = AbsentDevice::find($absent_received_log->absent_device_id);

        // create mqtt connection
        $mqtt = MQTT::connection();

        // publish message
        $mqtt->publish($absent_device->publish_topic, 'Open', 1);
        $mqtt->loop(true, true);

        DB::transaction(function () use ($request, $absent_device, $absent_received_log) {

            // save publish action to device log
            $absent_log = AbsentLog::create([
                'absent_device_id' => $absent_device->id,
                'value' => 'Open',
                'status' => 'Open',
            ]);

            // update absent_received_log status to 'Open'
            $absent_received_log->update([
                'marked_as_read' => true,
                'status' => 'Open',
                'notes' => $request->notes
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
