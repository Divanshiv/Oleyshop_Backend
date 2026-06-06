@extends('layouts.admin.app')

@section('title', translate('Dashboard'))

@section('content')
    <div class="content container-fluid">
        <h1>Components</h1>


        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <h4>Export Btn</h4>
                        <!-- Copy Export Btn Code -->
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker export_btn h-30 text-dark btn btn-sm dropdown-toggle min-height-30"
                                href="javascript:;"
                                data-hs-unfold-options='{
                                    "target": "#usersExportDropdown",
                                    "type": "css-animation"
                                }'>
                                <i class="tio-download-to title-clr3 top-02"></i>
                                {{ translate('export') }}
                                <i class="tio-down-ui fs-10 title-clr3"></i>
                            </a>

                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('download') }}
                                    {{ translate('options') }}</span>
                                <a id="export-excel" class="dropdown-item" href="#0">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="{{ translate('Image Description') }}">
                                    {{ translate('excel') }}
                                </a>
                            </div>
                        </div>
                        <!-- Export Btn End -->
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <!-- Copy Code -->
                        <a class="action-btn" href="#0">
                            <i class="tio-invisible"></i>
                        </a>
                        <!-- End -->
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <h4>Add Button</h4>
                        <!-- Copy add button Code -->
                        <a href="#0" type="button" class="btn btn--primary">
                            <i class="tio-add"></i>Add New Method
                        </a>
                        <!-- End -->
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <h4>Add Button Remove text android</h4>
                        <!-- Copy add button Code -->
                        <a href="#0" type="button"
                            class="btn d-inline-flex h-30 align-items-center gap-1 px-xxl-3 px-xl-3 px-2 py-2 btn--primary fs-13">
                            <i class="tio-add"></i>
                            <span class="d-lg-inline d-none text-nowrap fs-13">Add Lorem</span>
                        </a>
                        <!-- End -->
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a class="action-btn" href="#0">
                            <i class="tio-edit"></i>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <button class="action-btn btn--danger btn-outline-danger">
                            <i class="tio-delete-outlined"></i>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a class="action-btn btn--primary btn-outline-primary" href="#0">
                            <i class="tio-invisible"></i>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a class="action-btn btn-outline-primary-2" href="#0">
                            <i class="tio-print"></i>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a class="action-btn btn-outline-success-light" href="#0">
                            <i class="tio-done"></i>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a class="action-btn btn-outline-success-dark" href="#0">
                            <i class="tio-add"></i>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a class="action-btn btn--warning btn-outline-warning" href="#">
                            <i class="tio-invisible"></i>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <span class="badge text-nowrap badge-soft-primary-dark">
                            Packaging
                        </span>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center gap-2">
                            <select name="data_counting_select" class="custom-select w-auto custom-select-small h-25px">
                                <option value="20 Items">20 Items</option>
                                <option value="10 Items">10 Items</option>
                            </select>
                            <p class="text-record fs-12px m-0">Showing 1 To 20 Of 100 Records</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <form action="#0" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control h-40"
                                    placeholder="{{ translate('Search Here...') }}" aria-label="Search" value=""
                                    required autocomplete="off">
                                <div class="input-group-append h-30">
                                    <button type="submit" class="input-group-text title-bg3 p-2 text-white">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="table-order-data d-flex align-items-center flex-wrap">
                            <div class="table-data-badge d-flex align-items-center gap-2">
                                <div class="dot-custom style-1"></div>
                                <h5 class="text-dark fs-14 font-medium m-0">Total Order</h5>
                            </div>
                            <div class="table-data-badge d-flex align-items-center gap-2">
                                <div class="dot-custom style-2"></div>
                                <h5 class="text-dark fs-14 font-medium m-0">VAT / Tax</h5>
                            </div>
                            <div class="table-data-badge d-flex align-items-center gap-2">
                                <div class="dot-custom style-3"></div>
                                <h5 class="text-dark fs-14 font-medium m-0">Delivery Charge</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">

                    </div>
                    <div class="col-md-6 col-lg-3">

                    </div>
                    <div class="col-md-6 col-lg-3">

                    </div>
                    <div class="col-md-6 col-lg-3">

                    </div>
                    <div class="col-md-6 col-lg-3">

                    </div>
                    <div class="col-md-6 col-lg-3">

                    </div>
                    <div class="col-md-6 col-lg-3">

                    </div>
                </div>

            </div>
        </div>



        <h1>Check Validation</h1>
        <form action="" class="fnd-validation">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-8 col-md-7">
                            <div class="d-flex flex-column gap-24px">
                                <ul class="nav nav-tabs border-0">
                                    <li class="nav-item">
                                        <a class="nav-link lang_link active" href="#0"
                                            id="default-link">Default(EN)</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link lang_link" href="#0" id="ar-link">Arabic -
                                            العربية(AR)</a>
                                    </li>
                                </ul>
                                <div class="lang_form" id="default-form">
                                    <div class="d-flex flex-column gap-24px">
                                        <div class="form-group mb-0">
                                            <label class="input-label d-flex align-items-center gap-1">
                                                User name <span class="text-danger">*</span>
                                                <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                                    title="{{ translate('Content Need') }}">
                                                </i>
                                            </label>
                                            <input type="text" name="name" class="form-control"
                                                placeholder="User Name" required="">
                                            <span class="typing-error">Please enter your first name.</span>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="input-label d-flex align-items-center gap-1">
                                                Email Address <span class="text-danger">*</span>
                                                <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                                    title="{{ translate('Content Need') }}">
                                                </i>
                                            </label>
                                            <input type="text" name="email" class="form-control"
                                                placeholder="Type Your Mail" required="">
                                            <span class="typing-error">Please enter a valid email</span>
                                        </div>
                                        <div class="form-group counting-character-item mb-0">
                                            <div
                                                class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
                                                <label class="input-label d-flex align-items-center gap-1 mb-0">
                                                    Description
                                                    <i class="tio-info text-muted" data-toggle="tooltip"
                                                        data-placement="top" title="{{ translate('Content Need') }}">
                                                    </i>
                                                </label>
                                                <label class="toggle-switch my-0">
                                                    <input type="checkbox" class="toggle-switch-input" checked="">
                                                    <span class="toggle-switch-label mx-auto text">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </div>
                                            <textarea type="text" name="bio" rows="1" class="form-control" maxlength="100"
                                                placeholder="Write Description" required=""></textarea>
                                            <span class="counting-character text-right mt-1 d-block">0/100</span>
                                            <span class="typing-error">Typing something</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="lang_form d-none" id="ar-form">
                                    <div class="d-flex flex-column gap-24px"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-5">
                            <div class="upload-image-group bg-light rounded p-lg-4 p-3 h-100">
                                <div class="d-flex flex-column justify-content-center h-100">
                                    <div class="text-center text-dark mb-3 text--title">
                                        Upload Image
                                    </div>
                                    <label class="upload--squire ratio-2-1">
                                        <input type="file" name="image" id="customFileEg1"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" hidden="">
                                        <img id="viewer" src="{{ asset('public/assets/admin') }}/img/add-image.png"
                                            alt="Branch image">
                                    </label>
                                    <span class="fs-10 text-center mt-3">JPG, JPEG, PNG Image size : Max 5 MB
                                        <strong>(1:1)</strong></span>
                                    <span class="typing-error justify-content-center">File Size is larger</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="input-label">Phone <span class="text-danger">*</span></label>
                                <input type="phone" name="phone" class="form-control" placeholder="EX : +09853834"
                                    required="">
                                <span class="typing-error">Please enter a valid number</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="input-label">Selection</label>
                                <select name="brand_selection" class="custom-select">
                                    <option value="" selected disabled>Select Brand</option>
                                    <option value="all">All Brand</option>
                                    <option value="1">examle 01</option>
                                    <option value="2">examle 02</option>
                                    <option value="3">examle 03</option>
                                </select>
                                <span class="typing-error">Select Brand</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0 without-validation">
                                <label class="input-label">Selection</label>
                                <div class="d-flex align-items-center gap-0 border rounded">
                                    <input type="text" name="time-select" placeholder="Select from dropdown"
                                        class="from-control px-3 w-100 border-0 rounded-0">
                                    <select name="brand_selection"
                                        class="custom-select border-0 rounded-0 w-auto bg-light">
                                        <option value="" selected disabled>Hour</option>
                                        <option value="all">Minutes</option>
                                        <option value="1">Secound</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="without-validation">
                                <label class="input-label">Digital Payment <span class="text-danger">*</span></label>
                                <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                    <span class="mb-0">Status</span>
                                    <label class="toggle-switch toggle-switch-sm">
                                        <input type="checkbox" class="toggle-switch-input" id="" checked>
                                        <span class="toggle-switch-label text mb-0">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0 without-validation">
                                <label class="input-label">Order Notification Type <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap align-items-center form-control border">
                                    <label class="form-check form--check mr-2 mr-md-4 mb-0 change-currency-position">
                                        <input type="radio" class="form-check-input"
                                            name="projectViewNewProjectTypeRadio" id="projectViewNewProjectTypeRadio1">
                                        <span class="form-check-label">
                                            Firebase
                                        </span>
                                    </label>
                                    <label class="form-check form--check mb-0 change-currency-position">
                                        <input type="radio" class="form-check-input"
                                            name="projectViewNewProjectTypeRadio" id="projectViewNewProjectTypeRadio2"
                                            checked>
                                        <span class="form-check-label">
                                            Manual
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="input-label d-flex align-items-center gap-1">
                                    Password
                                    <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                        title="{{ translate('Content Need') }}">
                                    </i>
                                </label>
                                <div class="position-relative">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Ex: 8+ Character" maxlength="88" required="">
                                    <div class="__right-eye">
                                        <i class="tio-hidden-outlined"></i>
                                    </div>
                                </div>
                                <span class="typing-error">Type Strong Password</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="input-label d-flex align-items-center gap-1">
                                    Confirm Password
                                    <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                        title="{{ translate('Content Need') }}">
                                    </i>
                                </label>
                                <div class="position-relative">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Ex: 8+ Character" maxlength="88" required="">
                                    <div class="__right-eye">
                                        <i class="tio-hidden-outlined"></i>
                                    </div>
                                </div>
                                <span class="typing-error">Passwords do not match</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0 without-validation">
                                <label class="input-label d-flex align-items-center gap-1">
                                    Date
                                    <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                        title="{{ translate('Content Need') }}">
                                    </i>
                                </label>
                                <label class="input-date">
                                    <input type="text" name="dates" id="" value=""
                                        class="js-flatpickr form-control flatpickr-custom"
                                        placeholder="{{ translate('dd/mm/yy') }}"
                                        data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0 without-validation">
                                <label class="input-label d-flex align-items-center gap-1">
                                    Time
                                    <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                        title="{{ translate('Content Need') }}">
                                    </i>
                                </label>
                                <label class="input-time w-100">
                                    <input type="time" name="dates" id="" value=""
                                        class="form-control" placeholder="">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                        <button type="reset"
                            class="btn btn--reset min-w-120px min-h-45px">{{ translate('Reset') }}</button>
                        <button type="submit"
                            class="btn btn--primary min-w-120px min-h-45px">{{ translate('Submit') }}</button>
                    </div>
                </div>
            </div>
        </form>


        <!-- New Table Markup -->
        <div class="card mt-4">
            <div class="card-header flex-wrap gap-2 border-0">
                <form action="#0" method="GET">
                    <div class="input-group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control h-40"
                            placeholder="{{ translate('Search Here...') }}" aria-label="Search" value="" required
                            autocomplete="off">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text title-bg3 p-2 text-white">
                                <i class="tio-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center gap-3">
                    {{-- not functional right now --}}
                    <button type="button"
                        class="btn text-dark h-30 fs-13 filter-button rounded d-flex align-items-center gap-2">
                        <i class="tio-filter-list title-clr3"></i> Filter
                    </button>

                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker export_btn h-30 text-dark btn btn-sm dropdown-toggle min-height-30"
                            href="javascript:;"
                            data-hs-unfold-options="{
                                    &quot;target&quot;: &quot;#usersExportDropdown&quot;,
                                    &quot;type&quot;: &quot;css-animation&quot;
                                }"
                            data-hs-unfold-target="#usersExportDropdown" data-hs-unfold-invoker="">
                            <i class="tio-download-to title-clr3 top-02"></i>
                            Export
                            <i class="tio-down-ui fs-10 title-clr3"></i>
                        </a>
                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right hs-unfold-content-initialized hs-unfold-css-animation animated hs-unfold-hidden"
                            data-hs-target-height="98.7188" data-hs-unfold-content=""
                            data-hs-unfold-content-animation-in="slideInUp" data-hs-unfold-content-animation-out="fadeOut"
                            style="animation-duration: 300ms;">
                            <span class="dropdown-header">Download
                                Options</span>
                            <a id="export-excel" class="dropdown-item" href="#0">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                    alt="Image Description">
                                Excel
                            </a>
                        </div>
                    </div>

                    {{-- referance add button --}}
                    <a href="#0" type="button"
                        class="btn d-inline-flex h-30 align-items-center gap-1 px-xxl-3 px-xl-3 px-2 py-2 btn--primary fs-13">
                        <i class="tio-add"></i>
                        <span class="d-lg-inline d-none text-nowrap fs-13">Add Lorem</span>
                    </a>

                    {{-- similier as anchor tag --}}
                    <button
                        class="btn d-inline-flex h-30 align-items-center gap-1 px-xxl-3 px-xl-3 px-2 py-2 btn--primary fs-13"
                        data-toggle="modal" data-target="#attribute-modal">
                        <i class="tio-add"></i>
                        <span class="d-lg-inline d-none text-nowrap fs-13">{{ translate('add_attribute') }}</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>SL</th>
                            <th>Product Info</th>
                            <th>Price</th>
                            <th>Tax Amount</th>
                            <th>Discount</th>
                            <th>Stock</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="position-relative table-custom-tr">
                            <td class=" text-dark">1</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn p-0 collapse-btn">
                                        <span class="rounded-circle icon">
                                            <i class="tio-chevron-down top-02"></i>
                                        </span>
                                    </button>
                                    <a href="#0" class="product-list-media">
                                        <img class="w-45px h-45px min-w-45px"
                                            src="{{ asset('public/assets/admin/img/store-1.png') }}" alt="">
                                        <span class="name min-w-180px">
                                            <h6 class="line--limit-1 mb-0">
                                                In a laoreet purus. Integer turpis quam, laoreet id orci nec, ultrices
                                                lacinia nunc. Aliquam erat vo
                                            </h6>
                                            <span class="fs-12px d-block text-gray">3 Variants</span>
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <td class=" text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                10%
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="h-40 py-1 text-center px-3 border rounded text-dark d-flex align-items-center justify-content-center">
                                        65
                                    </div>
                                    <button class="btn p-0 text-theme" type="button" data-toggle="modal"
                                        data-target="#exampleModal">
                                        <i class="tio-edit"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn" href="#0">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="action-btn" href="#0">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- This Table Tr hide and collapse -->
                        <tr class="table-collapse-body d-none">
                            <td class="table-item-data collapse-content bg-light" colspan="100%">
                                <div class="py-3 px-3 table-data-items-space">
                                    <div class="row g-3">
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">20</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00 (10%
                                                            Dis)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- End -->
                        <tr class="position-relative table-custom-tr">
                            <td class=" text-dark">1</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn p-0 collapse-btn">
                                        <span class="rounded-circle icon">
                                            <i class="tio-chevron-down top-02"></i>
                                        </span>
                                    </button>
                                    <a href="#0" class="product-list-media">
                                        <img class="w-45px h-45px min-w-45px"
                                            src="{{ asset('public/assets/admin/img/store-1.png') }}" alt="">
                                        <span class="name min-w-180px">
                                            <h6 class="line--limit-1 mb-0">
                                                In a laoreet purus. Integer turpis quam, laoreet id orci nec, ultrices
                                                lacinia nunc. Aliquam erat vo
                                            </h6>
                                            <span class="fs-12px d-block text-gray">3 Variants</span>
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <td class=" text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                10%
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="h-40 py-1 text-center px-3 border rounded text-dark d-flex align-items-center justify-content-center">
                                        65
                                    </div>
                                    <button class="btn p-0 text-theme" type="button" data-toggle="modal"
                                        data-target="#exampleModal">
                                        <i class="tio-edit"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn" href="#0">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="action-btn" href="#0">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- This Table Tr hide and collapse -->
                        <tr class="table-collapse-body d-none">
                            <td class="table-item-data collapse-content bg-light" colspan="100%">
                                <div class="py-3 px-3 table-data-items-space">
                                    <div class="row g-3">
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">20</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00 (10%
                                                            Dis)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- End -->
                        <tr class="position-relative table-custom-tr">
                            <td class=" text-dark">1</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn p-0 collapse-btn">
                                        <span class="rounded-circle icon">
                                            <i class="tio-chevron-down top-02"></i>
                                        </span>
                                    </button>
                                    <a href="#0" class="product-list-media">
                                        <img class="w-45px h-45px min-w-45px"
                                            src="{{ asset('public/assets/admin/img/store-1.png') }}" alt="">
                                        <span class="name min-w-180px">
                                            <h6 class="line--limit-1 mb-0">
                                                In a laoreet purus. Integer turpis quam, laoreet id orci nec, ultrices
                                                lacinia nunc. Aliquam erat vo
                                            </h6>
                                            <span class="fs-12px d-block text-gray">3 Variants</span>
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <td class=" text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                10%
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="h-40 py-1 text-center px-3 border rounded text-dark d-flex align-items-center justify-content-center">
                                        65
                                    </div>
                                    <button class="btn p-0 text-theme" type="button" data-toggle="modal"
                                        data-target="#exampleModal">
                                        <i class="tio-edit"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn" href="#0">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="action-btn" href="#0">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- This Table Tr hide and collapse -->
                        <tr class="table-collapse-body d-none">
                            <td class="table-item-data collapse-content bg-light" colspan="100%">
                                <div class="py-3 px-3 table-data-items-space">
                                    <div class="row g-3">
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">20</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00 (10%
                                                            Dis)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- End -->
                        <tr class="position-relative table-custom-tr">
                            <td class=" text-dark">1</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn p-0 collapse-btn">
                                        <span class="rounded-circle icon">
                                            <i class="tio-chevron-down top-02"></i>
                                        </span>
                                    </button>
                                    <a href="#0" class="product-list-media">
                                        <img class="w-45px h-45px min-w-45px"
                                            src="{{ asset('public/assets/admin/img/store-1.png') }}" alt="">
                                        <span class="name min-w-180px">
                                            <h6 class="line--limit-1 mb-0">
                                                In a laoreet purus. Integer turpis quam, laoreet id orci nec, ultrices
                                                lacinia nunc. Aliquam erat vo
                                            </h6>
                                            <span class="fs-12px d-block text-gray">3 Variants</span>
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <td class=" text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                10%
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="h-40 py-1 text-center px-3 border rounded text-dark d-flex align-items-center justify-content-center">
                                        65
                                    </div>
                                    <button class="btn p-0 text-theme" type="button" data-toggle="modal"
                                        data-target="#exampleModal">
                                        <i class="tio-edit"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn" href="#0">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="action-btn" href="#0">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- This Table Tr hide and collapse -->
                        <tr class="table-collapse-body d-none">
                            <td class="table-item-data collapse-content bg-light" colspan="100%">
                                <div class="py-3 px-3 table-data-items-space">
                                    <div class="row g-3">
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">20</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00 (10%
                                                            Dis)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- End -->
                        <tr class="position-relative table-custom-tr">
                            <td class=" text-dark">1</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn p-0 collapse-btn">
                                        <span class="rounded-circle icon">
                                            <i class="tio-chevron-down top-02"></i>
                                        </span>
                                    </button>
                                    <a href="#0" class="product-list-media">
                                        <img class="w-45px h-45px min-w-45px"
                                            src="{{ asset('public/assets/admin/img/store-1.png') }}" alt="">
                                        <span class="name min-w-180px">
                                            <h6 class="line--limit-1 mb-0">
                                                In a laoreet purus. Integer turpis quam, laoreet id orci nec, ultrices
                                                lacinia nunc. Aliquam erat vo
                                            </h6>
                                            <span class="fs-12px d-block text-gray">3 Variants</span>
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <td class=" text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                10%
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="h-40 py-1 text-center px-3 border rounded text-dark d-flex align-items-center justify-content-center">
                                        65
                                    </div>
                                    <button class="btn p-0 text-theme" type="button" data-toggle="modal"
                                        data-target="#exampleModal">
                                        <i class="tio-edit"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn" href="#0">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="action-btn" href="#0">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- This Table Tr hide and collapse -->
                        <tr class="table-collapse-body d-none">
                            <td class="table-item-data collapse-content bg-light" colspan="100%">
                                <div class="py-3 px-3 table-data-items-space">
                                    <div class="row g-3">
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">20</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00 (10%
                                                            Dis)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- End -->
                        <tr class="position-relative table-custom-tr">
                            <td class=" text-dark">1</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn p-0 collapse-btn">
                                        <span class="rounded-circle icon">
                                            <i class="tio-chevron-down top-02"></i>
                                        </span>
                                    </button>
                                    <a href="#0" class="product-list-media">
                                        <img class="w-45px h-45px min-w-45px"
                                            src="{{ asset('public/assets/admin/img/store-1.png') }}" alt="">
                                        <span class="name min-w-180px">
                                            <h6 class="line--limit-1 mb-0">
                                                In a laoreet purus. Integer turpis quam, laoreet id orci nec, ultrices
                                                lacinia nunc. Aliquam erat vo
                                            </h6>
                                            <span class="fs-12px d-block text-gray">3 Variants</span>
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <td class=" text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                $234.00
                            </td>
                            <td class="text-dark">
                                10%
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="h-40 py-1 text-center px-3 border rounded text-dark d-flex align-items-center justify-content-center">
                                        65
                                    </div>
                                    <button class="btn p-0 text-theme" type="button" data-toggle="modal"
                                        data-target="#exampleModal">
                                        <i class="tio-edit"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn" href="#0">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="action-btn" href="#0">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- This Table Tr hide and collapse -->
                        <tr class="table-collapse-body d-none">
                            <td class="table-item-data collapse-content bg-light" colspan="100%">
                                <div class="py-3 px-3 table-data-items-space">
                                    <div class="row g-3">
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">20</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00 (10%
                                                            Dis)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                            <div class="rounded p-2 border bg-white d-flex align-items-center gap-2">
                                                <img width="40" height="40"
                                                    src="{{ asset('public/assets/admin/img/store-1.png') }}"
                                                    alt="" class="rounded">
                                                <div class="cont d-flex flex-wrap">
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Variant :</span>
                                                        <span class="fs-12px d-block text-dark">Small, Black</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Stoke :</span>
                                                        <span class="fs-12px d-block text-dark">10</span>
                                                    </div>
                                                    <div class="item d-flex align-items-center ">
                                                        <span class="fs-12px d-block text-gray">Price :</span>
                                                        <span class="fs-12px d-block text-dark">$ 1,000,000.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- End -->
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex align-items-center flex-wrap gap-2 justify-content-between border-0">
                <div class="d-flex align-items-center gap-2">
                    <select name="data_counting_select" class="custom-select w-auto custom-select-small h-25px">
                        <option value="20 Items">20 Items</option>
                        <option value="10 Items">10 Items</option>
                    </select>
                    <p class="text-record fs-12px m-0">Showing 1 To 20 Of 100 Records</p>
                </div>
                <div class="d-flex justify-content-center justify-content-sm-end">
                    <div class="page-item">
                        <span class="page-link">
                            <svg width="9" height="9" viewBox="0 0 9 9" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.45225 0.416895C4.55984 0.416222 4.66518 0.450067 4.75484 0.514118C4.84449 0.578168 4.91441 0.669521 4.95566 0.77653C4.99692 0.883539 5.00765 1.00136 4.98649 1.11496C4.96533 1.22856 4.91323 1.3328 4.83684 1.41439L1.966 4.50023L4.83684 7.58606C4.92558 7.69765 4.97195 7.8412 4.96668 7.98801C4.96141 8.13482 4.9049 8.27409 4.80843 8.37797C4.71197 8.48186 4.58265 8.54272 4.44632 8.54839C4.31 8.55406 4.17671 8.50413 4.07309 8.40856L0.823086 4.90856C0.7222 4.79927 0.665573 4.65142 0.665573 4.49731C0.665573 4.3432 0.7222 4.19536 0.823086 4.08606L4.07309 0.586062C4.17398 0.478294 4.31015 0.41754 4.45225 0.416895Z"
                                    fill="#6A727A" />
                                <path
                                    d="M7.70225 0.416883C7.80984 0.41621 7.91518 0.450056 8.00484 0.514107C8.09449 0.578157 8.16441 0.66951 8.20566 0.776519C8.24692 0.883528 8.25765 1.00134 8.23649 1.11495C8.21533 1.22855 8.16323 1.33279 8.08684 1.41438L5.216 4.50022L8.08684 7.58605C8.18883 7.69589 8.24614 7.84487 8.24614 8.00022C8.24614 8.15556 8.18883 8.30454 8.08684 8.41438C7.98484 8.52423 7.8465 8.58594 7.70225 8.58594C7.55801 8.58594 7.41967 8.52423 7.31767 8.41438L4.06767 4.91438C3.96678 4.80509 3.91016 4.65724 3.91016 4.50313C3.91016 4.34902 3.96678 4.20118 4.06767 4.09188L7.31767 0.591883C7.36784 0.536765 7.42766 0.492917 7.49368 0.462877C7.55969 0.432837 7.63059 0.417204 7.70225 0.416883Z"
                                    fill="#6A727A" />
                            </svg>
                        </span>
                    </div>
                    <div class="page-item">
                        <span class="page-link">
                            <svg width="8" height="9" viewBox="0 0 8 9" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M3.99989 0.416895C3.89231 0.416222 3.78697 0.450067 3.69731 0.514118C3.60766 0.578168 3.53774 0.669521 3.49649 0.77653C3.45523 0.883539 3.4445 1.00136 3.46566 1.11496C3.48682 1.22856 3.53892 1.3328 3.61531 1.41439L6.48615 4.50023L3.61531 7.58606C3.52657 7.69765 3.4802 7.8412 3.48547 7.98801C3.49073 8.13482 3.54725 8.27409 3.64371 8.37797C3.74018 8.48186 3.8695 8.54272 4.00582 8.54839C4.14215 8.55406 4.27544 8.50413 4.37906 8.40856L7.62906 4.90856C7.72995 4.79927 7.78657 4.65142 7.78657 4.49731C7.78657 4.3432 7.72995 4.19536 7.62906 4.08606L4.37906 0.586062C4.27817 0.478294 4.142 0.41754 3.99989 0.416895Z"
                                    fill="#6A727A" />
                                <path
                                    d="M0.749894 0.416883C0.642306 0.41621 0.536969 0.450056 0.447312 0.514107C0.357656 0.578157 0.287742 0.66951 0.246485 0.776519C0.205228 0.883528 0.194497 1.00134 0.21566 1.11495C0.236822 1.22855 0.28892 1.33279 0.365311 1.41438L3.23615 4.50022L0.365312 7.58605C0.263314 7.69589 0.206012 7.84487 0.206012 8.00022C0.206012 8.15556 0.263314 8.30454 0.365312 8.41438C0.46731 8.52423 0.605648 8.58594 0.749895 8.58594C0.894142 8.58594 1.03248 8.52423 1.13448 8.41438L4.38448 4.91438C4.48537 4.80509 4.54199 4.65724 4.54199 4.50313C4.54199 4.34902 4.48537 4.20118 4.38448 4.09188L1.13448 0.591883C1.08431 0.536765 1.02449 0.492917 0.958472 0.462877C0.892456 0.432837 0.821561 0.417204 0.749894 0.416883Z"
                                    fill="#6A727A" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>




        <h1>Check Validation</h1>
        <form action="" class="fnd-validation">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-8 col-md-7">
                            <div class="d-flex flex-column gap-24px">
                                <ul class="nav nav-tabs border-0">
                                    <li class="nav-item">
                                        <a class="nav-link lang_link active" href="#0"
                                            id="default-link">Default(EN)</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link lang_link" href="#0" id="ar-link">Arabic -
                                            العربية(AR)</a>
                                    </li>
                                </ul>
                                <div class="lang_form" id="default-form">
                                    <div class="d-flex flex-column gap-24px">
                                        <div class="form-group mb-0">
                                            <label class="input-label d-flex align-items-center gap-1">
                                                User name <span class="text-danger">*</span>
                                                <i class="tio-info text-muted" data-toggle="tooltip"
                                                    data-placement="top" title="{{ translate('Content Need') }}">
                                                </i>
                                            </label>
                                            <input type="text" name="name" class="form-control"
                                                placeholder="User Name" required="">
                                            <span class="typing-error">Please enter your first name.</span>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="input-label d-flex align-items-center gap-1">
                                                Email Address <span class="text-danger">*</span>
                                                <i class="tio-info text-muted" data-toggle="tooltip"
                                                    data-placement="top" title="{{ translate('Content Need') }}">
                                                </i>
                                            </label>
                                            <input type="text" name="email" class="form-control"
                                                placeholder="Type Your Mail" required="">
                                            <span class="typing-error">Please enter a valid email</span>
                                        </div>
                                        <div class="form-group counting-character-item mb-0">
                                            <div
                                                class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
                                                <label class="input-label d-flex align-items-center gap-1 mb-0">
                                                    Description
                                                    <i class="tio-info text-muted" data-toggle="tooltip"
                                                        data-placement="top" title="{{ translate('Content Need') }}">
                                                    </i>
                                                </label>
                                                <label class="toggle-switch my-0">
                                                    <input type="checkbox" class="toggle-switch-input" checked="">
                                                    <span class="toggle-switch-label mx-auto text">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </div>
                                            <textarea type="text" name="bio" rows="1" class="form-control" maxlength="100"
                                                placeholder="Write Description" required=""></textarea>
                                            <span class="counting-character text-right mt-1 d-block">0/100</span>
                                            <span class="typing-error">Typing something</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="lang_form d-none" id="ar-form">
                                    <div class="d-flex flex-column gap-24px"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-5">
                            <div class="upload-image-group bg-light rounded p-lg-4 p-3 h-100">
                                <div class="d-flex flex-column justify-content-center h-100">
                                    <div class="text-center text-dark mb-3 text--title">
                                        Upload Image
                                    </div>
                                    <label class="upload--squire ratio-2-1">
                                        <input type="file" name="image" id="customFileEg1"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" hidden="">
                                        <img id="viewer" src="{{ asset('public/assets/admin') }}/img/add-image.png"
                                            alt="Branch image">
                                    </label>
                                    <span class="fs-10 text-center mt-3">JPG, JPEG, PNG Image size : Max 5 MB
                                        <strong>(1:1)</strong></span>
                                    <span class="typing-error justify-content-center">File Size is larger</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="input-label">Phone <span class="text-danger">*</span></label>
                                <input type="phone" name="phone" class="form-control"
                                    placeholder="EX : +09853834" required="">
                                <span class="typing-error">Please enter a valid number</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="input-label">Selection</label>
                                <select name="brand_selection" class="custom-select">
                                    <option value="" selected disabled>Select Brand</option>
                                    <option value="all">All Brand</option>
                                    <option value="1">examle 01</option>
                                    <option value="2">examle 02</option>
                                    <option value="3">examle 03</option>
                                </select>
                                <span class="typing-error">Select Brand</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0 without-validation">
                                <label class="input-label">Selection</label>
                                <div class="d-flex align-items-center gap-0 border rounded">
                                    <input type="text" name="time-select" placeholder="Select from dropdown"
                                        class="from-control px-3 w-100 border-0 rounded-0">
                                    <select name="brand_selection"
                                        class="custom-select border-0 rounded-0 w-auto bg-light">
                                        <option value="" selected disabled>Hour</option>
                                        <option value="all">Minutes</option>
                                        <option value="1">Secound</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="without-validation">
                                <label class="input-label">Digital Payment <span class="text-danger">*</span></label>
                                <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                    <span class="mb-0">Status</span>
                                    <label class="toggle-switch toggle-switch-sm">
                                        <input type="checkbox" class="toggle-switch-input" id="" checked>
                                        <span class="toggle-switch-label text mb-0">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0 without-validation">
                                <label class="input-label">Order Notification Type <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap align-items-center form-control border">
                                    <label class="form-check form--check mr-2 mr-md-4 mb-0 change-currency-position">
                                        <input type="radio" class="form-check-input"
                                            name="projectViewNewProjectTypeRadio" id="projectViewNewProjectTypeRadio1">
                                        <span class="form-check-label">
                                            Firebase
                                        </span>
                                    </label>
                                    <label class="form-check form--check mb-0 change-currency-position">
                                        <input type="radio" class="form-check-input"
                                            name="projectViewNewProjectTypeRadio" id="projectViewNewProjectTypeRadio2"
                                            checked>
                                        <span class="form-check-label">
                                            Manual
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="input-label d-flex align-items-center gap-1">
                                    Password
                                    <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                        title="{{ translate('Content Need') }}">
                                    </i>
                                </label>
                                <div class="position-relative">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Ex: 8+ Character" maxlength="88" required="">
                                    <div class="__right-eye">
                                        <i class="tio-hidden-outlined"></i>
                                    </div>
                                </div>
                                <span class="typing-error">Type Strong Password</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="input-label d-flex align-items-center gap-1">
                                    Confirm Password
                                    <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                        title="{{ translate('Content Need') }}">
                                    </i>
                                </label>
                                <div class="position-relative">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Ex: 8+ Character" maxlength="88" required="">
                                    <div class="__right-eye">
                                        <i class="tio-hidden-outlined"></i>
                                    </div>
                                </div>
                                <span class="typing-error">Passwords do not match</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0 without-validation">
                                <label class="input-label d-flex align-items-center gap-1">
                                    Date
                                    <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                        title="{{ translate('Content Need') }}">
                                    </i>
                                </label>
                                <label class="input-date">
                                    <input type="text" name="dates" id="" value=""
                                        class="js-flatpickr form-control flatpickr-custom"
                                        placeholder="{{ translate('dd/mm/yy') }}"
                                        data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0 without-validation">
                                <label class="input-label d-flex align-items-center gap-1">
                                    Time
                                    <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top"
                                        title="{{ translate('Content Need') }}">
                                    </i>
                                </label>
                                <label class="input-time w-100">
                                    <input type="time" name="dates" id="" value=""
                                        class="form-control" placeholder="">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                        <button type="reset"
                            class="btn btn--reset min-w-120px min-h-45px">{{ translate('Reset') }}</button>
                        <button type="submit"
                            class="btn btn--primary min-w-120px min-h-45px">{{ translate('Submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection


@push('script_2')
@endpush
