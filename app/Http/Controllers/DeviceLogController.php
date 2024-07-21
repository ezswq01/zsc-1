<?php

namespace App\Http\Controllers;

use App\Models\DeviceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DeviceLogController extends Controller
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
            $devices = DeviceLog::with('device')->select('device_logs.*');
            if (request()->date) {
                $from_date = explode(' - ', request()->date)[0];
                $to_date = explode(' - ', request()->date)[1];
                $devices = $devices->whereBetween('device_logs.created_at', [$from_date, $to_date]);
            }
            return DataTables::eloquent($devices)
                ->addIndexColumn()
                ->addColumn('device_id', function ($model) {
                    return $model->device->device_id;
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

    public function camPayload(Request $request) 
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $file_name = $file->getClientOriginalName();
        $app = explode('_', $file_name);
        $payload_id = $app[1];

        if ($app[0] !== 'cambymcc') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid app code. Please use the correct app code',
            ], 400);
        }

        $path = $file->store(
            'payloads/cam',
            'public'
        );

        DB::table('cam_payloads')->insert([
            'payload_id' => $payload_id,
            'file_name' => $file_name,
            'file' => $path,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cam payload saved successfully.',
        ], 200);
    }
}
