<?php

namespace App\Http\Controllers;

use App\Models\Notif;
use Illuminate\Http\Request;

class NotifController extends Controller
{
    public function index(Request $request)
    {
        $notif = Notif::with('absent_device', 'device');

        return response()->json([
            'success' => true,
            'message' => 'Get data successfully.',
            'data' => $notif->orderByDesc('id')->paginate($request->limit)
        ]);
    }

    public function read($id)
    {
        $notif = Notif::findOrFail($id);

        $notif->update([
            'notif_status' => 'read'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Read notification successfully.',
            'data' => $notif
        ]);
    }
}
