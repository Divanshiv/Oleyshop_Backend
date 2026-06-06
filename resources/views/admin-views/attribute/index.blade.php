@extends('layouts.admin.app')

@section('title', translate('Add new attribute'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/attribute.png') }}" class="w--24"
                        alt="{{ translate('attribute') }}">
                </span>
                <span>
                    {{ translate('Attribute Setup') }}
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card--header order-top">
                <div class="d-flex gap-2 align-items-center">
                    <h5 class="mb-0"> {{ translate('Attribute Table') }}
                        <span class="badge badge-soft-dark rounded-pill fs-10 ml-1">{{ $attributes->total() }}</span>
                    </h5>
                </div>

                <div class="d-flex flex-sm-nowrap flex-wrap gap-sm-3 gap-3">
                    <form action="{{ request()->url() }}" method="GET">
                        @foreach (request()->except('search', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                                placeholder="{{ translate('Search by Name') }}" aria-label="Search"
                                value="{{ $search }}" autocomplete="off">

                            <div class="input-group-append h-30">
                                <button type="submit" class="input-group-text title-bg3 p-2 text-white">
                                    <i class="tio-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <button
                        class="btn d-inline-flex h-30 align-items-center gap-1 px-xxl-3 px-xl-3 px-2 py-2 btn--primary fs-13"
                        data-toggle="modal" data-target="#attribute-modal">
                        <i class="tio-add"></i>
                        <span class="d-lg-inline d-none text-nowrap fs-13">{{ translate('add_attribute') }}</span>
                    </button>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table
                    class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('#') }}</th>
                            <th>{{ translate('name') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attributes as $key => $attribute)
                            <tr>
                                <td>{{ $attributes->firstItem() + $key }}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body text-trim-70">
                                        {{ $attribute['name'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn"
                                            href="{{ route('admin.attribute.edit', [$attribute['id']]) }}">
                                            <i class="tio-edit"></i></a>
                                        <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                            data-id="attribute-{{ $attribute['id'] }}"
                                            data-message="{{ translate('Want to delete this') }}">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{ route('admin.attribute.delete', [$attribute['id']]) }}" method="post"
                                        id="attribute-{{ $attribute['id'] }}">
                                        @csrf @method('delete')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="">
                    {!! $attributes->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
                </div>

                @if (count($attributes) == 0)
                    <div class="text-center p-4">
                        <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                            alt="{{ translate('image') }}">
                        <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="attribute-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.attribute.store') }}" method="post" id="attribute_form">
                    <div class="modal-body pt-3">
                        @csrf

                        @php
                            $languages = Helpers::get_business_settings('language');
                            $default_lang = Helpers::get_default_language();
                        @endphp

                        @if ($languages && array_key_exists('code', $languages[0]))
                            <ul class="nav nav-tabs mb-4">
                                @foreach ($languages as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{ $lang['default'] == true ? 'active' : '' }}"
                                            href="#" id="{{ $lang['code'] }}-link">
                                            {{ Helpers::get_language_name($lang['code']) . '(' . strtoupper($lang['code']) . ')' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="row">
                                <div class="col-12">
                                    @foreach ($languages as $lang)
                                        <div class="form-group lang_form {{ $lang['default'] == false ? 'd-none' : '' }}"
                                            id="{{ $lang['code'] }}-form">

                                            <label class="input-label" for="name-{{ $lang['code'] }}">
                                                {{ translate('name') }} ({{ strtoupper($lang['code']) }})
                                                @if ($lang['code'] == 'en')
                                                    <span class="input-label-secondary text-danger">*</span>
                                                @endif
                                            </label>

                                            <input type="text" name="name[]" id="name-{{ $lang['code'] }}"
                                                class="form-control" placeholder="{{ translate('New Attribute') }}"
                                                maxlength="255">

                                            @if ($lang['code'] == 'en')
                                                <span class="error-text d-flex justify-content-end fs-12px text-danger"
                                                    data-error="name.0"></span>
                                            @endif
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang['code'] }}">
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group lang_form" id="{{ $default_lang }}-form">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('name') }}
                                            ({{ strtoupper($default_lang) }})</label>
                                        <input type="text" name="name[]" class="form-control"
                                            placeholder="{{ translate('New attribute') }}" maxlength="255">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                                </div>
                            </div>
                        @endif

                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset"
                                data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";

        $(".lang_link").click(function(e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{ $default_lang }}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });

        submitByAjax('#attribute_form', {
            hasEditors: false,
            languages: @json($languages ?? []),
            successMessage: '{{ translate('Attribute added successfully!') }}',
            redirectUrl: '{{ route('admin.attribute.add-new') }}'
        });
    </script>
@endpush
