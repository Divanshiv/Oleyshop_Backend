@extends('layouts.admin.app')

@section('title', translate('Cancellation policy'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header pb-2">
            @include('admin-views.business-settings.partial.page-setup-menu')
        </div>
        <div class="row align-items-center mb-3">
            <div class="col-sm mb-2 mb-sm-0">
                <h5 class="d-flex flex-wrap justify-content-end">
                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('on') }}</span>
                        <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('Status') }}</span>
                        <input type="checkbox"
                               data-route="{{ route('admin.business-settings.page-setup.cancellation-policy.status', [$status['value'] ? 0 : 1]) }}"
                               data-message="{{ $status['value']? translate('you want to disable this page'): translate('you want to active this page') }}"
                               class="toggle-switch-input status-change-alert" id="stocksCheckbox"
                            {{ $status['value'] ? 'checked' : '' }}>
                        <span class="toggle-switch-label text">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                    </label>
                </h5>
            </div>
        </div>
        <form action="{{route('admin.business-settings.page-setup.cancellation-policy')}}" method="post" id="cancellation_policy-form" enctype="multipart/form-data">
            @csrf
            @include('admin-views.business-settings.page-setup._background-image', compact('data'))
            <div class="card card-body">
                <div class="bg-section rounded-10 p-3 p-sm-4">
                    <div class="form-group mb-0">
                        <label class="input-label" for="">{{ translate('Page_Description') }}</label>
                        <div id="editor" class="min-h-116px">{!! json_decode($data['value'], true)['description'] ?? $data['value'] ?? null !!}</div>
                        <textarea name="description" id="hiddenArea" style="display:none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="btn--container justify-content-end mt-4">
                <button type="reset" class="btn btn--reset" id="reset">{{translate('reset')}}</button>
                <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
            </div>
        </form>
    </div>

    @include('admin-views.partials._image-modal')

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/quill-editor.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var bn_quill = new Quill('#editor', {
                theme: 'snow'
            });

            $('#cancellation_policy-form').on('submit', function () {
                var myEditor = document.querySelector('#editor');
                $('#hiddenArea').val(myEditor.children[0].innerHTML);
            });
        });
        $('#reset').click(function() {
            location.reload();
        });
    </script>
@endpush
