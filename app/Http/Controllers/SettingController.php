<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\StatusType;
use App\Models\StatusTypeWidget;
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
        $status_type_widgets = StatusTypeWidget::all(['id']);
        return view('admin.settings.index', compact('data', 'status_types', 'status_type_widgets'));
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required',
            'status_types' => 'array',
            'status_types.*' => 'string',
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

                // delete old file
                if (Storage::disk('public')->exists($old_photo)) {
                    Storage::disk('public')->delete($old_photo);
                }
            }

            $data->update([
                'app_name' => $validated['app_name'],
                'logo' => $path
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
