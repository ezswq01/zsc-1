<?php

namespace App\Http\Controllers;

use App\Events\CamDataEvent;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
			'file' => 'required|file',
			'latlong' => 'required'
		]);

		try {
			$file = $request->file('file');
			$file_name = $file->getClientOriginalName();
			$app = explode('_', $file_name);
			$payload_id = explode('.', $app[1])[0];
			$file_ext = $file->getClientOriginalExtension();

			if ($app[0] !== 'cambymcc') {
					return response()->json([
						'success' => false,
						'message' => 'Invalid app code. Please use the correct app code',
					], 400);
			}

			// $path = $file->store(
			//     'payloads/cam',
			//     'public'
			// );

			$device = Device::where('id', DeviceLog::find($payload_id)->device_id)->first();
			if (!$device) {
					return response()->json([
						'success' => false,
						'message' => 'Device not found.',
					], 404);
			}
			if (!isset($device->cam_topic)) {
					$cam_topic = implode('/', array(
						Setting::first()->mqtt_main_topic ?? "mcc",
						str_replace(" ","-", strtolower($device->branch)),
						str_replace(" ","-", strtolower($device->building)),
						str_replace(" ","-", strtolower($device->room)),
						str_replace(" ","-", strtolower($device->device_id)),
						"cambymcc"
					));
					$device->cam_topic = $cam_topic;
					$device->save();
			}
			$cam_topic = $device->cam_topic;
			$cam_topic = str_replace('/', '_', $cam_topic);
			$file_name_original = $cam_topic . '_' . now()->format('YmdHis') . '.' . $file_ext;
			$path = $file->storeAs('payloads/cam', $file_name_original, 'public');

			DB::table('cam_payloads')->insert([
					'device_log_id' => $payload_id,
					'file_name' => $file_name,
					'file' => $path,
					'created_at' => now(),
					'updated_at' => now(),
					'latlong' => $request->latlong,
			]);

			CamDataEvent::dispatch([
					'type' => 'dynamic_device',
					'data' => DeviceLog::find($payload_id)->load('cam_payloads'),
			]);
			
		} catch (\Exception $e) {
			if (isset($path) && Storage::disk('public')->exists($path)) {
					Storage::disk('public')->delete($path);
			}
			return response()->json([
					'success' => false,
					'message' => $e->getMessage(),
			], 500);
		}

		return response()->json([
			'success' => true,
			'message' => 'Cam payload saved successfully.',
		], 200);
	}
}
