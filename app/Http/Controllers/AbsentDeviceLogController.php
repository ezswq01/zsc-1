<?php

namespace App\Http\Controllers;

use App\Models\AbsentLog;
use Yajra\DataTables\Facades\DataTables;

class AbsentDeviceLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:device-logs-read')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::eloquent(AbsentLog::with('absent_device')->select('absent_logs.*'))
                ->addIndexColumn()
                ->addColumn('absent_device_id', function ($model) {
                    return $model->absent_device->absent_device_id;
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

        return view('admin.absent_device_logs.index');
    }
}
