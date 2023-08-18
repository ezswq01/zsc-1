<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\PublishAction;
use App\Models\SubscribeExpression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = Device::all();
        return view('admin.devices.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.devices.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required',
            'device_type_id' => 'required',
            'publish_topic' => 'required',
            'subscribe_topic' => 'required',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $device = Device::create([
                'device_id' => $validated['device_id'],
                'device_type_id' => $validated['device_type_id'],
                'publish_topic' => $validated['publish_topic'],
                'subscribe_topic' => $validated['subscribe_topic'],
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
        $data = Device::with('subscribe_expression', 'publish_action', 'device_type')->find($id);
        return view('admin.devices.show', compact('data'));
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
        return view('admin.devices.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'device_id' => 'required',
            'device_type_id' => 'required',
            'publish_topic' => 'required',
            'subscribe_topic' => 'required',
        ]);

        DB::transaction(function () use ($validated, $request, $id) {
            Device::find($id)->update([
                'device_id' => $validated['device_id'],
                'device_type_id' => $validated['device_type_id'],
                'publish_topic' => $validated['publish_topic'],
                'subscribe_topic' => $validated['subscribe_topic'],
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
}
