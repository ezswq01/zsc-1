<?php

namespace App\Http\Controllers;

use App\Models\StatusType;
use App\Models\StatusTypeWidget;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // filter by area
        $status_type_widgets = StatusTypeWidget::with(['status_type.device_status.device' => function ($query) use ($request) {
            if (!$request->area) {
                return $query;
            } else {
                return $query->where('publish_topic', 'like', '%' . $request->area . '%')
                    ->orWhere('subscribe_topic', 'like', '%' . $request->area . '%');
            }
        }])->get();
        
        return view('admin.dashboard', compact('status_type_widgets'));
    }
}
