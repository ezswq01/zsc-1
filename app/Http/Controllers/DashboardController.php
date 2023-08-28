<?php

namespace App\Http\Controllers;

use App\Models\StatusType;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $status_types = StatusType::with('device_status')->get();

        return view('admin.dashboard', compact('status_types'));
    }
}
