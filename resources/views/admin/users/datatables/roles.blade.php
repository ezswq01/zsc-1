@foreach ($model->roles as $role)
    <span>{{ ucfirst($role->name) }}</span>
    <span>{{ $loop->last ? '' : '|' }}</span>
@endforeach
