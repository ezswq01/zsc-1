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
            $device = Device::create([
                'device_id' => $validated['device_id'],
                'device_type_id' => $validated['device_type_id'],
                'publish_topic' => strtolower($validated['publish_topic']),
                'subscribe_topic' => strtolower($validated['subscribe_topic']),
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
            return DataTables::of(DeviceLog::query()->where('device_id', $id))
                ->addIndexColumn()
                ->editColumn('created_at', function ($model) {
                    return [
                        'display' => date('Y-m-d H:i:s', strtotime($model->created_at)),
                        'timestamp' => strtotime($model->created_at)
                    ];
                })
                ->setRowAttr([
                    'data-model-id' => function ($model) {
                        return $model->id;
                    }
                ])
                ->rawColumns(['options'])
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
            Device::find($id)->update([
                'device_id' => $validated['device_id'],
                'device_type_id' => $validated['device_type_id'],
                'publish_topic' => strtolower($validated['publish_topic']),
                'subscribe_topic' => strtolower($validated['subscribe_topic']),
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
        // $subscribe_expression = $device->subscribe_expression;

        // create mqtt connection
        $mqtt = MQTT::connection();

        // publish message
        $mqtt->publish($device->publish_topic, $publish_action->value, 1);
        $mqtt->loop(true, true);

        DB::transaction(function () use ($publish_action, $device, $request) {

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
                DeviceStatus::find($request->device_status_id)->update([
                    'marked_as_read' => true,
                    'notes' => $request->notes
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Published successfully.',
        ]);
    }
}
