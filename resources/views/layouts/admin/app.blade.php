<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    @php($icon = \App\Model\BusinessSetting::where(['key' => 'fav_icon'])->first()->value)
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/app/public/restaurant/' . $icon ?? '') }}">
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/owl.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/style.css">
    @stack('css_or_js')

    <script
        src="{{asset('public/assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/toastr.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/custom-helper.css">
</head>

<body class="footer-offset">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-none" id="loading">
                <div class="loader-image">
                    <img width="200" src="{{asset('public/assets/admin/img/loader.gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.admin.partials._header')
@include('layouts.admin.partials._sidebar')

<main id="content" role="main" class="main pointer-event">

@yield('content')

@include('layouts.admin.partials._footer')

    <div class="modal fade" id="popup-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <h2 class="order-check-colour">
                                    <i class="tio-shopping-cart-outlined"></i> {{translate('You have new order, Check Please.')}}
                                </h2>
                                <hr>
                                <button id="check-order" class="btn btn-primary">{{translate('Ok, let me check')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin-views.partials._image-modal')
</main>

{{--<span class="global-data-to-js"--}}
{{--      data-maximum-upload-filesize="{{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}"--}}
{{--></span>--}}

<script src="{{asset('public/assets/admin')}}/js/custom.js"></script>

@stack('script')

<script src="{{asset('public/assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('public/assets/admin')}}/js/toastr.js"></script>
<script src="{{asset('public/assets/admin/js/owl.min.js')}}"></script>
<script src="{{asset('public/assets/admin/js/firebase.min.js')}}"></script>

{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif
<script>
    $(document).on('ready', function () {
        var sidebar = $('.js-navbar-vertical-aside').hsSideNav();

        $('.js-nav-tooltip-link').tooltip({boundary: 'window'})

        $(".js-nav-tooltip-link").on("show.bs.tooltip", function (e) {
            if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
                return false;
            }
        });

        $('.js-hs-unfold-invoker').each(function () {
            var unfold = new HSUnfold($(this)).init();
        });

        $('.js-form-search').each(function () {
            new HSFormSearch($(this)).init()
        });

        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });

        $('.js-daterangepicker').daterangepicker();

        $('.js-daterangepicker-times').daterangepicker({
            timePicker: true,
            startDate: moment().startOf('hour'),
            endDate: moment().startOf('hour').add(32, 'hour'),
            locale: {
                format: 'M/DD hh:mm A'
            }
        });

        /* 18.1: Increase */
        $('.quantity__plus').on('click', function (e) {
            e.stopPropagation();
            var $qty = $(this).parent().find('input');
            var currentVal = parseInt($qty.val());
            if (!isNaN(currentVal)) {
                $qty.val(currentVal + 1);
            }
            if(currentVal >= $qty.attr('max') -1){
                $(this).attr('disabled', true);
            }
            quantityListener();
        });

        // /* 18.2: Decrease */
        $('.quantity__minus').on('click', function (e) {
            e.stopPropagation();
            var $qty = $(this).parent().find('input');
            var currentVal = parseInt($qty.val());
            if (!isNaN(currentVal) && currentVal > 1) {
                $qty.val(currentVal - 1);
            }
            if (currentVal < $qty.attr('max')) {
                $('.quantity__plus').removeAttr('disabled', true);
            }
            quantityListener();
        });

        /* 18.3: show hide delete icon */
        function quantityListener() {
            $('.quantity__qty').each(function () {
                var qty = $(this);
                if (qty.val() == 1) {
                    qty.siblings('.quantity__minus').html('<i class="tio-delete text-danger fs-10"></i>')
                } else {
                    qty.siblings('.quantity__minus').html('<i class="tio-remove"></i>')
                }
            });
        }
        quantityListener();
    });

    //Form Validatioon
    $(document).on('ready', function () {
        $("form.fnd-validation").each(function () {
            const $form = $(this);

            if ($form.closest('.without-validation').length > 0) {
                return; // skip binding validation entirely
            }

            const $inputs = $form.find('input, textarea, select').not('[type="file"]');
            const $fileInputs = $form.find('input[type="file"]');
            const $bio = $form.find('[name="bio"]');
            const $counter = $('.counting-character');
            const $pass = $form.find('[name="password"]');

            // Character counter
            $counter.text('0/100');
            $bio.on('input', function () {
                const len = this.value.length;
                $counter.text(`${len}/100`).toggleClass('limit-reached', len >= 100);
            });

            // Blur validation
            $inputs.on('blur', function () {
                validateField($(this));
            });

            // File change validation
            $fileInputs.on('change', function () {
                validateFile($(this));
            });

            // Reset button
            $form.find('button[type="reset"]').on('click', function () {
                $form[0].reset();
                $counter.text('0/100').removeClass('limit-reached');
                $('.typing-error').hide();
            });

            // Submit
            $form.on('submit', function (e) {
                let valid = true;

                $inputs.each(function () {
                    const $el = $(this);
                    if ($el.closest('.without-validation').length === 0) {
                        if (!validateField($el)) valid = false;
                    }
                });

                $fileInputs.each(function () {
                    const $el = $(this);
                    if ($el.closest('.without-validation').length === 0) {
                        if (!validateFile($el)) valid = false;
                    }
                });

                if (!valid) {
                    e.preventDefault(); // Only block if something invalid
                }
            });

            // Validation for normal fields
            function validateField($el) {
                const val = $.trim($el.val());
                const name = $el.attr('name');
                const $error = $el.closest('.form-group, .upload-image-group').find('.typing-error');
                let ok = true;

                if (['name', 'brand_selection'].includes(name)) ok = val !== '';
                else if (name === 'email') ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
                else if (name === 'bio') ok = val.length >= 5;
                else if (name === 'phone') ok = /^\+?[0-9\s\-()]{7,20}$/.test(val);
                else if (name === 'password') ok = val.length >= 8;
                else if (name === 'confirm_password') ok = val.length >= 8 && val === $pass.val();
                else ok = val !== '';

                if ($error.length) $error.css('display', ok ? 'none' : 'flex');
                return ok;
            }

            // Validation for file inputs
            function validateFile($input) {
                const $error = $input.closest('.form-group, .upload-image-group').find('.typing-error');
                const file = $input[0].files[0];
                let ok = true;

                if (!file) {
                    ok = false;
                } else {
                    const types = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff'];
                    ok = types.includes(file.type) && file.size <= 2 * 1024 * 1024;
                }

                if ($error.length) $error.css('display', ok ? 'none' : 'flex');
                return ok;
            }
        });
    });

    //Form Validatioon
    $(document).ready(function () {
        // Generate unique IDs for collapsible content
        $('.table').each(function (tableIndex) {
            $(this).find('.table-custom-tr').each(function (rowIndex) {
                var $mainRow = $(this);
                var $collapseRow = $mainRow.next('.table-collapse-body');

                if ($collapseRow.length) {
                    var uniqueId = 'collapse-body-' + tableIndex + '-' + rowIndex;
                    $mainRow.attr('data-collapse-id', uniqueId);
                    $collapseRow.attr('data-collapse-id', uniqueId);
                }
            });
        });
        // Handle collapse button click
        $('.collapse-btn').on('click', function () {
            var $btn = $(this);
            var $mainRow = $btn.closest('.table-custom-tr');
            var collapseId = $mainRow.data('collapse-id');
            var $collapseRow = $('.table-collapse-body[data-collapse-id="' + collapseId + '"]');
            var $content = $collapseRow.find('.collapse-content');

            // Toggle active class
            $mainRow.toggleClass('active');

            if ($collapseRow.hasClass('d-none')) {
                // Opening: first remove d-none, then slide down
                $collapseRow.removeClass('d-none');
                $content.hide().slideDown(5);
            } else {
                // Closing: slide up, then add d-none after animation completes
                $content.slideUp(5, function () {
                    $collapseRow.addClass('d-none');
                });
            }
        });
    });
    //Custom Table Collapse


    //Upload Image
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#viewer').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#customFileEg1").change(function () {
        readURL(this);
    });

    //Password Viewer
    $('.__right-eye').on('click', function(){
        if($(this).hasClass('active')) {
            $(this).removeClass('active')
            $(this).find('i').removeClass('tio-invisible')
            $(this).find('i').addClass('tio-hidden-outlined')
            $(this).siblings('input').attr('type', 'password')
        }else {
            $(this).addClass('active')
            $(this).siblings('input').attr('type', 'text')

            $(this).find('i').addClass('tio-invisible')
            $(this).find('i').removeClass('tio-hidden-outlined')
        }
    })
