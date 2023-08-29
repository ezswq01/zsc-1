<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviceType\StoreDeviceTypeRequest;
use App\Http\Requests\DeviceType\UpdateDeviceTypeRequest;
use App\Models\DeviceType;
use Illuminate\Http\Request;

class DeviceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = DeviceType::all();
        return view('admin.device_types.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.device_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDeviceTypeRequest $request)
    {
        $validated = $request->all();

        DeviceType::create($validated);

        return redirect()->route('admin.device_types.index')
            ->with('success', 'Device Type created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = DeviceType::findOrFail($id);
        return view('admin.device_types.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = DeviceType::findOrFail($id);
        return view('admin.device_types.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDeviceTypeRequest $request, $id)
    {
        $validated = $request->all();

        DeviceType::find($id)->update($validated);

        return redirect()->route('admin.device_types.index')
            ->with('success', 'Device Type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DeviceType::find($id)->delete();
        return redirect()->route('admin.device_types.index')
            ->with('success', 'Device Type deleted successfully');
    }
}
