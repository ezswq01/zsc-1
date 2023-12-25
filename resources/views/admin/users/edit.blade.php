@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    User - <span class="fw-normal">Edit</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="#">User</a>
                    <span class="breadcrumb-item active">Edit</span>
                </div>
                <a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                    data-bs-toggle="collapse" href="#breadcrumb_elements">
                    <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                </a>
            </div>
        </div>
    </div>
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit User</h5>
        </div>

        <div class="card-body border-top">
            <div class="row g-lg-5 g-2">
                <div class="col-lg-8 col-12">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Name</label>
                            <div class="col-lg-8">
                                <input value="{{ old('name', $user->name) }}" class="form-control" name="name"
                                    placeholder="Type Name" required type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Email</label>
                            <div class="col-lg-8">
                                <input value="{{ old('email', $user->email) }}" class="form-control" name="email"
                                    placeholder="Type Email" required type="email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Role</label>
                            <div class="col-lg-8">
                                <select class="form-control select" data-placeholder="Select Role" name="role">
                                    @foreach ($roles as $role)
                                        <option {{ $user->roles[0]->name == $role ? 'selected' : '' }}
                                            value="{{ $role }}">
                                            {{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">User Code</label>
                            <div class="col-lg-8">
                                <input value="{{ old('user_code', $user->user_code) }}" class="form-control" name="user_code"
                                    placeholder="Type User Code" required type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Password</label>
                            <div class="col-lg-8">
                                <input value="{{ old('password') }}" class="form-control" name="password"
                                    placeholder="•••••••••••" required type="password">
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary" type="submit">Submit form <i
                                    class="ph-paper-plane-tilt ms-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