</script>


@stack('script_2')
<audio id="myAudio">
    <source src="{{asset('public/assets/admin/sound/notification.mp3')}}" type="audio/mpeg">
</audio>

<script>
    var audio = document.getElementById("myAudio");

    function playAudio() {
        audio.play();
    }

    function pauseAudio() {
        audio.pause();
    }
</script>
<script>
    $('#check-order').on('click', function(){
        location.href = '{{route('admin.order.list',['status'=>'all'])}}';
    })

    function route_alert(route, message) {
        Swal.fire({
            title: '{{translate("Are you sure?")}}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#01684b',
            cancelButtonText: '{{translate("No")}}',
            confirmButtonText: '{{translate("Yes")}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = route;
            }
        })
    }

    $('.form-alert').on('click', function (){
        let id = $(this).data('id');
        let message = $(this).data('message');
        form_alert(id, message)
    });

    function form_alert(id, message) {
        Swal.fire({
            title: '{{translate("Are you sure?")}}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#01684b',
            cancelButtonText: '{{translate("No")}}',
            confirmButtonText: '{{translate("Yes")}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#'+id).submit()
            }
        })
    }

    function call_demo(){
        toastr.info('{{translate("Disabled for demo version!")}}')
    }

    $('.call-demo').click(function() {
        if ('{{ env('APP_MODE') }}' === 'demo') {
            call_demo();
        }
    });
</script>

<script>

    $('.status-change-alert').on('click', function (){
        let url = $(this).data('route');
        let message = $(this).data('message');
        status_change_alert(url, message, event)
    });

    function status_change_alert(url, message, e) {
        e.preventDefault();
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#107980',
            confirmButtonText: '{{ translate("Yes") }}',
            cancelButtonText: '{{ translate("No") }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = url;
            }
        })
    }
