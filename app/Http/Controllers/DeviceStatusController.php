<?php

namespace App\Http\Controllers;

use App\Models\DeviceStatus;
use Illuminate\Http\Request;

class DeviceStatusController extends Controller
{
    public function get_device_status($id)
    {
        $deviceStatus = DeviceStatus::with(['device'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $deviceStatus
        ]);
    }

    public function notes(Request $request)
    {
        if (!$request->notes) {
            return response()->json([
                'success' => false,
                'message' => 'Notes is required.'
            ], 500);
        }

        if ($request->notes === 'Normal State') {
            return response()->json([
                'success' => false,
                'message' => 'Notes cannot be Normal State!'
            ], 500);
        }

        $device_status = DeviceStatus::find($request->device_status_id);
        $device_id = $device_status->device_id;
        $status_type_id = $device_status->status_type_id;

        $normal_state_device_status = DeviceStatus::where('device_id', $device_id)
            ->where('status_type_id', $status_type_id)
            ->where('notes', 'Normal State')
            ->where('created_at', '>', $device_status->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($normal_state_device_status) {
            $device_status->notes = $request->notes;
            $device_status->marked_as_read = true;
            $device_status->noted = true;
            $device_status->user_id = auth()->user()->id;
            $device_status->save();
            DeviceStatus::where('device_id', $device_id)
                ->where('status_type_id', $status_type_id)
                ->where('notes', '!=', 'Normal State')
                ->update([
                    'marked_as_read' => true
                ]);
        } else {
            $device_status->notes = $request->notes;
            $device_status->marked_as_read = false;
            $device_status->noted = true;
            $device_status->user_id = auth()->user()->id;
            $device_status->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notes updated successfully.',
            'device_status' => $device_status->load('status_type.status_type_widget')
        ]);
    }

    public function index(Request $request)
    {
        $device_statuses = DeviceStatus::with('device.publish_action', 'user');
        if (request()->date) {
            $from_date = explode(' - ', request()->date)[0];
            $to_date = explode(' - ', request()->date)[1];
            $device_statuses = $device_statuses->whereBetween('created_at', [$from_date, $to_date]);
        }
        $device_statuses = $device_statuses
            ->whereHas('device', function ($query) use ($request) {
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
                }}
            )->orderBy('id', 'desc')->get();

        return view('admin.device_statuses.index', compact('device_statuses'));
    }
}
