@extends('admin.layout.main')

@push('header')
<div class="page-header page-header-light shadow">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Device - <span class="fw-normal">Detail</span>
            </h4>
        </div>
    </div>

    <div class="page-header-content d-lg-flex border-top">
        <div class="d-flex">
            <div class="breadcrumb py-2">
                <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                <a class="breadcrumb-item" href="{{route('admin.devices.index')}}">Device</a>
                <span class="breadcrumb-item active">{{ $data->device_id }}</span>
            </div>
            <a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse" href="#breadcrumb_elements">
                <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
            </a>
        </div>
    </div>
</div>
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Device</h5>
    </div>

    <div class="card-body border-top">
        <div class="row g-lg-5 g-2">
            <div class="col-lg-8 col-12">
                <form action="{{ route('admin.devices.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-lg-4 col-form-label">Device ID</label>
                        <div class="col-lg-8">
                            <input class="form-control" value="{{ $data->device_id }}" name="device_id" placeholder="Type Device ID" required type="text">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-4 col-form-label">Subscribe Topic</label>
                        <div class="col-lg-8">
                            <input class="form-control" value="{{ $data->subscribe_topic }}" name="subscribe_topic" placeholder="Type Subscribe Topic" required type="text">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-4 col-form-label">Publish Topic</label>
                        <div class="col-lg-8">
                            <input class="form-control" value="{{ $data->publish_topic }}" name="publish_topic" placeholder="Type Publish Topic" required type="text">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-4 col-form-label">Device Type</label>
                        <div class="col-lg-8">
                            <select class="form-control select" data-placeholder="Select Device Type" name="device_type_id">
                                <option></option>
                                @foreach ($device_types as $device_type)
                                <option {{ $data->device_type_id == $device_type->id ? 'selected' : '' }} value="{{ $device_type->id }}">{{ $device_type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="subscribe_expression">
                        <div class="text-start">
                            <button class="btn btn-primary" type="button" onclick="hanldeAddSubsribeExpression()">Add Subscribe
                                Expression</button>
                        </div>
                    </div>
                    <hr>
                    <div class="publish_button">
                        <div class="text-start">
                            <button class="btn btn-primary" type="button" onclick="handleAddPublishButtonAction()">Add Publish Button
                                Action</button>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-primary" type="submit">Submit form <i class="ph-paper-plane-tilt ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="publish_html" style="display: none;">
    <div class="publish_element">
        <div class="row mt-3">
            <label class="col-lg-4 col-form-label number">1. </label>
        </div>
        <div class="row mt-3">
            <label class="col-lg-4 col-form-label">Button Label</label>
            <div class="col-lg-8">
                <input class="form-control" name="publish_actions[label][]" placeholder="Type Button Label" required type="text">
            </div>
        </div>
        <div class="row mt-3">
            <label class="col-lg-4 col-form-label">Publish Value</label>
            <div class="col-lg-8">
                <input class="form-control" name="publish_actions[value][]" placeholder="Type Publish Value" required type="text">
            </div>
        </div>
        <div class="text-start mt-3">
            <button class="btn btn-danger" type="button" data-publish_actions="1" onclick="handleDeletePublishActions(this)">Delete</button>
        </div>
    </div>
</div>
<div class="subscribe_html" style="display: none;">
    <div class="subscribe_element">
        <div class="row mt-3">
            <label class="col-lg-4 col-form-label number">1. </label>
        </div>
        <div class="row mt-3">
            <label class="col-lg-4 col-form-label">Expression</label>
            <div class="col-lg-8">
                <input class="form-control" name="subscribe_expressions[expression][]" placeholder="Type Expression, e.g : &#123;&#123;value&#125;&#125; > 10" required type="text">
            </div>
        </div>
        <div class="row mt-3">
            <label class="col-lg-4 col-form-label">Status Type</label>
            <div class="col-lg-8">
                <select class="form-control select2modify" data-placeholder="Select Status Type" name="subscribe_expressions[status_type][]">
                    <option></option>
                    @foreach ($status_types as $status_type)
                    <option value="{{ $status_type->id }}">{{ $status_type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="text-start mt-3">
            <button class="btn btn-danger" type="button" data-subscribe_expression="1" onclick="handleDeleteSubscribeExpression(this)">Delete</button>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const model = @json($data);
    const dataSubscribeExpressions = model.subscribe_expression;
    const dataPublishActions = model.publish_action;

    $(document).ready(function() {
        if (dataSubscribeExpressions.length > 0) {
            dataSubscribeExpressions.forEach((item, index) => {
                hanldeAddSubsribeExpression();
                $(`.subscribe_expression_${index + 1} input[name="subscribe_expressions[expression][]"]`).val(item.expression);
                $(`.subscribe_expression_${index + 1} select[name="subscribe_expressions[status_type][]"]`).val(item.status_type_id).change();
            })
        }
        if (dataPublishActions.length > 0) {
            dataPublishActions.forEach((item, index) => {
                handleAddPublishButtonAction();
                $(`.publish_button_${index + 1} input[name="publish_actions[label][]"]`).val(item.label);
                $(`.publish_button_${index + 1} input[name="publish_actions[value][]"]`).val(item.value);
            })
        }
    })

    let subscribeExpressionCount = 0;
    let publishActionsCount = 0;

    const hanldeAddSubsribeExpression = () => {
        const html = $('.subscribe_html').html();
        const htmlAppend = $(html).clone();

        // Add Number
        htmlAppend.find('.number').text(`${subscribeExpressionCount + 1}. `);
        htmlAppend.show();

        // Delete Logic
        htmlAppend.addClass(`subscribe_expression_${subscribeExpressionCount + 1}`);
        htmlAppend.find('.btn-danger').attr('data-subscribe_expression', subscribeExpressionCount + 1);

        // Count Logic
        subscribeExpressionCount++;

        // Select2
        htmlAppend.find('.select2modify').select2();

        $('.subscribe_expression').append(htmlAppend);
    }

    const handleDeleteSubscribeExpression = (el) => {
        const subscribeExpression = $(el).data('subscribe_expression');
        $(`.subscribe_expression_${subscribeExpression}`).remove();
        subscribeExpressionCount--;
    }

    const handleAddPublishButtonAction = () => {
        const html = $('.publish_html').html();
        const htmlAppend = $(html).clone();

        // Add Number
        htmlAppend.find('.number').text(`${publishActionsCount + 1}. `);
        htmlAppend.show();

        // Delete Logic
        htmlAppend.addClass(`publish_button_${publishActionsCount + 1}`);
        htmlAppend.find('.btn-danger').attr('data-publish_actions', publishActionsCount + 1);

        // Count Logic
        publishActionsCount++;

        // Select2
        htmlAppend.find('.select2modify').select2();

        $('.publish_button').append(htmlAppend);
    }

    const handleDeletePublishActions = (el) => {
        const publishActions = $(el).data('publish_actions');
        $(`.publish_button_${publishActions}`).remove();
        publishActionsCount--;
    }

</script>
@endpush
