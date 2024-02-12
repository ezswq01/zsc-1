@extends('auth.layout.main')
@section('content')
  <form action="/login"
    class="login-form"
    method="POST">
    @csrf
    <div class="card mb-0">
      <div class="card-body">
        <div class="text-center mb-3">
          <div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
            <img alt=""
              class="h-48px"
              src="/assets/images/logo_icon.svg">
          </div>
          <h5 class="mb-0">Login to your account</h5>
          <span class="d-block text-muted">Enter your credentials below</span>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <div class="form-control-feedback form-control-feedback-start">
            <input class="form-control"
              name="email"
              placeholder="john@doe.com"
              type="text">
            <div class="form-control-feedback-icon">
              <i class="ph-user-circle text-muted"></i>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="form-control-feedback form-control-feedback-start">
            <input class="form-control"
              name="password"
              placeholder="•••••••••••"
              type="password">
            <div class="form-control-feedback-icon">
              <i class="ph-lock text-muted"></i>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <button class="btn btn-primary w-100"
            type="submit">Sign in</button>
        </div>
      </div>
    </div>
  </form>
@endsection