</script>

<script>
    var initialImages = [];
    $(window).on('load', function() {
        $("form").find('img').each(function (index, value) {
            initialImages.push(value.src);
        })
    })

    $(document).ready(function() {
        $('form').on('reset', function(e) {
            $("form").find('img').each(function (index, value) {
                $(value).attr('src', initialImages[index]);
            })
        });
    });
</script>

<script>
    $(function(){
        var owl = $('.single-item-slider');
        owl.owlCarousel({
            autoplay: false,
            items:1,
            onInitialized  : counter,
            onTranslated : counter,
            autoHeight: true,
            dots: true,
        });

        function counter(event) {
            var element   = event.target;         // DOM element, in this example .owl-carousel
            var items     = event.item.count;     // Number of items
            var item      = event.item.index + 1;     // Position of the current item

            if(item > items) {
                item = item - items
            }
            $('.slide-counter').html(+item+"/"+items)
        }
    });
</script>

<script>
    @php($admin_order_notification = \App\CentralLogics\Helpers::get_business_settings('admin_order_notification'))
    @php($admin_order_notification_type = \App\CentralLogics\Helpers::get_business_settings('admin_order_notification_type'))

    @if(\App\CentralLogics\Helpers::module_permission_check('order_management') && $admin_order_notification)

    @if($admin_order_notification_type == 'manual')
    setInterval(function () {
        $.get({
            url: '{{route('admin.get-restaurant-data')}}',
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                new_order_type = data.type;
                if (data.new_order > 0) {
                    playAudio();
                    $('#popup-modal').appendTo("body").modal('show');
                }
            },
        });
    }, 10000);
    @endif

    @if($admin_order_notification_type == 'firebase')
    @php($fcm_credentials = \App\CentralLogics\Helpers::get_business_settings('firebase_message_config'))
    var firebaseConfig = {
        apiKey: "{{isset($fcm_credentials['apiKey']) ? $fcm_credentials['apiKey'] : ''}}",
        authDomain: "{{isset($fcm_credentials['authDomain']) ? $fcm_credentials['authDomain'] : ''}}",
        projectId: "{{isset($fcm_credentials['projectId']) ? $fcm_credentials['projectId'] : ''}}",
        storageBucket: "{{isset($fcm_credentials['storageBucket']) ? $fcm_credentials['storageBucket'] : ''}}",
        messagingSenderId: "{{isset($fcm_credentials['messagingSenderId']) ? $fcm_credentials['messagingSenderId'] : ''}}",
        appId: "{{isset($fcm_credentials['appId']) ? $fcm_credentials['appId'] : ''}}",
        measurementId: "{{isset($fcm_credentials['measurementId']) ? $fcm_credentials['measurementId'] : ''}}"
    };


    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function startFCM() {
        messaging
            .requestPermission()
            .then(function() {
                return messaging.getToken();
            })
            .then(function(token) {
                subscribeTokenToBackend(token, 'grofresh_admin_message');
            }).catch(function(error) {
            console.error('Error getting permission or token:', error);
        });
    }

    function subscribeTokenToBackend(token, topic) {
        fetch('{{url('/')}}/subscribeToTopic', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: token, topic: topic })
        }).then(response => {
            if (response.status < 200 || response.status >= 400) {
                return response.text().then(text => {
                    throw new Error(`Error subscribing to topic: ${response.status} - ${text}`);
                });
            }
        }).catch(error => {
            console.error('Subscription error:', error);
        });
    }

    messaging.onMessage(function(payload) {
        if(payload.data.order_id && payload.data.type == "order_request"){
            playAudio();
            $('#popup-modal').appendTo("body").modal('show');
        }
    });

    startFCM();
    @endif
    @endif

</script>

<script>
    $(document).ready(function() {
    // --- Changing svg color ---
        $("img.svg").each(function() {
            var $img = jQuery(this);
            var imgID = $img.attr("id");
            var imgClass = $img.attr("class");
            var imgURL = $img.attr("src");

            jQuery.get(
                imgURL,
                function(data) {
                    var $svg = jQuery(data).find("svg");

                    if (typeof imgID !== "undefined") {
                        $svg = $svg.attr("id", imgID);
                    }
                    if (typeof imgClass !== "undefined") {
                        $svg = $svg.attr("class", imgClass + " replaced-svg");
                    }

                    $svg = $svg.removeAttr("xmlns:a");

                    if (
                        !$svg.attr("viewBox") &&
                        $svg.attr("height") &&
                        $svg.attr("width")
                    ) {
                        $svg.attr(
                            "viewBox",
                            "0 0 " + $svg.attr("height") + " " + $svg.attr("width")
                        );
                    }
                    $img.replaceWith($svg);
                },
                "xml"
            );
        });
    });

</script>

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('public/assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
<script src="{{asset('public/assets/admin/js/single-image-upload.js')}}"></script>
<script src="{{asset('public/assets/admin/js/file-size-type-validation.js')}}"></script>
</body>
</html>
