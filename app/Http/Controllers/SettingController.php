<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\StatusType;
use App\Models\StatusTypeWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
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

        DB::transaction(function () use ($validated, $data) {
            $data->update([
                'app_name' => $validated['app_name']
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
