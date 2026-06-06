@extends('layouts.admin.app')

@section('title', translate('Update category'))

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/category.png') }}" class="w--24"
                        alt="{{ translate('category') }}">
                </span>
                <span>
                    @if ($category->parent_id == 0)
                        {{ translate('category Update') }}
                    @else
                        {{ translate('Sub Category Update') }}
                    @endif
                </span>
            </h1>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.category.update', [$category['id']]) }}" method="post"
                    enctype="multipart/form-data" id="category_form">
                    @csrf

                    @php
                        $languages = Helpers::get_business_settings('language');
                        $defaultLanguage = Helpers::get_default_language();
                    @endphp

                    <div class="row justify-content-between g-4">
                        <div class="col-xxl-8 col-xl-8 col-lg-8 col-sm-7">
                            <div class="bg-light rounded p-4 h-100">
                                @if ($languages && array_key_exists('code', $languages[0]))
                                    <ul
                                        class="nav nav-tabs d-inline-flex mb-sm-5 mb-4 {{ $category->parent_id == 0 ?: 'mb-4' }}">
                                        @foreach ($languages as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link {{ $lang['default'] == true ? 'active' : '' }}"
                                                    href="#"
                                                    id="{{ $lang['code'] }}-link">{{ Helpers::get_language_name($lang['code']) . '(' . strtoupper($lang['code']) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if ($languages && array_key_exists('code', $languages[0]))
                                    @foreach ($languages as $lang)
                                        <?php
                                        if (count($category['translations'])) {
                                            $translate = [];
                                            foreach ($category['translations'] as $t) {
                                                if ($t->locale == $lang['code'] && $t->key == 'name') {
                                                    $translate[$lang['code']]['name'] = $t->value;
                                                }
                                            }
                                        }
                                        ?>

                                        <div class=" {{ $lang['default'] == false ? 'd-none' : '' }} lang_form"
                                            id="{{ $lang['code'] }}-form">
                                            <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('name') }}
                                                ({{ strtoupper($lang['code']) }})
                                                @if ($lang['code'] == 'en')
                                                    <span class="input-label-secondary text-danger">*</span>
                                                @endif
                                            </label>

                                            <input type="text" name="name[]" maxlength="255"
                                                value="{{ $lang['code'] == 'en' ? $category['name'] : $translate[$lang['code']]['name'] ?? '' }}"
                                                class="form-control"
                                                @if ($lang['status'] == true) oninvalid="document.getElementById('{{ $lang['code'] }}-link').click()" @endif
                                                placeholder="{{ translate('New Category') }}">

                                            @if ($lang['code'] == 'en')
                                                <span class="error-text d-flex justify-content-end fs-12px text-danger"
                                                    data-error="name.0"></span>
                                            @endif
                                        </div>

                                        <input type="hidden" name="lang[]" value="{{ $lang['code'] }}">
                                    @endforeach
                                @else
                                    <div class="col-sm-6 lang_form" id="{{ $defaultLanguage }}-form">
                                        <label class="input-label" for="default_category_name">{{ translate('name') }}
                                            ({{ strtoupper($defaultLanguage) }})</label>

                                        <input type="text" name="name[]" id="default_category_name"
                                            value="{{ $category['name'] }}" class="form-control"
                                            oninvalid="document.getElementById('en-link').click()"
                                            placeholder="{{ translate('New Category') }}">
                                    </div>

                                    <input type="hidden" name="lang[]" value="{{ $defaultLanguage }}">
                                @endif

                                <input name="position" value="0" hidden>
                            </div>
                        </div>

                        @if ($category->parent_id == 0)
                            <div class="col-xxl-4 col-xl-4 col-lg-4 col-sm-5">
                                <div class="bg-light rounded p-xxl-4 p-3">
                                    <div class="d-flex flex-column justify-content-center h-100">
                                        <h5 class="text-center mb-3 text--title text-capitalize">
                                            {{ translate('Upload  Image') }}
                                        </h5>
                                        <label class="upload--vertical ratio-2-2 max-w-100 w-auto h-200">
                                            <input type="file" name="image" id="customFileEg1" class=""
                                                   accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                                   hidden
                                                   data-maxFileSize="{{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}">
                                            <img id="viewer" class="img-viewer" src="{{ $category->imageFullPath }}"
                                                alt="banner image" />
                                        </label>
                                    </div>

                                    <p class="fs-10 m-0 text-center mt-3">
                                        {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} : Max {{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}
                                        <span class="text-dark font-semibold">(1:1)</span>
                                    </p>

                                    <span class="error-text d-flex justify-content-center fs-12px text-danger"
                                        data-error="image"></span>
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/category.js') }}"></script>
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
            if (lang == '{{ $defaultLanguage }}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });

        submitByAjax('#category_form', {
            hasEditors: false,
            languages: @json($languages ?? []),
            successMessage: '{{ $category->parent_id == 0 ? translate('Category updated successfully!') : translate('Sub category updated successfully!') }}',
            redirectUrl: '{{ $category->parent_id == 0 ? route('admin.category.add') : route('admin.category.add-sub-category') }}'
        });
    </script>
@endpush
