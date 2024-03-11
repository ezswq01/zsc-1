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
        $deviceStatus = DeviceStatus::find($request->device_status_id);
        $deviceStatus->notes = $request->notes;
        $deviceStatus->marked_as_read = $request->marked_as_read;
        $deviceStatus->save();

        return response()->json([
            'success' => true,
            'message' => 'Notes updated successfully.',
            'device_status' => $deviceStatus?->load('status_type.status_type_widget')
        ]);
    }

    public function index(Request $request)
    {
        $device_statuses = DeviceStatus::with('device.publish_action', 'user')
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
