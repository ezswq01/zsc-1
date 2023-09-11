@if ($model->name == 'admin')
    <span class="badge bg-success text-bold">all access</span>
@else
    @foreach ($model->permissions as $permission)
        <span class="badge bg-info">{{ $permission->name }}</span>
    @endforeach
@endif
