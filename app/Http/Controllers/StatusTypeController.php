<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusType\StoreStatusTypeRequest;
use App\Http\Requests\StatusType\UpdateStatusTypeRequest;
use App\Models\DeviceStatus;
use App\Models\StatusType;
use App\Models\StatusTypeWidget;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StatusTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:status-types-create')->only(['create', 'store']);
        $this->middleware('can:status-types-read')->only(['index', 'show']);
        $this->middleware('can:status-types-update')->only(['edit', 'update']);
        $this->middleware('can:status-types-delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(StatusType::query())
                ->addIndexColumn()
                ->editColumn('name', function ($model) {
                    return '<a href="' . route('admin.status_types.show', $model->id) . '">' . $model->name . '</a>';
                })
                ->addColumn('options', 'admin.status_types.datatables.options')
                ->setRowAttr([
                    'data-model-id' => function ($model) {
                        return $model->id;
                    }
                ])
                ->rawColumns(['name', 'options'])
                ->toJson();
        }

        return view('admin.status_types.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.status_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStatusTypeRequest $request)
    {
        $validated = $request->all();

        StatusType::create($validated);
        return redirect()->route('admin.status_types.index')
            ->with('success', 'Device Type created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = StatusType::findOrFail($id);
        return view('admin.status_types.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = StatusType::findOrFail($id);
        return view('admin.status_types.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStatusTypeRequest $request, $id)
    {
        $validated = $request->all();

        StatusType::find($id)->update($validated);

        return redirect()->route('admin.status_types.index')
            ->with('success', 'Device Type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        StatusType::find($id)->delete();
        return redirect()->route('admin.status_types.index')
            ->with('success', 'Device Type deleted successfully');
    }

    public function history($id, Request $request)
    {
        $status_type_widgets = StatusTypeWidget::where('status_type_id', $id)->with([
            'status_type.device_status' => function ($query) use ($request) {
                return $query->orderBy('id', 'desc')
                    ->with('device.publish_action')
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
                        }
                    });
            }
        ])->orderBy('id', 'desc')->first();

        return view('admin.status_types.history', compact('status_type_widgets'));
    }
}
