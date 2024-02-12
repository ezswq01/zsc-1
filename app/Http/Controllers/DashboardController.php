<?php

namespace App\Http\Controllers;

use App\Models\AbsentReceivedLog;
use App\Models\Device;
use App\Models\Setting;
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
            ->get()->sortByDesc('created_at');;

        $device_locations = Device::distinct()->get(['branch']);
        $device_sub_locations = Device::distinct()->get(['building']);
        $device_location_ids = Device::distinct()->get(['room']);

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

        return view('admin.dashboard', compact(
            'device_locations',
            'device_sub_locations',
            'device_location_ids',
            'status_types',
        ));
    }

    public function ajaxDashboard(Request $request)
    {
        $status_type_widgets = StatusTypeWidget::with('status_type.device_status.device.publish_action')
            ->with(['status_type.device_status.device' => function ($query) use ($request) {
                if (!empty($request->branches)) {
                    return $query->where(function ($w) use ($request) {
                        $branches = $request->branches;
                        foreach ($branches as $branch) {
                            $w->orWhere('branch', $branch);
                        }
                    });
                }
                if (!empty($request->buildings)) {
                    return $query->where(function ($w) use ($request) {
                        $buildings = $request->buildings;
                        foreach ($buildings as $building) {
                            $w->orWhere('building', $building);
                        }
                    });
                }
                if (!empty($request->rooms)) {
                    return $query->where(function ($w) use ($request) {
                        $rooms = $request->rooms;
                        foreach ($rooms as $room) {
                            $w->orWhere('room', $room);
                        }
                    });
                }
                if (!empty($request->get('search'))) {
                    $query->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('device_id', 'ILIKE', "%$search%");
                    });
                }
                return $query;
            }])
            ->get()->sortByDesc('created_at');

        $absent_received_logs = [];
        if (Setting::first()->is_access_device) {
            $absent_received_logs = AbsentReceivedLog::with('absent_device', 'user')
                ->whereHas('absent_device', function ($query) use ($request) {
                        if (!empty($request->branches)) {
                            return $query->where(function ($w) use ($request) {
                                $branches = $request->branches;
                                foreach ($branches as $branch) {
                                    $w->orWhere('branch', $branch);
                                }
                            });
                        }
                        if (!empty($request->buildings)) {
                            return $query->where(function ($w) use ($request) {
                                $buildings = $request->buildings;
                                foreach ($buildings as $building) {
                                    $w->orWhere('building', $building);
                                }
                            });
                        }
                        if (!empty($request->rooms)) {
                            return $query->where(function ($w) use ($request) {
                                $rooms = $request->rooms;
                                foreach ($rooms as $room) {
                                    $w->orWhere('room', $room);
                                }
                            });
                        }
                        if (!empty($request->get('search'))) {
                            $query->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $w->orWhere('absent_device_id', 'ILIKE', "%$search%");
                            });
                        }
                        return $query;
                    }
                )->get();
        }

        return response()->json([
            'status_type_widgets' => $status_type_widgets,
            'absent_received_logs' => $absent_received_logs
        ], 200);
    }
}
