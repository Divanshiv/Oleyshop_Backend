@extends('layouts.admin.app')

@section('title', translate('Update product'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/ai-sidebar.css">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/edit.png') }}" class="w--24" alt="">
                </span>
                <span>
                    {{ translate('product') }} {{ translate('update') }}
                </span>
            </h1>
        </div>

        <form action="{{ route('admin.product.update', [$product['id']]) }}" method="post" id="product_form"
            enctype="multipart/form-data" class="row g-2">
            @csrf

            @php
                $languages = Helpers::get_business_settings('language');
                $default_lang = Helpers::get_default_language();
            @endphp

            <input type="hidden" name="product_id" value="{{ $product['id'] }}">

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body pt-2">
                        @if ($languages && array_key_exists('code', $languages[0]))
                            <ul class="nav nav-tabs mb-4">
                                @foreach ($languages as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{ $lang['default'] == true ? 'active' : '' }}"
                                            href="#"
                                            id="{{ $lang['code'] }}-link">{{ Helpers::get_language_name($lang['code']) . '(' . strtoupper($lang['code']) . ')' }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            @foreach ($languages as $lang)
                             <?php
                                if (count($product['translations'])) {
                                    $translate = [];
                                    foreach ($product['translations'] as $t) {
                                        if ($t->locale == $lang['code'] && $t->key == 'name') {
                                            $translate[$lang['code']]['name'] = $t->value;
                                        }
                                        if ($t->locale == $lang['code'] && $t->key == 'description') {
                                            $translate[$lang['code']]['description'] = $t->value;
                                        }
                                    }
                                }
                                ?>
                                <div class="{{ $lang['default'] == false ? 'd-none' : '' }} lang_form" id="{{ $lang['code'] }}-form">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="input-label mb-0" for="{{ $lang['code'] }}_name">{{ translate('name') }}
                                                ({{ strtoupper($lang['code']) }})
                                                @if ($lang['code'] == 'en')
                                                    <span class="input-label-secondary text-danger">*</span>
                                                @endif
                                            </label>
                                            @if($aIStatus)
                                                <button type="button" class="btn bg-white text-primary bg-transparent shadow-none border-0 opacity-1 generate_btn_wrapper p-0 auto_fill_title"
                                                        id="title-{{ $lang['code'] }}-action-btn"
                                                        data-item='@json(["title" => $translate[$lang['code']]["name"] ?? $product["name"]])'
                                                        data-lang="{{ $lang['code'] }}"
                                                        data-route="{{ route('admin.product.title-auto-fill') }}">
                                                    <div class="btn-svg-wrapper">
                                                        <img width="18" height="18" class=""
                                                             src="{{ asset('public/assets/admin/img/ai/blink-right-small.svg') }}" alt="">
                                                    </div>
                                                    <span class="ai-text-animation d-none" role="status">
                                                        {{ translate('Just_a_second') }}
                                                    </span>
                                                    <span class="btn-text">{{ translate('Generate') }}</span>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="outline-wrapper" id="title-container-{{ $lang['code'] }}">
                                            <input type="text" name="name[]" id="{{ $lang['code'] }}_name"
                                                value="{{ $translate[$lang['code']]['name'] ?? $product['name'] }}"
                                                class="form-control" placeholder="{{ translate('New Product') }}"
                                                {{ $lang['code'] == $default_lang ? 'required' : '' }}>
                                        </div>

                                        @if ($lang['code'] == 'en')
                                            <span class="error-text d-flex justify-content-end fs-12px text-danger"
                                                data-error="name.0"></span>
                                        @endif
                                    </div>

                                    <input type="hidden" name="lang[]" value="{{ $lang['code'] }}">

                                    <div class="form-group mb-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="input-label mb-0"
                                            for="{{ $lang['code'] }}_hiddenArea">{{ translate('short') }}
                                            {{ translate('description') }} ({{ strtoupper($lang['code']) }})</label>

                                            @if($aIStatus)
                                                <button type="button" class="btn bg-white text-primary bg-transparent shadow-none border-0 opacity-1 generate_btn_wrapper p-0 auto_fill_description"
                                                        id="description-{{ $lang['code'] }}-action-btn" data-item='@json(["description" => $translate[$lang['code']]["description"] ?? $product["description"]])'
                                                        data-lang="{{ $lang['code'] }}"
                                                        data-route="{{ route('admin.product.description-auto-fill') }}">
                                                    <div class="btn-svg-wrapper">
                                                        <img width="18" height="18" class=""
                                                             src="{{ asset('public/assets/admin/img/ai/blink-right-small.svg') }}" alt="">
                                                    </div>
                                                    <span class="ai-text-animation d-none" role="status">
                                                    {{ translate('Just_a_second') }}
                                                </span>
                                                    <span class="btn-text">{{ translate('Generate') }}</span>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="outline-wrapper" id="editor-container-{{ $lang['code']  }}">
                                             <textarea name="description[]" class="form-control h--172px summernote" id="{{ $lang['code'] }}_hiddenArea">{{ $translate[$lang['code']]['description'] ?? $product['description'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div id="{{ $default_lang }}-form">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="input-label mb-0" for="exampleFormControlInput1">{{ translate('name') }}(EN)</label>

                                        @if($aIStatus)
                                            <button type="button" class="btn bg-white text-primary bg-transparent shadow-none border-0 opacity-1 generate_btn_wrapper p-0 auto_fill_description"   id=""  data-lang="" data-route="">
                                                <div class="btn-svg-wrapper">
                                                    <img width="18" height="18" class=""
                                                         src="{{ asset('public/assets/admin/img/ai/blink-right-small.svg') }}" alt="">
                                                </div>
                                                <span class="ai-text-animation d-none" role="status">
                                                {{ translate('Just_a_second') }}
                                            </span>
                                                <span class="btn-text">{{ translate('Generate') }}</span>
                                            </button>
                                        @endif
                                    </div>

                                    <div class="outline-wrapper title-container">
                                        <input type="text" name="name[]" value="{{ $product['name'] }}"
                                        class="form-control" placeholder="{{ translate('New Product') }}">
                                    </div>
                                </div>

                                <input type="hidden" name="lang[]" value="en">

                                <div class="form-group mb-0">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="input-label mb-0" for="hiddenArea">{{ translate('short') }}
                                            {{ translate('description') }} (EN)</label>

                                        @if($aIStatus)
                                            <button type="button" class="btn bg-white text-primary bg-transparent shadow-none border-0 opacity-1 generate_btn_wrapper p-0 auto_fill_description"  id=""  data-lang="" data-route="">
                                                <div class="btn-svg-wrapper">
                                                    <img width="18" height="18" class=""
                                                         src="{{ asset('public/assets/admin/img/ai/blink-right-small.svg') }}" alt="">
                                                </div>
                                                <span class="ai-text-animation d-none" role="status">
                                                {{ translate('Just_a_second') }}
                                            </span>
                                                <span class="btn-text">{{ translate('Generate') }}</span>
                                            </button>
                                        @endif

                                    </div>
                                    <div class="outline-wrapper description-container">
                                       <textarea name="description[]" class="form-control h--172px summernote" id="hiddenArea">{{ $product['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="d-flex flex-column gap-3 h-100">
                    <div class="card min-h-116px">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="d-flex flex-wrap-reverse justify-content-between">
                                <div class="w-200 flex-grow-1 mr-3">
                                    {{ translate('Turning Visibility off will not show this product in the user app and website') }}
                                </div>
                                <div class="d-flex align-items-center mb-2 mb-sm-0">
                                    <h5 class="mb-0 mr-2">{{ translate('Visibility') }}</h5>
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox" class="toggle-switch-input" name="status" value="1"
                                            checked>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="category_wrapper h-100">
                        <div class="outline-wrapper">
                            <div class="card bg-animate">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <span class="card-header-icon">
                                            <i class="tio-user"></i>
                                        </span>
                                        <span>
                                            {{ translate('category') }}
                                        </span>
                                    </h5>
                                        @if($aIStatus)
                                            <button type="button"
                                                    class="btn bg-white text-primary bg-transparent shadow-none border-0 opacity-1 generate_btn_wrapper p-0 category_setup_auto_fill"
                                                    data-route="{{ route('admin.product.category-setup-auto-fill') }}" data-item=""  data-lang="en">
                                                <div class="btn-svg-wrapper">
                                                    <img width="18" height="18" class=""
                                                         src="{{ asset('public/assets/admin/img/ai/blink-right-small.svg') }}" alt="">
                                                </div>
                                                <span class="ai-text-animation d-none" role="status">
                                                    {{ translate('Just_a_second') }}
                                                </span>
                                                <span class="btn-text">{{ translate('Generate') }}</span>
                                            </button>
                                        @endif
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="input-label" for="category_id">{{ translate('category') }}<span
                                                        class="input-label-secondary text-danger">*</span></label>

                                                <select name="category_id" id="category_id" class="form-control js-select2-custom"
                                                    onchange="getRequest('{{ url('/') }}/admin/product/get-categories?parent_id='+this.value,'sub-categories')">
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category['id'] }}"
                                                            {{ $category->id == $productCategory[0]->id ? 'selected' : '' }}>
                                                            {{ $category['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="input-label" for="sub-categories">{{ translate('sub_category') }}<span
                                                        class="input-label-secondary"></span></label>

                                                <select name="sub_category_id" id="sub-categories"
                                                    data-id="{{ count($productCategory) >= 2 ? $productCategory[1]->id : '' }}"
                                                    class="form-control js-select2-custom"
                                                    onchange="getRequest('{{ url('/') }}/admin/product/get-categories?parent_id='+this.value,'sub-sub-categories')">

                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="input-label" for="unit">{{ translate('unit') }}</label>

                                                <select name="unit" id="unit" class="form-control js-select2-custom">
                                                    <option value="kg" {{ $product['unit'] == 'kg' ? 'selected' : '' }}>{{ translate('kg') }}</option>
                                                    <option value="gm" {{ $product['unit'] == 'gm' ? 'selected' : '' }}>{{ translate('gm') }}</option>
                                                    <option value="ltr" {{ $product['unit'] == 'ltr' ? 'selected' : '' }}>{{ translate('ltr') }}</option>
                                                    <option value="pc" {{ $product['unit'] == 'pc' ? 'selected' : '' }}>{{ translate('pc') }}</option>
                                                    <option value="ml" {{ $product['unit'] == 'ml' ? 'selected' : '' }}>{{ translate('ml') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="input-label" for="capacity">{{ translate('capacity') }}</label>

                                                <input type="number" min="0" step="0.01"
                                                    value="{{ $product['capacity'] }}" name="capacity" id="capacity"
                                                    class="form-control" placeholder="{{ translate('Ex : 5') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="input-label"
                                                    for="maximum_order_quantity">{{ translate('maximum_order_quantity') }}</label>

                                                <input type="number" min="1" step="1"
                                                    value="{{ $product['maximum_order_quantity'] }}" name="maximum_order_quantity"
                                                    id="maximum_order_quantity" class="form-control"
                                                    placeholder="{{ translate('Ex : 3') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="input-label" for="weight">{{ translate('weight') }}
                                                    <span>({{ Helpers::get_business_settings('product_weight_unit') }})</span>
                                                </label>

                                                <input type="number" min="0.00" step=".00"
                                                    value="{{ $product['weight'] }}" name="weight" id="weight"
                                                    class="form-control" placeholder="{{ translate('Ex : 1') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ translate('product') }} {{ translate('image') }} <small
                                class="text-danger">* ( {{ translate('ratio') }} 1:1 )</small>
                            <p class="fs-10 m-0">
                                {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} : Max {{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}
                            </p>
                        </h5>
                        <div class="d-flex flex-column">
                            <div class="position-relative">
                                <div class="multi_image_picker d-flex gap-20 pt-4"
                                        data-ratio="1/1"
                                        data-field-name="images[]"
                                        data-max-count="4"
                                        data-total-max-size="{{ \App\CentralLogics\convertToReadableSize(\App\CentralLogics\convertToBytes(ini_get('post_max_size'))) }}"
                                        data-max-filesize="{{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}"
                                >

                                    <div>
                                        <div class="imageSlide_prev">
                                            <div
                                                class="d-flex justify-content-center align-items-center h-100">
                                                <button
                                                    type="button"
                                                    class="btn btn-circle border-0 bg-primary text-white">
                                                    <i class="tio-back-ui"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="imageSlide_next">
                                            <div
                                                class="d-flex justify-content-center align-items-center h-100">
                                                <button
                                                    type="button"
                                                    class="btn btn-circle border-0 bg-primary text-white">
                                                    <i class="tio-next-ui"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    @if (!empty(json_decode($product->image, true)))
                                        @foreach ($product->identityImageFullPath as $index => $identification_image)
                                            <div class="upload-file m-0 spartan--item--wrapper"  data-spartanindexrow="{{ $index + 100 }}">
                                                <a class="remove_btn btn btn-danger btn-circle"
                                                    style="--size: 20px;"
                                                    href="{{ route('admin.product.remove-image', [$product->id, $index]) }}"
                                                    data-spartanindexremove="{{ $index + 100 }}"
                                                >
                                                    <i class="tio-clear"></i>
                                                </a>
                                                <label class="upload-file__wrapper ratio-1-1">
                                                    <img class="upload-file-img" loading="lazy"
                                                        src="{{ $identification_image }}"
                                                        data-default-src="{{ $identification_image }}"
                                                        alt=""
                                                        data-spartanindeximage="{{ $index + 100 }}"
                                                    >
                                                    <input type="hidden" name="existing_identity_images[]"
                                                            value="{{ $identification_image }}">
                                                </label>
                                                <div class="overlay">
                                                    <div class="d-flex gap-10px justify-content-center align-items-center h-100">
                                                        <button type="button" class="btn btn-info text-info bg-white icon-btn view_btn">
                                                            <i class="tio-visible-outlined"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <span class="error-text d-flex justify-content-start fs-12px text-danger mt-2" data-error="images"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="price_wrapper h-100">
                    <div class="outline-wrapper  h-100">
                        <div class="card h-100 bg-animate">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="card-header-icon">
                                        <i class="tio-dollar"></i>
                                    </span>
                                    <span>
                                        {{ translate('price_information') }}
                                    </span>
                                </h5>
                                @if($aIStatus)
                                    <button type="button"
                                            class="btn bg-white text-primary bg-transparent shadow-none border-0 opacity-1 generate_btn_wrapper p-0 price_others_auto_fill"
                                            id="price_others_auto_fill"
                                            data-route="{{ route('admin.product.price-others-auto-fill') }}" data-lang="en">
                                        <div class="btn-svg-wrapper">
                                            <img width="18" height="18" class=""
                                                 src="{{ asset('public/assets/admin/img/ai/blink-right-small.svg') }}"
                                                 alt="">
                                        </div>
                                        <span class="ai-text-animation d-none" role="status">
                                        {{ translate('Just_a_second') }}
                                    </span>
                                        <span class="btn-text">{{ translate('Generate') }}</span>
                                    </button>
                                @endif
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="price">{{ translate('default_unit_price') }}<span
                                                    class="input-label-secondary text-danger">*</span></label>

                                            <input type="number" value="{{ $product['price'] }}" min="0"
                                                max="100000000" name="price" id="price" class="form-control"
                                                step="any" placeholder="{{ translate('Ex : 100') }}">

                                            <span class="error-text d-flex justify-content-end fs-12px text-danger"
                                                data-error="price"></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="total_stock">{{ translate('stock') }}<span
                                                    class="input-label-secondary text-danger">*</span></label>

                                            <input type="number" min="1" max="100000000"
                                                value="{{ $product['total_stock'] }}" name="total_stock" id="total_stock"
                                                class="form-control" placeholder="{{ translate('Ex : 100') }}">

                                            <span class="error-text d-flex justify-content-end fs-12px text-danger"
                                                data-error="total_stock"></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="discount_type">{{ translate('discount') }}
                                                {{ translate('type') }}</label>

                                            <select name="discount_type" id="discount_type"
                                                class="form-control js-select2-custom">
                                                <option value="percent"
                                                    {{ $product['discount_type'] == 'percent' ? 'selected' : '' }}>
                                                    {{ translate('percent') }}
                                                </option>
                                                <option value="amount"
                                                    {{ $product['discount_type'] == 'amount' ? 'selected' : '' }}>
                                                    {{ translate('amount') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="discount">{{ translate('discount') }}
                                                <span
                                                    id="discount_symbol">{{ $product['discount_type'] == 'amount' ? '' : '(%)' }}</span></label>

                                            <input type="number" min="0" value="{{ $product['discount'] }}"
                                                max="100000" name="discount" id="discount" class="form-control"
                                                step="any" placeholder="{{ translate('Ex : 100') }}">

                                            <span class="error-text d-flex justify-content-end fs-12px text-danger"
                                                data-error="discount"></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="tax_type">{{ translate('tax_type') }}</label>
                                            <select name="tax_type" id="tax_type" class="form-control js-select2-custom">
                                                <option value="percent"
                                                    {{ $product['tax_type'] == 'percent' ? 'selected' : '' }}>
                                                    {{ translate('percent') }}
                                                </option>
                                                <option value="amount"
                                                    {{ $product['tax_type'] == 'amount' ? 'selected' : '' }}>
                                                    {{ translate('amount') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="tax">{{ translate('tax_rate') }} <span
                                                    id="tax_symbol">{{ $product['tax_type'] == 'amount' ? '' : '(%)' }}</span></label>

                                            <input type="number" value="{{ $product['tax'] }}" min="0"
                                                max="100000" name="tax" id="tax" class="form-control"
                                                step="any" placeholder="{{ translate('Ex : 7') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="variation_wrapper h-100">
                    <div class="outline-wrapper h-100">
                        <div class="card h-100 bg-animate">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="card-header-icon">
                                        <i class="tio-label"></i>
                                    </span>
                                    <span>
                                        {{ translate('Tags_&_Attribute') }}
                                    </span>
                                </h5>
                                @if($aIStatus)
                                    <button type="button"
                                            class="btn bg-white text-primary bg-transparent shadow-none border-0 opacity-1 generate_btn_wrapper p-0 variation_tag_setup_auto_fill"
                                            data-route="{{ route('admin.product.variation-tag-setup-auto-fill') }}"
                                            data-lang="en">
                                        <div class="btn-svg-wrapper">
                                            <img width="18" height="18" class=""
                                                 src="{{ asset('public/assets/admin/img/ai/blink-right-small.svg') }}"
                                                 alt="">
                                        </div>
                                        <span class="ai-text-animation d-none" role="status">
                                        {{ translate('Just_a_second') }}
                                    </span>
                                        <span class="btn-text">{{ translate('Generate') }}</span>
                                    </button>
                                @endif
                            </div>
                            <div class="card-body pb-0">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="tags">{{ translate('Enter_Tag') }}</label>
                                           <input type="text" class="form-control" name="tags"
                                                placeholder="Enter tags"
                                                value="@foreach ($product->tags as $c) {{ $c->tag . ',' }} @endforeach"
                                                data-role="tagsinput">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group __select-attr mb-0">
                                            <label class="input-label"
                                                for="choice_attributes">{{ translate('select attribute') }}<span
                                                    class="input-label-secondary"></span></label>

                                            <select name="attribute_id[]" id="choice_attributes"
                                                class="form-control js-select2-custom" multiple="multiple">
                                                @foreach (\App\Model\Attribute::orderBy('name')->get() as $attribute)
                                                    <option value="{{ $attribute['id'] }}"
                                                        {{ in_array($attribute->id, json_decode($product['attributes'], true)) ? 'selected' : '' }}>
                                                        {{ $attribute['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mt-2 mb-2">
                                                <div class="customer_choice_options" id="customer_choice_options">
                                                    @include('admin-views.product.partials._choices', [
                                                        'choice_no' => json_decode($product['attributes']),
                                                        'choice_options' => json_decode($product['choice_options'], true),
                                                    ])
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-2 mb-2">
                                                <div class="variant_combination" id="variant_combination">
                                                    @include('admin-views.product.partials._edit-combinations', [
                                                        'combinations' => json_decode($product['variations'], true),
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="btn--container justify-content-end">
                    <a href="" class="btn btn--reset min-w-120px">{{ translate('reset') }}</a>
                    <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                </div>
            </div>
        </form>
    </div>

    @if($aIStatus)
        @include('admin-views.product.partials._ai-sidebar')
    @endif

    <span class="data-to-js"
          data-variant-combination-route="{{ route('admin.product.variant-combination') }}"
    >
    </span>
@endsection


@push('script_2')
    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker-min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="{{ asset('public/assets/admin/js/ai/ai-sidebar.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/ai/ai-common.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/multiple-image-upload.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200
            });
        });
    </script>

    <script>
        $(".lang_link").click(function(e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == 'en') {
                $("#from_part_2").removeClass('d-none');
            } else {
                $("#from_part_2").addClass('d-none');
            }


        })
    </script>

    <script>
        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function(data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        $(document).ready(function() {
            setTimeout(function() {
                let category = $("#category_id").val();
                let sub_category = '{{ count($productCategory) >= 2 ? $productCategory[1]->id : '' }}';
                let sub_sub_category =
                    '{{ count($productCategory) >= 3 ? $productCategory[2]->id : '' }}';
                getRequest('{{ url('/') }}/admin/product/get-categories?parent_id=' + category +
                    '&&sub_category=' + sub_category, 'sub-categories');
                getRequest('{{ url('/') }}/admin/product/get-categories?parent_id=' + sub_category +
                    '&&sub_category=' + sub_sub_category, 'sub-sub-categories');
            }, 1000)
        });
    </script>

    <script>
        $(document).on('ready', function() {
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

    <script src="{{ asset('public/assets/admin/js/tags-input.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/product/load-variation.js') }}"></script>
    <script>
        submitByAjax('#product_form', {
            hasEditors: true,
            languages: @json($languages ?? []),
            successMessage: '{{ translate('product uploaded successfully!') }}',
            redirectUrl: '{{ route('admin.product.list') }}'
        });

        $('#discount_type').change(function() {
            if ($('#discount_type').val() == 'percent') {
                $("#discount_symbol").html('(%)')
            } else {
                $("#discount_symbol").html('')
            }
        });

        $('#tax_type').change(function() {
            if ($('#tax_type').val() == 'percent') {
                $("#tax_symbol").html('(%)')
            } else {
                $("#tax_symbol").html('')
            }
        });
    </script>

    <script src="{{ asset('public/assets/admin/js/ai/product/autofill-title.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/ai/product/autofill-description.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/ai/product/autofill-category-setup.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/ai/product/autofill-price-others.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/ai/product/autofill-variation-tag-setup.js') }}"></script>
@endpush
