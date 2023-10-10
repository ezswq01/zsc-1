<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\StatusTypeWidget;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // filter by area get unique device_status
        $status_type_widgets = StatusTypeWidget::with('status_type.device_status.device.publish_action')
            ->with(['status_type.device_status.device' => function ($query) use ($request) {
                if (!empty($request->locations)) {
                    return $query->where(function ($w) use ($request) {
                        $locations = $request->locations;
                        foreach ($locations as $location) {
                            $w->orWhere('branch', $location);
                        }
                    });
                }
                return $query;
            }])
            ->get();

        $device_locations = Device::distinct()->get(['branch']);

        $status_types = [];
        foreach ($status_type_widgets as $key => $status_type_widget) {
            $status_types[$status_type_widget->status_type_id] = [
                'widget_id' => $status_type_widget->id,
                'name' => $status_type_widget->status_type->name,
                'color' => $status_type_widget->status_type->color,
                'count' => 0
            ];

            foreach ($status_type_widget->status_type->device_status as $key => $device_status) {
                if ($device_status->device && $device_status->marked_as_read == false) {
                    $status_types[$device_status->status_type_id]['count'] += 1;
                }
            }
        }
        $status_types = json_decode(json_encode($status_types));

        return view('admin.dashboard', compact('status_type_widgets', 'device_locations', 'status_types'));
    }
}
