<?php

namespace App\Http\Controllers;

use App\Http\Requests\Device\StoreDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\DeviceStatus;
use App\Models\DeviceType;
use App\Models\PublishAction;
use App\Models\Setting;
use App\Models\StatusType;
use App\Models\SubscribeExpression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:devices-create')->only(['create', 'store', 'importTemplate', 'import']);
        $this->middleware('can:devices-read')->only(['index', 'show']);
        $this->middleware('can:devices-update')->only(['edit', 'update']);
        $this->middleware('can:devices-delete')->only('destroy');
    }

    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(Device::query()->with('device_type'))
                ->addIndexColumn()
                ->editColumn('device_id', function ($model) {
                    return '<a href="' . route('admin.devices.show', $model->id) . '">' . $model->device_id . '</a>';
                })
                ->addColumn('options', 'admin.devices.datatables.options')
                ->setRowAttr([
                    'data-model-id' => function ($model) {
                        return $model->id;
                    }
                ])
                ->filter(function ($query) {
                    if (!empty(request()->get('locations'))) {
                        $query->where(function ($w) {
                            $locations = request()->get('locations');
                            foreach ($locations as $location) {
                                $w->orWhere('branch', $location);
                            }
                        });
                    }

                    if (!empty(request()->get('buildings'))) {
                        $query->where(function ($w) {
                            $buildings = request()->get('buildings');
                            foreach ($buildings as $building) {
                                $w->orWhere('building', $building);
                            }
                        });
                    }

                    if (!empty(request()->get('rooms'))) {
                        $query->where(function ($w) {
                            $rooms = request()->get('rooms');
                            foreach ($rooms as $room) {
                                $w->orWhere('room', $room);
                            }
                        });
                    }

                    if (!empty(request()->get('device_types'))) {
                        $query->where(function ($w) {
                            $device_types = request()->get('device_types');
                            foreach ($device_types as $device_type) {
                                $w->orWhere('device_type_id', $device_type);
                            }
                        });
                    }

                    if (!empty(request()->get('search'))) {
                        $query->where(function ($w) {
                            $search = request()->get('search');
                            $w->orWhere('device_id', 'ILIKE', "%$search%")
                                ->orWhere('subscribe_topic', 'ILIKE', "%$search%")
                                ->orWhere('publish_topic', 'ILIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['device_id', 'options'])
                ->toJson();
        }

        return view('admin.devices.index');
    }

    public function create()
    {
        $device_types = DeviceType::all(['id', 'name']);
        $status_types = StatusType::all(['id', 'name']);

        return view('admin.devices.create', compact('device_types', 'status_types'));
    }

    public function store(StoreDeviceRequest $request)
    {
        $validated = $request->all();

        DB::transaction(function () use ($validated, $request) {

            // OLD CODES
            // $topics = explode('/', $validated['publish_topic']);
            // $branch = $topics[1];
            // $building = $topics[2];
            // $room = $topics[3];
            // $device = Device::create([
            //     'device_id' => $validated['device_id'],
            //     'device_type_id' => $validated['device_type_id'],
            //     'publish_topic' => strtolower($validated['publish_topic']),
            //     'subscribe_topic' => strtolower($validated['subscribe_topic']),
            //     'branch' => strtolower($branch),
            //     'building' => strtolower($building),
            //     'room' => $room
            // ]);

            $subscribe_topic = implode('/', array(
                    Setting::first()->mqtt_main_topic ?? "mcc",
                    str_replace(" ","-",strtolower($validated['branch'])),
                    str_replace(" ","-",strtolower($validated['building'])),
                    str_replace(" ","-",strtolower($validated['room'])),
                    str_replace(" ","-",strtolower($validated['sensor_id'])),
                    "sub"
            ));

            $publish_topic = implode('/', array(
                    Setting::first()->mqtt_main_topic ?? "mcc",
                    str_replace(" ","-",strtolower($validated['branch'])),
                    str_replace(" ","-",strtolower($validated['building'])),
                    str_replace(" ","-",strtolower($validated['room'])),
                    str_replace(" ","-",strtolower($validated['sensor_id'])),
                    "pub"
            ));

            $cam_topic = implode('/', array(
                    Setting::first()->mqtt_main_topic ?? "mcc",
                    str_replace(" ","-",strtolower($validated['branch'])),
                    str_replace(" ","-",strtolower($validated['building'])),
                    str_replace(" ","-",strtolower($validated['room'])),
                    str_replace(" ","-",strtolower($validated['sensor_id'])),
                    "cam"
            ));

            $device = Device::create([
                    'device_id' => $validated['device_id'],
                    'device_type_id' => $validated['device_type_id'],
                    'publish_topic' => strtolower($publish_topic),
                    'subscribe_topic' => strtolower($subscribe_topic),
                    'cam_topic' => strtolower($cam_topic),
                    'branch' => strtolower($validated['branch']),
                    'building' => strtolower($validated['building']),
                    'room' => strtolower($validated['room'])
            ]);

            if ($request->subscribe_expressions) {
                    foreach ($request->subscribe_expressions['expression'] as $key => $subscribe_expression) {
                        SubscribeExpression::create([
                            'device_id' => $device->id,
                            'expression' => $request->subscribe_expressions['expression'][$key],
                            'status_type_id' => $request->subscribe_expressions['status_type'][$key],
                            'normal_state' => $request->subscribe_expressions['normal_state'][$key] == 'on' ? true : false
                        ]);
                    }
            }

            if ($request->publish_actions) {
                    foreach ($request->publish_actions['label'] as $key => $publish_action) {
                        PublishAction::create([
                            'device_id' => $device->id,
                            'label' => $request->publish_actions['label'][$key],
                            'value' => $request->publish_actions['value'][$key],
                        ]);
                    }
            }
        });

        return redirect()->route('admin.devices.index')->with('success', 'Device created successfully.');
    }

    public function show($id)
    {
        if (request()->ajax()) {
            $device_status = DeviceStatus::with('device', 'device_log')
                ->select('device_status.*')
                ->where('device_status.device_id', $id);

            if (request()->date) {
                $from_date = explode(' - ', request()->date)[0];
                $to_date   = explode(' - ', request()->date)[1];
                $device_status->whereBetween('device_status.created_at', [$from_date, $to_date]);
            }

            return DataTables::eloquent($device_status)
                ->editColumn('created_at', function ($model) {
                    return [
                        'display'   => date('Y-m-d H:i:s', strtotime($model->created_at)),
                        'timestamp' => strtotime($model->created_at),
                    ];
                })
                ->addColumn('command', function ($model) {
                    return $model->device_log->value;
                })
                ->addColumn('status', function ($model) {
                    return $model->marked_as_read
                        ? '<i class="ph-check-circle text-success"></i>'
                        : '<i class="ph-question text-danger"></i>';
                })
                ->addColumn('location', function ($model) {
                    return explode('/', $model->device->publish_topic)[1];
                })
                ->addColumn('options', 'admin.devices.datatables.device-logs-options')
                ->setRowAttr([
                    'data-model-id' => function ($model) {
                        return $model->id;
                    }
                ])
                ->rawColumns(['status', 'options'])
                ->toJson();
        }

        $data         = Device::with('subscribe_expression', 'publish_action', 'device_type')->find($id);
        $data->sensor_id = explode('/', $data->publish_topic)[4];
        $device_types = DeviceType::all(['id', 'name']);
        $status_types = StatusType::all(['id', 'name']);
        $device_logs  = DeviceLog::with('device')->where('device_id', $id)->get();

        return view('admin.devices.show', compact('data', 'device_types', 'status_types', 'device_logs'));
    }

    public function edit($id)
    {
        $data         = Device::with('subscribe_expression', 'publish_action', 'device_type')->find($id);
        $data->sensor_id = explode('/', $data->publish_topic)[4];
        $device_types = DeviceType::all(['id', 'name']);
        $status_types = StatusType::all(['id', 'name']);

        return view('admin.devices.edit', compact('data', 'device_types', 'status_types'));
    }

    public function update(UpdateDeviceRequest $request, $id)
    {
        $validated = $request->all();

        DB::transaction(function () use ($validated, $request, $id) {

            // OLD CODES
            // $topics = explode('/', $validated['publish_topic']);
            // $branch = $topics[1];
            // $building = $topics[2];
            // $room = $topics[3];
            // Device::find($id)->update([
            //     'device_id' => $validated['device_id'],
            //     'device_type_id' => $validated['device_type_id'],
            //     'publish_topic' => strtolower($validated['publish_topic']),
            //     'subscribe_topic' => strtolower($validated['subscribe_topic']),
            //     'branch' => strtolower($branch),
            //     'building' => strtolower($building),
            //     'room' => $room
            // ]);

            $subscribe_topic = implode('/', array(
                    Setting::first()->mqtt_main_topic ?? "mcc",
                    str_replace(" ","-",strtolower($validated['branch'])),
                    str_replace(" ","-",strtolower($validated['building'])),
                    str_replace(" ","-",strtolower($validated['room'])),
                    str_replace(" ","-",strtolower($validated['sensor_id'])),
                    "sub"
            ));

            $publish_topic = implode('/', array(
                    Setting::first()->mqtt_main_topic ?? "mcc",
                    str_replace(" ","-",strtolower($validated['branch'])),
                    str_replace(" ","-",strtolower($validated['building'])),
                    str_replace(" ","-",strtolower($validated['room'])),
                    str_replace(" ","-",strtolower($validated['sensor_id'])),
                    "pub"
            ));

            $cam_topic = implode('/', array(
                    Setting::first()->mqtt_main_topic ?? "mcc",
                    str_replace(" ","-",strtolower($validated['branch'])),
                    str_replace(" ","-",strtolower($validated['building'])),
                    str_replace(" ","-",strtolower($validated['room'])),
                    str_replace(" ","-",strtolower($validated['sensor_id'])),
                    "cam"
            ));

            Device::find($id)->update([
                    'device_id' => $validated['device_id'],
                    'device_type_id' => $validated['device_type_id'],
                    'publish_topic' => strtolower($publish_topic),
                    'subscribe_topic' => strtolower($subscribe_topic),
                    'cam_topic' => strtolower($cam_topic),
                    'branch' => strtolower($validated['branch']),
                    'building' => strtolower($validated['building']),
                    'room' => strtolower($validated['room'])
            ]);

            SubscribeExpression::where('device_id', $id)->delete();
            if ($request->subscribe_expressions) {
                    foreach ($request->subscribe_expressions['expression'] as $key => $arr) {
                        SubscribeExpression::create([
                            'device_id' => $id,
                            'expression' => $request->subscribe_expressions['expression'][$key],
                            'status_type_id' => $request->subscribe_expressions['status_type'][$key],
                            'normal_state' => $request->subscribe_expressions['normal_state'][$key] == 'on' ? true : false
                        ]);
                    }
            }

            PublishAction::where('device_id', $id)->delete();
            if ($request->publish_actions) {
                    foreach ($request->publish_actions['label'] as $key => $publish_action) {
                        PublishAction::create([
                            'device_id' => $id,
                            'label' => $request->publish_actions['label'][$key],
                            'value' => $request->publish_actions['value'][$key],
                        ]);
                    }
            }
        });

        return redirect()->route('admin.devices.index')->with('success', 'Device updated successfully.');
    }

    public function destroy($id)
    {
        $data = Device::find($id);
        $data->delete();
        return redirect()->route('admin.devices.index')->with('success', 'Device deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // MQTT helpers
    // -------------------------------------------------------------------------

    public function publish(Request $request)
    {
        $actionId = is_array($request->id) ? ($request->id['id'] ?? null) : $request->id;
        $publish_action = PublishAction::find($actionId);

        if (!$publish_action || !$publish_action->device) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid publish action'
            ], 400);
        }

        $device = $publish_action->device;
        $device_status = DeviceStatus::find($request->device_status_id);
        $device_device_status = DeviceStatus::where('device_id', $device->id)->latest()->first();

        $publish_value = $publish_action->value;
        $log_id = $request->log_id ?? optional($device_device_status)->device_log_id;

        if ($log_id && str_contains($publish_value, '{{log_id}}')) {
            $publish_value = str_replace('{{log_id}}', $log_id, $publish_value);
        }

        try {
            $mqtt = MQTT::connection();
            $mqtt->publish($device->publish_topic, $publish_value, 1);
            $mqtt->loop(false, true);
        } catch (\Throwable $e) {
            \Log::error("MQTT publish failed: ".$e->getMessage());
            return response()->json(['success' => false, 'message' => 'MQTT publish failed'], 500);
        }

        DB::transaction(function () use ($publish_action, $device, $request, &$device_status, $publish_value) {
            DeviceLog::create([
                'device_id' => $device->id,
                'value'     => $publish_value,   // store string or numeric
                'type'      => 'publish'
            ]);

            if ($device_status && !$request->is_testing) {
                $device_status->update([
                    'user_id' => auth()->id()
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Published successfully.',
            'device_status' => $device_status?->load('status_type.status_type_widget')
        ]);
    }

    public function branches(Request $request)
    {
        abort_if(!$request->ajax(), 404);

        if ($request->has('search')) {
            $search = $request->input('search');

            $device_branches = Device::where('branch', 'ILIKE', '%' . $search . '%')
                    ->distinct()
                    ->get(['branch']);
        } else {
            $device_branches = Device::distinct()->get(['branch']);
        }

        $response = [];
        foreach ($device_branches as $device_branch) {
            $response[] = [
                    'id' => $device_branch->branch,
                    'text' => ucfirst($device_branch->branch)
            ];
        }

        return response()->json($response);
    }

    public function buildings(Request $request)
    {
        abort_if(!$request->ajax(), 404);

        if ($request->has('search')) {
            $search = $request->input('search');

            $device_buildings = Device::where('building', 'ILIKE', '%' . $search . '%')
                    ->distinct()
                    ->get(['building']);
        } else {
            $device_buildings = Device::distinct()->get(['building']);
        }

        $response = [];
        foreach ($device_buildings as $device_building) {
            $response[] = [
                    'id' => $device_building->building,
                    'text' => ucfirst($device_building->building)
            ];
        }

        return response()->json($response);
    }

    public function device_types(Request $request)
    {
        abort_if(!$request->ajax(), 404);

        if ($request->has('search')) {
            $search = $request->input('search');

            $device_types = DeviceType::whereHas('device')
                    ->where('name', 'ILIKE', '%' . $search . '%')
                    ->get(['id', 'name']);
        } else {
            $device_types = DeviceType::whereHas('device')
                    ->get(['id', 'name']);
        }

        $response = [];
        foreach ($device_types as $device_type) {
            $response[] = [
                    'id' => $device_type->id,
                    'text' => $device_type->name
            ];
        }

        return response()->json($response);
    }

    public function rooms(Request $request)
    {
        abort_if(!$request->ajax(), 404);

        if ($request->has('search')) {
            $search = $request->input('search');

            $device_rooms = Device::where('room', 'ILIKE', '%' . $search . '%')
                    ->distinct()
                    ->get(['room']);
        } else {
            $device_rooms = Device::distinct()
                    ->get(['room']);
        }

        $response = [];
        foreach ($device_rooms as $device_room) {
            $response[] = [
                    'id' => $device_room->room,
                    'text' => ucfirst($device_room->room)
            ];
        }

        return response()->json($response);
    }

    public function getRegisteredLocations()
    {
        // Load ALL active locations keyed by code in one query:
        //   $locationByCode['some-room'] => Location (with coordinate)
        $locationByCode = \App\Models\Location::where('is_active', true)
            ->get(['code', 'coordinate'])
            ->keyBy('code');

        // Only codes that have an active location record
        $activeLocationCodes = $locationByCode->keys()->flip(); // for O(1) isset()

        // Filter helper: keep only rooms that have an active location record
        $onlyActive = fn($grouped) => $grouped->filter(
            fn($devices, $room) => isset($activeLocationCodes[$room])
        );

        // Inject latlong from locations.coordinate onto every Device object so
        // the Leaflet map JS can read dev.latlong without any extra API call.
        $injectLatlong = function ($grouped) use ($locationByCode) {
            return $grouped->map(function ($devices, $room) use ($locationByCode) {
                $coordinate = $locationByCode[$room]->coordinate ?? null;
                return $devices->map(function ($device) use ($coordinate) {
                    $device->latlong = $coordinate; // virtual attribute, not persisted
                    return $device;
                });
            });
        };

        $onlineDevices = Device::where('is_online', true)
            ->where('last_ping_at', '>=', now()->subMinutes(15))
            ->get();

        $inactiveDevices = Device::where(function ($q) {
            $q->where('is_online', false)
              ->orWhere('last_ping_at', '<', now()->subMinutes(15))
              ->orWhereNull('last_ping_at');
        })->get();

        $registeredLocations = $injectLatlong($onlyActive(Device::all()->groupBy('room')));
        $activeLocations      = $injectLatlong($onlyActive($onlineDevices->groupBy('room')));
        $inactiveLocations    = $injectLatlong($onlyActive($inactiveDevices->groupBy('room')));

        return response()->json([
            'success' => true,
            'message' => 'Registered devices fetched successfully.',
            'data' => [
                'registeredLocations' => $registeredLocations,
                'activeLocations'     => $activeLocations,
                'inactiveLocations'   => $inactiveLocations,
            ]
        ]);
    }

    /**
     * Helper to get the path to the VPN slots JSON file.
     */
    private function getSlotsFilePath()
    {
        // Ensure this file exists in storage/app/vpn_slots.json
        return storage_path('app/vpn_slots.json');
    }

    /**
     * Requests a VPN slot and triggers streaming on the device.
     */
    public function publishStreaming()
    {
        $device = Device::find(request()->device_id);
        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.'
            ], 404);
        }

        $path = $this->getSlotsFilePath();

        // Ensure the slots file exists
        if (!file_exists($path)) {
            \Log::error("VPN Slots file missing at: " . $path);
            return response()->json([
                'success' => false,
                'message' => 'Server configuration error: Slot file missing.'
            ], 500);
        }

        $assignedSlotIndex = null;

        // --- CRITICAL: FILE LOCKING BLOCK START ---
        $fp = fopen($path, 'r+'); // Open for reading and writing

        if (flock($fp, LOCK_EX)) { // Acquire exclusive lock (wait if busy)
            $fileContent = fread($fp, filesize($path));
            $slots = json_decode($fileContent, true);

            // 1. Idempotency Check: Does this device already hold a slot?
            foreach ($slots as $key => $slot) {
                if ($slot['assigned_to'] == $device->id) {
                    // Refresh timestamp and keep existing slot
                    $slots[$key]['timestamp'] = time();
                    $assignedSlotIndex = $key; // Use array index (0-9)
                    break;
                }
            }

            // 2. If not, find a free slot
            if ($assignedSlotIndex === null) {
                foreach ($slots as $key => $slot) {
                    // Check for Stale Slots (assigned > 1 hour ago)
                    $isStale = ($slot['timestamp'] > 0 && (time() - $slot['timestamp'] > 3600));

                    if (is_null($slot['assigned_to']) || $isStale) {
                        // Assign the slot
                        $slots[$key]['assigned_to'] = $device->id;
                        $slots[$key]['timestamp'] = time();
                        $assignedSlotIndex = $key;
                        break; // Stop looking
                    }
                }
            }

            // 3. Write changes back to file if we found/assigned a slot
            if ($assignedSlotIndex !== null) {
                ftruncate($fp, 0);      // Clear file content
                rewind($fp);            // Go to start
                fwrite($fp, json_encode($slots, JSON_PRETTY_PRINT));
            }

            fflush($fp);            // Flush buffer
            flock($fp, LOCK_UN);    // Release lock
        } else {
            fclose($fp);
            return response()->json([
                'success' => false,
                'message' => 'Could not acquire lock on slot file.'
            ], 500);
        }
        fclose($fp);
        // --- FILE LOCKING BLOCK END ---

        // Check if we successfully got a slot
        if ($assignedSlotIndex === null) {
            return response()->json([
                'success' => false,
                'message' => 'System busy. All 10 streaming slots are currently in use.'
            ], 503);
        }

        try {
            $mqtt = MQTT::connection();

            // Construct JSON Payload for cccfg.py
            $payload = json_encode([
                "cmd" => "startstream",
                "slot" => $assignedSlotIndex
            ]);

            // Publish with QoS 1
            $mqtt->publish($device->publish_topic, $payload, 1);
            $mqtt->loop(false, true);

            return response()->json([
                'success' => true,
                'message' => "Streaming requested on Slot #{$assignedSlotIndex}.",
            ]);
        } catch (\Throwable $e) {
            \Log::error("publishStreaming MQTT failed: ".$e->getMessage());

            // Note: Ideally, you would re-open the file and release the slot here if MQTT fails,
            // but the stale check (1 hour) will eventually clean it up anyway.

            return response()->json([
                'success' => false,
                'message' => 'Failed to request streaming via MQTT.',
            ], 500);
        }
    }

    /**
     * Releases the VPN slot and stops streaming.
     */
    public function publishStreamingStop()
    {
        $device = Device::find(request()->device_id);
        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.'
            ], 404);
        }

        $path = $this->getSlotsFilePath();

        // Release the slot in the JSON file
        if (file_exists($path)) {
            // --- FILE LOCKING BLOCK START ---
            $fp = fopen($path, 'r+');
            if (flock($fp, LOCK_EX)) {
                $fileContent = fread($fp, filesize($path));
                $slots = json_decode($fileContent, true);

                $updated = false;
                foreach ($slots as $key => $slot) {
                    if ($slot['assigned_to'] == $device->id) {
                        $slots[$key]['assigned_to'] = null;
                        $slots[$key]['timestamp'] = 0;
                        $updated = true;
                    }
                }

                if ($updated) {
                    ftruncate($fp, 0);
                    rewind($fp);
                    fwrite($fp, json_encode($slots, JSON_PRETTY_PRINT));
                }

                fflush($fp);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
            // --- FILE LOCKING BLOCK END ---
        }

        try {
            $mqtt = MQTT::connection();
            // Send simple stop command (Client accepts string "stopstream")
            $mqtt->publish($device->publish_topic, "stopstream", 1);
            $mqtt->loop(false, true);

            return response()->json([
                'success' => true,
                'message' => 'Streaming stopped.',
            ]);
        } catch (\Throwable $e) {
            \Log::error("publishStreamingStop MQTT failed: ".$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop streaming via MQTT.'
            ], 500);
        }
    }

    public function getHours(Request $request)
    {
        $device_id = $request->device_id;
        $device = Device::find($device_id);
        $active_topic = implode('/', array(
            Setting::first()->mqtt_main_topic ?? "mcc",
            str_replace(" ","-",strtolower($device->branch)),
            str_replace(" ","-",strtolower($device->building)),
            str_replace(" ","-",strtolower($device->room)),
            "getactivehour",
            "pub"
        ));
        $inactiveTopic = implode('/', array(
            Setting::first()->mqtt_main_topic ?? "mcc",
            str_replace(" ","-",strtolower($device->branch)),
            str_replace(" ","-",strtolower($device->building)),
            str_replace(" ","-",strtolower($device->room)),
            "getinactivehour",
            "pub"
        ));
        Log::info("Active Topic: {$active_topic}");
        Log::info("Inactive Topic: {$inactiveTopic}");
        try {
            $mqtt = MQTT::connection();
            $mqtt->publish($active_topic, "getactivehour", 1);
            $mqtt->publish($inactiveTopic, "getinactivehour", 1);
            $mqtt->loop(true, true);
            return response()->json([
                'success' => true,
                'message' => 'Streaming requested.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to request streaming. ' . $e->getMessage(),
            ]);
        }
    }

    public function setActiveHours(Request $request)
    {
        $device_id = $request->device_id;
        $device = Device::find($device_id);
        $active_topic = implode('/', array(
            Setting::first()->mqtt_main_topic ?? "mcc",
            str_replace(" ","-",strtolower($device->branch)),
            str_replace(" ","-",strtolower($device->building)),
            str_replace(" ","-",strtolower($device->room)),
            "setactivehour",
            "pub"
        ));
        Log::info("Set Active Topic: {$active_topic}");
        try {
            $mqtt = MQTT::connection();
            $mqtt->publish($active_topic, $request->time, 1);
            $mqtt->loop(true, true);
            return response()->json([
                'success' => true,
                'message' => 'Streaming requested.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to request streaming. ' . $e->getMessage(),
            ]);
        }
    }

    public function setInactiveHours(Request $request)
    {
        $device_id = $request->device_id;
        $device = Device::find($device_id);
        $inactiveTopic = implode('/', array(
            Setting::first()->mqtt_main_topic ?? "mcc",
            str_replace(" ","-",strtolower($device->branch)),
            str_replace(" ","-",strtolower($device->building)),
            str_replace(" ","-",strtolower($device->room)),
            "setinactivehour",
            "pub"
        ));
        Log::info("Set Inactive Topic: {$inactiveTopic}");
        try {
            $mqtt = MQTT::connection();
            $mqtt->publish($inactiveTopic, $request->time, 1);
            $mqtt->loop(true, true);
            return response()->json([
                'success' => true,
                'message' => 'Streaming requested.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to request streaming. ' . $e->getMessage(),
            ]);
        }
    }

    // =========================================================================
    // CSV BULK IMPORT
    // =========================================================================

    /**
     * Stream a CSV template the user can fill in and re-upload.
     *
     * Columns (11 total):
     *   device_id | sensor_id | device_type_id | branch | building | room
     *   | expr_expression | expr_status_type_id | expr_normal_state
     *   | action_label    | action_value
     *
     * Multiple expressions / actions on the same row are separated by  " | "
     * (space-pipe-space).  Leave the last 5 columns blank for devices that
     * have no expressions or actions.
     *
     * A reference sheet for device_type_id and status_type_id is appended
     * below the data rows so the user can look up the correct IDs.
     */
    public function importTemplate()
    {
        $deviceTypes = DeviceType::orderBy('name')->get(['id', 'name']);
        $statusTypes = StatusType::orderBy('name')->get(['id', 'name']);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="devices_import_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($deviceTypes, $statusTypes) {
            $h = fopen('php://output', 'w');

            // --- header row ---
            fputcsv($h, [
                'device_id',
                'sensor_id',
                'device_type_id',
                'branch',
                'building',
                'room',
                'expr_expression',       // pipe-separated  e.g. {{value}} == 'nok' | {{value}} == 'ok'
                'expr_status_type_id',   // pipe-separated status_type IDs, same count as expr_expression
                'expr_normal_state',     // pipe-separated  off=TRIGGER WARNING  on=NORMAL STATE
                'action_label',          // pipe-separated publish-action labels
                'action_value',          // pipe-separated publish-action command values
            ]);

            // --- example row (mirrors the real create-device payload) ---
            fputcsv($h, [
                'wsid_b1120874-albuzrl1',
                'albuzrl1',
                '7',
                'poc',
                'brinks',
                'wsid_b1120874',
                "{{value}} == 'albuzrlnok' | {{value}} == 'albuzrlok'",
                '2 | 2',
                'off | on',
                'capture | Disable Buzzer | Enable Buzzer | Reset Control',
                'cambymcc_{{log_id}} | albuzrloffbymcc | albuzrlonbymcc | resetcontrolbymcc',
            ]);

            // --- blank example (no expressions / actions) ---
            fputcsv($h, [
                'demo-unit-01', 'sensor-01', '7',
                'poc', 'brinks', 'room-01',
                '', '', '', '', '',
            ]);

            // --- reference: device types ---
            fputcsv($h, []);
            fputcsv($h, ['=== DEVICE TYPE REFERENCE — do not edit below this line ===']);
            fputcsv($h, ['device_type_id', 'device_type_name']);
            foreach ($deviceTypes as $dt) {
                fputcsv($h, [$dt->id, $dt->name]);
            }

            // --- reference: status types ---
            fputcsv($h, []);
            fputcsv($h, ['=== STATUS TYPE REFERENCE — do not edit below this line ===']);
            fputcsv($h, ['status_type_id', 'status_type_name']);
            foreach ($statusTypes as $st) {
                fputcsv($h, [$st->id, $st->name]);
            }

            fclose($h);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process an uploaded CSV and bulk-create devices with their
     * subscribe expressions and publish actions.
     *
     * Mirrors exactly what store() does for each device row.
     * Each row is wrapped in a DB transaction; failures are collected and
     * returned without aborting the rest of the import.
     *
     * Returns JSON: { success: true, created: int, skipped: int, errors: string[] }
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $mqttTopic = Setting::first()->mqtt_main_topic ?? 'mcc';
        $slug      = fn (string $v): string => str_replace(' ', '-', strtolower(trim($v)));

        $pipeSplit = fn (?string $cell): array => array_values(
            array_filter(array_map('trim', explode('|', $cell ?? '')))
        );

        // Pre-load lookup maps to avoid N+1 queries
        $deviceTypeMap  = DeviceType::all()->keyBy('id');
        $statusTypeMap  = StatusType::all()->keyBy('id');

        $handle  = fopen($request->file('file')->getRealPath(), 'r');
        $created = 0;
        $updated = 0;
        $errors  = [];
        $rowNum  = 0;

        fgetcsv($handle); // skip header row

        while (($line = fgetcsv($handle)) !== false) {
            $rowNum++;

            $firstCell = trim($line[0] ?? '');
            if ($firstCell === '' || str_starts_with($firstCell, '===')) {
                break;
            }

            $line = array_map('trim', $line);

            [
                $deviceId,
                $sensorId,
                $deviceTypeId,
                $branch,
                $building,
                $room,
                $rawExpressions,
                $rawStatusTypeIds,
                $rawNormalStates,
                $rawActionLabels,
                $rawActionValues,
            ] = array_pad($line, 11, '');

            // --- validate required device fields ---
            if (!$deviceId || !$sensorId || !$deviceTypeId || !$branch || !$building || !$room) {
                $errors[] = "Row {$rowNum}: missing required field(s) — device_id, sensor_id, device_type_id, branch, building, room are all required.";
                continue;
            }

            // --- validate device_type_id ---
            $deviceType = $deviceTypeMap->get((int) $deviceTypeId);
            if (!$deviceType) {
                $errors[] = "Row {$rowNum}: device_type_id \"{$deviceTypeId}\" does not exist. Check the Device Type reference sheet in the template.";
                continue;
            }

            // --- validate room exists in locations ---
            if (!\App\Models\Location::where('code', strtolower(trim($room)))->exists()) {
                $errors[] = "Row {$rowNum}: room \"{$room}\" does not exist in the locations list. Add it to Locations first.";
                continue;
            }

            // --- parse expressions ---
            $expressions   = $pipeSplit($rawExpressions);
            $statusTypeIds = $pipeSplit($rawStatusTypeIds);
            $normalStates  = $pipeSplit($rawNormalStates);
            $exprCount     = count($expressions);

            if ($exprCount > 0 && (count($statusTypeIds) !== $exprCount || count($normalStates) !== $exprCount)) {
                $errors[] = "Row {$rowNum}: expr_expression, expr_status_type_id and expr_normal_state must have the same number of pipe-separated values.";
                continue;
            }

            // --- validate every status_type_id before touching the DB ---
            $rowHasError = false;
            foreach ($statusTypeIds as $i => $stId) {
                if (!$statusTypeMap->has((int) $stId)) {
                    $errors[] = "Row {$rowNum}: expr_status_type_id \"{$stId}\" (expression " . ($i + 1) . ") does not exist. Check the Status Type reference sheet in the template.";
                    $rowHasError = true;
                }
            }
            if ($rowHasError) continue;

            // --- parse publish actions ---
            $actionLabels = $pipeSplit($rawActionLabels);
            $actionValues = $pipeSplit($rawActionValues);

            if (count($actionLabels) !== count($actionValues)) {
                $errors[] = "Row {$rowNum}: action_label and action_value must have the same number of pipe-separated values.";
                continue;
            }

            // --- build MQTT topics ---
            $subscribeTopic = implode('/', [$mqttTopic, $slug($branch), $slug($building), $slug($room), $slug($sensorId), 'sub']);
            $publishTopic   = implode('/', [$mqttTopic, $slug($branch), $slug($building), $slug($room), $slug($sensorId), 'pub']);
            $camTopic       = implode('/', [$mqttTopic, $slug($branch), $slug($building), $slug($room), $slug($sensorId), 'cam']);

            // --- INSERT or UPDATE based on branch+building+room+sensor_id uniqueness ---
            // The subscribe_topic already encodes all four fields uniquely, so we use it
            // as the existence key rather than re-querying 4 separate columns.
            $existingDevice = Device::where('subscribe_topic', $subscribeTopic)->first();

            try {
                DB::transaction(function () use (
                    $existingDevice,
                    $deviceId, $deviceType,
                    $subscribeTopic, $publishTopic, $camTopic,
                    $branch, $building, $room,
                    $expressions, $statusTypeIds, $normalStates,
                    $actionLabels, $actionValues,
                    &$created, &$updated
                ) {
                    if ($existingDevice) {
                        // UPDATE — replace device fields, wipe and recreate relations
                        $existingDevice->update([
                            'device_id'       => $deviceId,
                            'device_type_id'  => $deviceType->id,
                            'subscribe_topic' => $subscribeTopic,
                            'publish_topic'   => $publishTopic,
                            'cam_topic'       => $camTopic,
                            'branch'          => strtolower(trim($branch)),
                            'building'        => strtolower(trim($building)),
                            'room'            => strtolower(trim($room)),
                        ]);

                        $device = $existingDevice;

                        SubscribeExpression::where('device_id', $device->id)->delete();
                        PublishAction::where('device_id', $device->id)->delete();

                        $updated++;
                    } else {
                        // INSERT — brand new device
                        $device = Device::create([
                            'device_id'       => $deviceId,
                            'device_type_id'  => $deviceType->id,
                            'subscribe_topic' => $subscribeTopic,
                            'publish_topic'   => $publishTopic,
                            'cam_topic'       => $camTopic,
                            'branch'          => strtolower(trim($branch)),
                            'building'        => strtolower(trim($building)),
                            'room'            => strtolower(trim($room)),
                        ]);

                        $created++;
                    }

                    foreach ($expressions as $i => $expr) {
                        SubscribeExpression::create([
                            'device_id'      => $device->id,
                            'expression'     => $expr,
                            'status_type_id' => (int) $statusTypeIds[$i],
                            'normal_state'   => strtolower(trim($normalStates[$i])) === 'on',
                        ]);
                    }

                    foreach ($actionLabels as $i => $label) {
                        PublishAction::create([
                            'device_id' => $device->id,
                            'label'     => $label,
                            'value'     => $actionValues[$i],
                        ]);
                    }
                });

            } catch (\Throwable $e) {
                $errors[] = "Row {$rowNum}: failed to save — " . $e->getMessage();
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'created' => $created,
            'updated' => $updated,
            'skipped' => count($errors),
            'errors'  => $errors,
        ]);
    }

    /**
     * Export devices as CSV in the same 11-column format as importTemplate().
     *
     * Filter: ?rooms[]=room1&rooms[]=room2
     * If no rooms supplied, all devices are exported.
     *
     * sensor_id is derived from publish_topic (segment index 4).
     * Expressions and actions are pipe-joined within each cell.
     */
    public function exportCsv(Request $request)
    {
        $rooms = array_filter((array) $request->input('rooms', []));

        $query = Device::with(['device_type', 'subscribe_expression.status_type', 'publish_action'])
            ->orderBy('room')
            ->orderBy('device_id');

        if (!empty($rooms)) {
            $query->whereIn('room', $rooms);
        }

        $devices  = $query->get();
        $filename = 'devices_export_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($devices) {
            $h = fopen('php://output', 'w');

            // Same header as importTemplate()
            fputcsv($h, [
                'device_id',
                'sensor_id',
                'device_type_id',
                'branch',
                'building',
                'room',
                'expr_expression',
                'expr_status_type_id',
                'expr_normal_state',
                'action_label',
                'action_value',
            ]);

            foreach ($devices as $device) {
                // sensor_id = segment 4 of publish_topic  (mcc/branch/building/room/sensor_id/pub)
                $topicParts = explode('/', $device->publish_topic);
                $sensorId   = $topicParts[4] ?? '';

                // Pipe-join expressions
                $expressions   = $device->subscribe_expression;
                $exprExpr      = $expressions->pluck('expression')->implode(' | ');
                $exprStId      = $expressions->pluck('status_type_id')->implode(' | ');
                $exprNormal    = $expressions->map(fn ($e) => $e->normal_state ? 'on' : 'off')->implode(' | ');

                // Pipe-join actions
                $actions       = $device->publish_action;
                $actionLabel   = $actions->pluck('label')->implode(' | ');
                $actionValue   = $actions->pluck('value')->implode(' | ');

                fputcsv($h, [
                    $device->device_id,
                    $sensorId,
                    $device->device_type_id,
                    $device->branch,
                    $device->building,
                    $device->room,
                    $exprExpr,
                    $exprStId,
                    $exprNormal,
                    $actionLabel,
                    $actionValue,
                ]);
            }

            fclose($h);
        };

        return response()->stream($callback, 200, $headers);
    }
}