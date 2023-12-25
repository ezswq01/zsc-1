<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:users-create')->only(['create', 'store']);
        $this->middleware('can:users-read')->only(['index', 'show']);
        $this->middleware('can:users-update')->only(['edit', 'update']);
        $this->middleware('can:users-delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::eloquent(User::with('roles')->select('users.*'))
                ->addIndexColumn()
                ->editColumn('name', function ($model) {
                    return '<a href="' . route('admin.users.show', $model->id) . '">' . $model->name . '</a>';
                })
                ->addColumn('roles', function ($model) {
                    return $model->roles[0]->name;
                })
                ->editColumn('created_at', function ($model) {
                    return [
                        'display' => date('Y-m-d H:i:s', strtotime($model->created_at)),
                        'timestamp' => strtotime($model->created_at)
                    ];
                })
                ->addColumn('options', 'admin.users.datatables.options')
                ->setRowAttr([
                    'data-model-id' => function ($model) {
                        return $model->id;
                    }
                ])
                ->rawColumns(['name', 'roles', 'options'])
                ->toJson();
        }

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all()->pluck('name');

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->all();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_code' => $validated['user_code'],
            'password' => Hash::make($validated['password']),
            'absent_device_id' => $validated['absent_device_id'],
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all()->pluck('name');

        return view('admin.users.show', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all()->pluck('name');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $validated = $request->all();

        $user = User::findOrFail($id);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_code' => $validated['user_code'],
            'password' => Hash::make($validated['password']),
            'absent_device_id' => $validated['absent_device_id'],
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
