<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusType\StoreStatusTypeRequest;
use App\Http\Requests\StatusType\UpdateStatusTypeRequest;
use App\Models\StatusType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StatusTypeController extends Controller
{
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
                ->addColumn('name', function ($model) {
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

        $datas = StatusType::all();
        return view('admin.status_types.index', compact('datas'));
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
}
