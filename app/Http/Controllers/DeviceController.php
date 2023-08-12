<?php

namespace App\Http\Controllers;

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
        $datas = $this->dummyDataFromDB();
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
        dd($request->all());

        $validated = $request->validate([
            'id' => 'required',
            'device_type_id' => 'required',
            'publish_topic' => 'required',
            'subscribe_topic' => 'required',
        ]);

        DB::transaction(function () use($validated)) {
            $device = Device::create([
                'id' => $validated['id'],
                'device_type_id' => $validated['device_type_id'],
                'publish_topic' => $validated['publish_topic'],
                'subscribe_topic' => $validated['subscribe_topic'],
            ]);

            foreach ($request->subscribe_expressions as $subscribe_expression)
            {
                SubscribeExpression::create([
                    'device_id' => $device['id'],
                    'expression' => $subscribe_expression['expression'],
                    'status_type_id' => $subscribe_expression['status_type_id'],
                ]);
            }

            foreach ($request->publis_actions as $publis_action)
            {
                PublisAction::create([
                    'device_id' => $device['id'],
                    'label' => $publis_action['label'],
                    'value' => $publis_action['value'],
                ]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function dummyDataFromDB()
    {
        $datas = [
            [
                'id' => 'DEVICE_2138321',
                'device_type_id' => '1',
                'publish_topic' => 'devices/1/publish',
                'subscribe_topic' => 'devices/1/subscribe',
                'created_at' => '2021-08-11 07:40:25',
                'updated_at' => '2021-08-11 07:40:25',
            ],
            [
                'id' => 'DEVICE_2138322',
                'device_type_id' => '1',
                'publish_topic' => 'devices/2/publish',
                'subscribe_topic' => 'devices/2/subscribe',
                'created_at' => '2021-08-11 07:40:25',
                'updated_at' => '2021-08-11 07:40:25',
            ],
            [
                'id' => 'DEVICE_2138323',
                'device_type_id' => '1',
                'publish_topic' => 'devices/3/publish',
                'subscribe_topic' => 'devices/3/subscribe',
                'created_at' => '2021-08-11 07:40:25',
                'updated_at' => '2021-08-11 07:40:25',
            ],
            [
                'id' => 'DEVICE_2138324',
                'device_type_id' => '1',
                'publish_topic' => 'devices/4/publish',
                'subscribe_topic' => 'devices/4/subscribe',
                'created_at' => '2021-08-11 07:40:25',
                'updated_at' => '2021-08-11 07:40:25',
            ],
            [
                'id' => 'DEVICE_2138325',
                'device_type_id' => '1',
                'publish_topic' => 'devices/5/publish',
                'subscribe_topic' => 'devices/5/subscribe',
                'created_at' => '2021-08-11 07:40:25',
                'updated_at' => '2021-08-11 07:40:25',
            ],
        ];

        $datas = collect($datas);

        foreach ($datas as $index => $data) {
            $datas[$index] = (object) $data;
        }

        return $datas;
    }
}
