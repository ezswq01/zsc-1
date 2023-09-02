<?php

namespace App\Http\Controllers;

use App\Models\StatusType;
use App\Models\StatusTypeWidget;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $status_type_widgets = StatusTypeWidget::with('status_type')->get();
        return view('admin.dashboard', compact('status_type_widgets'));
    }
}
