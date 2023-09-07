<?php

namespace App\Http\Controllers;

use App\Models\DeviceLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DeviceLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::eloquent(DeviceLog::with('device')->select('device_logs.*'))
                ->addIndexColumn()
                ->addColumn('device_id', function ($model) {
                    return $model->device->device_id;
                })
                ->addColumn('type', function ($model) {
                    return $model->type;
                })
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
                ->toJson();
        }

        return view('admin.device_logs.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
}
