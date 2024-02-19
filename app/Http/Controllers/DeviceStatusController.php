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
}
