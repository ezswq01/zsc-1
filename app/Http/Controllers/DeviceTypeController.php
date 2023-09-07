<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviceType\StoreDeviceTypeRequest;
use App\Http\Requests\DeviceType\UpdateDeviceTypeRequest;
use App\Models\DeviceType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DeviceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(DeviceType::query())
                ->addIndexColumn()
                ->editColumn('name', function ($model) {
                    return '<a href="' . route('admin.device_types.show', $model->id) . '">' . $model->name . '</a>';
                })
                ->addColumn('options', 'admin.device_types.datatables.options')
                ->setRowAttr([
                    'data-model-id' => function ($model) {
                        return $model->id;
                    }
                ])
                ->rawColumns(['name', 'options'])
                ->toJson();
        }

        return view('admin.device_types.index');
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
