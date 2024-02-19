<?php

namespace App\Http\Controllers;

use App\Http\Requests\Device\StoreDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\DeviceStatus;
use App\Models\DeviceType;
use App\Models\PublishAction;
use App\Models\StatusType;
use App\Models\SubscribeExpression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;
use Yajra\DataTables\Facades\DataTables;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:devices-create')->only(['create', 'store']);
        $this->middleware('can:devices-read')->only(['index', 'show']);
        $this->middleware('can:devices-update')->only(['edit', 'update']);
        $this->middleware('can:devices-delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(Device::query())
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $device_types = DeviceType::all(['id', 'name']);
        $status_types = StatusType::all(['id', 'name']);

        return view('admin.devices.create', compact('device_types', 'status_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDeviceRequest $request)
    {
        $validated = $request->all();

        DB::transaction(function () use ($validated, $request) {
            $topics = explode('/', $validated['publish_topic']);

            $branch = $topics[1];
            $building = $topics[2];
            $room = $topics[3];

            $device = Device::create([
                'device_id' => $validated['device_id'],
                'device_type_id' => $validated['device_type_id'],
                'publish_topic' => strtolower($validated['publish_topic']),
                'subscribe_topic' => strtolower($validated['subscribe_topic']),
                'branch' => strtolower($branch),
                'building' => strtolower($building),
                'room' => $room
            ]);

            if ($request->subscribe_expressions) {
                foreach ($request->subscribe_expressions['expression'] as $key => $subscribe_expression) {
                    SubscribeExpression::create([
                        'device_id' => $device->id,
                        'expression' => $request->subscribe_expressions['expression'][$key],
                        'status_type_id' => $request->subscribe_expressions['status_type'][$key],
                    ]);
                }
            }

            if ($request->publish_actions) {
                foreach ($request->publish_actions['label'] as $key => $publish_action) {
                    PublishAction::create([
                        'device_id' => $device->id,
                        'label' => $publish_action['label'][$key],
                        'value' => $publish_action['value'][$key],
                    ]);
                }
            }
        });

        return redirect()->route('admin.devices.index')->with('success', 'Device created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (request()->ajax()) {
            $device_status = DeviceStatus::with('device', 'device_log')
                ->select('device_status.*')
                ->where('device_status.device_id', $id);

            return DataTables::eloquent($device_status)
                ->editColumn('created_at', function ($model) {
                    return [
                        'display' => date('Y-m-d H:i:s', strtotime($model->created_at)),
                        'timestamp' => strtotime($model->created_at)
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

        $data = Device::with('subscribe_expression', 'publish_action', 'device_type')->find($id);
        $device_types = DeviceType::all(['id', 'name']);
        $status_types = StatusType::all(['id', 'name']);
        $device_logs = DeviceLog::with('device')->where('device_id', $id)->get();

        return view('admin.devices.show', compact('data', 'device_types', 'status_types', 'device_logs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Device::with('subscribe_expression', 'publish_action', 'device_type')->find($id);
        $device_types = DeviceType::all(['id', 'name']);
        $status_types = StatusType::all(['id', 'name']);

        return view('admin.devices.edit', compact('data', 'device_types', 'status_types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDeviceRequest $request, $id)
    {
        $validated = $request->all();

        DB::transaction(function () use ($validated, $request, $id) {
            $topics = explode('/', $validated['publish_topic']);

            $branch = $topics[1];
            $building = $topics[2];
            $room = $topics[3];

            Device::find($id)->update([
                'device_id' => $validated['device_id'],
                'device_type_id' => $validated['device_type_id'],
                'publish_topic' => strtolower($validated['publish_topic']),
                'subscribe_topic' => strtolower($validated['subscribe_topic']),
                'branch' => strtolower($branch),
                'building' => strtolower($building),
                'room' => $room
            ]);

            SubscribeExpression::where('device_id', $id)->delete();
            PublishAction::where('device_id', $id)->delete();

            if ($request->subscribe_expressions) {
                foreach ($request->subscribe_expressions['expression'] as $key => $arr) {
                    SubscribeExpression::create([
                        'device_id' => $id,
                        'expression' => $request->subscribe_expressions['expression'][$key],
                        'status_type_id' => $request->subscribe_expressions['status_type'][$key],
                    ]);
                }
            }

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Device::find($id);
        $data->delete();
        return redirect()->route('admin.devices.index')->with('success', 'Device deleted successfully.');
    }

    public function publish(Request $request)
    {
        $publish_action = PublishAction::find($request->id);
        $device = $publish_action->device;
        $device_status = DeviceStatus::find($request->device_status_id);
        // $subscribe_expression = $device->subscribe_expression;

        // create mqtt connection
        $mqtt = MQTT::connection();

        // publish message
        $mqtt->publish($device->publish_topic, $publish_action->value, 1);
        $mqtt->loop(true, true);

        DB::transaction(function () use ($publish_action, $device, $request, &$device_status) {

            // save publish action to device log
            DeviceLog::create([
                'device_id' => $device->id,
                'value' => $publish_action->value,
                'type' => 'publish'
            ]);

            // @note : this is not needed
            // update or create device status
            // Device::evalValue($device->id, $device_log->id, $subscribe_expression, $publish_action->value);

            // dashboard action only.
            // delete current device_status to point that i handled.
            if (!$request->is_testing) {
                $device_status->update([
                    'marked_as_read' => true,
                    'notes' => $request->notes
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
}
