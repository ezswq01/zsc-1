<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsentDevice\StoreAbsentDeviceRequest;
use App\Http\Requests\AbsentDevice\UpdateAbsentDeviceRequest;
use App\Models\AbsentDevice;
use App\Models\AbsentLastLog;
use App\Models\AbsentLog;
use App\Models\AbsentReceivedLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;

class AbsentDeviceController extends Controller
{
    public function index()
    {
        $data = AbsentDevice::orderBy('created_at','desc')->get();
        return view('admin.absent_devices.index', compact('data'));
    }

    public function create()
    {
        return view('admin.absent_devices.create');
    }

    public function show($id)
    {
        $data = AbsentDevice::find($id);
        return view('admin.absent_devices.show', compact('data'));
    }

    public function edit($id)
    {
        $data = AbsentDevice::find($id);
        return view('admin.absent_devices.edit', compact('data'));
    }

    public function store(StoreAbsentDeviceRequest $request)
    {
        $validated = $request->all();

        DB::transaction(function () use ($validated) {
            $topics = explode('/', $validated['publish_topic']);

            $branch = $topics[1];
            $building = $topics[2];
            $room = $topics[3];

            AbsentDevice::create([
                'absent_device_id' => $validated['absent_device_id'],
                'publish_topic' => strtolower($validated['publish_topic']),
                'subscribe_topic' => strtolower($validated['subscribe_topic']),
                'branch' => strtolower($branch),
                'building' => strtolower($building),
                'room' => $room
            ]);
        });

        return redirect()->route('admin.absent_devices.index')->with('success', 'Device created successfully.');
    }

    public function update(UpdateAbsentDeviceRequest $request, $id)
    {
        $validated = $request->all();

        DB::transaction(function () use ($validated, $id) {
            $topics = explode('/', $validated['publish_topic']);

            $branch = $topics[1];
            $building = $topics[2];
            $room = $topics[3];

            AbsentDevice::find($id)->update([
                'absent_device_id' => $validated['absent_device_id'],
                'publish_topic' => strtolower($validated['publish_topic']),
                'subscribe_topic' => strtolower($validated['subscribe_topic']),
                'branch' => strtolower($branch),
                'building' => strtolower($building),
                'room' => $room
            ]);
        });

        return redirect()->route('admin.absent_devices.index')->with('success', 'Device updated successfully.');
    }

    public function destroy($id)
    {
        $data = AbsentDevice::find($id);
        $data->delete();
        return redirect()->route('absent_devices.index')->with('success', 'Data deleted successfully');
    }

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
