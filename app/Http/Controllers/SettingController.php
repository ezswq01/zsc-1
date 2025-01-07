<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\StatusType;
use App\Models\StatusTypeWidget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
	public function __construct()
	{
		$this->middleware('can:systems-control')->only(['index', 'update']);
	}

	public function index()
	{
		$data = Setting::first();
		$status_types = StatusType::all(['id', 'name']);
		$status_type_widgets = StatusTypeWidget::all(['status_type_id']);
		$users = User::all(['id', 'name']);
		return view('admin.settings.index', compact('data', 'status_types', 'status_type_widgets', 'users'));
	}

	public function update($id, Request $request)
	{
		$validated = $request->validate([
			'app_name' => 'required',
			'mqtt_main_topic' => 'required',
			'status_types' => 'array',
			'status_types.*' => 'string',
			'is_access_device' => 'sometimes',
			'email_users' => 'sometimes',
			'location_widget' => 'sometimes'
		]);

		$data = Setting::findOrFail($id);

		DB::transaction(function () use ($validated, $request, $data) {
			if ($request->hasFile('logo')) {
				$file = $request->file('logo');

				$old_photo = $data->logo;

				// store file
				$path = $file->store(
					'settings/logo',
					'public'
				);

				$data->logo = $path;
				$data->save();

				// delete old file
				if (Storage::disk('public')->exists($old_photo)) {
					Storage::disk('public')->delete($old_photo);
				}
			}

			$data->update([
				'app_name' => $validated['app_name'],
				'mqtt_main_topic' => $validated['mqtt_main_topic'],
				'is_access_device' => isset($validated['is_access_device']) 
					&& $validated['is_access_device'] == "on" 
					? true 
					: false,
				'location_widget' => isset($validated['location_widget']) 
					&& $validated['location_widget'] == "on" 
					? true 
					: false,
				'email_users' => isset($validated['email_users']) 
					? $validated['email_users'] 
					: []
			]);

			StatusTypeWidget::where('setting_id', $data->id)->delete();
			foreach ($validated['status_types'] as $status_type_id) {
				StatusTypeWidget::create([
					'setting_id' => $data->id,
					'status_type_id' => $status_type_id
				]);
			}
		});

		return redirect()->back()->with('success', 'Settings updated successfully');
	}
}
