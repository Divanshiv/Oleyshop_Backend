@extends('layouts.admin.app')

@section('title', translate('Update Branch'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/add_branch.png')}}" class="w--24"
                    alt="{{ translate('branch') }}">
            </span>
            <span>
                {{translate('branch')}} {{translate('update')}}
            </span>
        </h1>
    </div>

    @php($branchCount = \App\Model\Branch::count())
    <form action="{{route('admin.branch.update', [$branch['id']])}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row g-2">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="tio-user"></i>
                            {{translate('store_information')}}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="name">{{translate('name')}}</label>
                                            <input type="text" name="name" id="name" value="{{$branch['name']}}"
                                                class="form-control" placeholder="{{ translate('New branch') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="address">{{translate('address')}}</label>
                                            <textarea type="text" name="address" id="address" class="form-control h--90px"
                                                placeholder="{{translate('Ex: 666/668 DOHS Mirpur, Dhaka, Bangladesh')}}"
                                                value="{{$branch['address']}}"
                                                required>{{$branch['address']}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-column justify-content-center h-100">
                                    <div class="text-center mb-3 text--title">
                                        {{translate('Branch Image')}}
                                        <small class="text-danger">*</small>
                                    </div>
                                    <label class="upload--squire">
                                        <input type="file" name="image" id="customFileEg1" value="{{$branch['image']}}"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               hidden
                                               data-maxFileSize="{{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}">
                                        <img id="viewer" src="{{$branch->imageFullPath}}" alt="branch image" />
                                    </label>
                                    <p class="fs-10 m-0 text-center mt-3">
                                        {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} : Max {{ \App\CentralLogics\Helpers::readableUploadMaxFileSize('image') }}
                                        <span class="text-dark font-semibold">(1:1)</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="phone">{{translate('phone')}}</label>
                                    <input type="phone" name="phone" id="phone" value="{{$branch['phone']}}" class="form-control"
                                        placeholder="{{ translate('EX : +09853834') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="email">{{translate('email')}}</label>
                                    <input type="email" name="email" id="email" value="{{$branch['email']}}" class="form-control"
                                        placeholder="{{ translate('EX : example@example.com') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group mb-0">
                                    <label for="password" class="input-label" for="password">{{translate('password')}} <span
                                            class="text-danger font-size-sm">* (
                                            {{ translate('input if you want to reset.') }} )</span></label>

                                    <div class="input-group input-group-merge">
                                        <input type="password" class="js-toggle-password form-control form-control-lg"
                                            name="password" id="password"
                                            placeholder="{{ translate('8+ characters required') }}"
                                            aria-label="8+ characters required"
                                            data-hs-toggle-password-options='{
                                            "target": "#changePassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changePassIcon"
                                        }'>
                                        <div id="changePassTarget" class="input-group-append">
                                            <a class="input-group-text" href="javascript:">
                                                <i id="changePassIcon" class="tio-visible-outlined"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
            @if($googleMapStatus)
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="tio-poi"></i>
                                {{translate('store_location')}}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="row  g-3">
                                        <div class="col-12">
                                            <div class="form-group mb-0">
                                                <label class="form-label text-capitalize"
                                                    for="latitude">{{ translate('latitude') }}<span
                                                        class="form-label-secondary pl-1" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('click_on_the_map_select_your_default_location') }}"><img
                                                            src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                            alt="{{ translate('click_on_the_map_select_your_default_location') }}"></span></label>
                                                <input type="text" id="latitude" name="latitude" class="form-control"
                                                    placeholder="{{ translate('Ex:') }} 23.8118428"
                                                    value="{{$branch['latitude']}}" required readonly>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group mb-0">
                                                <label class="form-label text-capitalize"
                                                    for="longitude">{{ translate('longitude') }}<span
                                                        class="form-label-secondary pl-1" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('messages.click_on_the_map_select_your_default_location') }}"><img
                                                            src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                            alt="{{ translate('click_on_the_map_select_your_default_location') }}"></span></label>
                                                <input type="text" name="longitude" class="form-control"
                                                    placeholder="{{ translate('Ex:') }} 90.356331" id="longitude"
                                                    value="{{$branch['longitude']}}" required readonly>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="">
                                                    <i class="tio-info-outined" data-toggle="tooltip" data-placement="top"
                                                        title="This value is the radius from your branch location, and customer can order food inside  the circle calculated by this radius."></i>
                                                    {{translate('coverage')}} ( {{translate('km')}} )
                                                </label>
                                                <input type="number" name="coverage" min="1" value="{{$branch['coverage']}}"
                                                    max="1000" class="form-control" placeholder="{{ translate('Ex : 3') }}"
                                                    {{$branchCount > 1 ? 'required' : ''}}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="location_map_div">
                                    <input id="pac-input" class="controls rounded" data-toggle="tooltip"
                                        data-placement="right"
                                        data-original-title="{{ translate('search_your_location_here') }}" type="text"
                                        placeholder="{{ translate('search_here') }}" />
                                    <div id="location_map_canvas" class="overflow-hidden rounded h-100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="btn--container justify-content-end mt-3">
            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
            <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
        </div>
    </form>
</div>

@endsection

@push('script_2')

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ Helpers::get_business_settings('map_api_client_key') }}&libraries=places&v=3.45.8"></script>
    {{--
    <script src="{{ asset('public/assets/admin/js/branch.js') }}"></script>--}}

    <script>
        $(document).on('ready', function () {
            $('.js-toggle-password').each(function () {
                new HSTogglePassword(this).init()
            });
        });

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


        $(document).ready(function () {
            function initAutocomplete() {
                var myLatLng = {

                    lat: {{ $branch->latitude }} ?? 23.811842872190343,
                    lng: {{ $branch->longitude }} ?? 90.356331
                };
                const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                    center: {
                        lat: {{ $branch->latitude }} ?? 23.811842872190343,
                        lng: {{ $branch->longitude }} ?? 90.356331
                    },
                    zoom: 13,
                    mapTypeId: "roadmap",
                });

                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                });

                marker.setMap(map);
                var geocoder = geocoder = new google.maps.Geocoder();
                google.maps.event.addListener(map, 'click', function (mapsMouseEvent) {
                    var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                    var coordinates = JSON.parse(coordinates);
                    var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                    marker.setPosition(latlng);
                    map.panTo(latlng);

                    document.getElementById('latitude').value = coordinates['lat'];
                    document.getElementById('longitude').value = coordinates['lng'];


                    geocoder.geocode({
                        'latLng': latlng
                    }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                document.getElementById('address').innerHtml = results[1].formatted_address;
                            }
                        }
                    });
                });
                // Create the search box and link it to the UI element.
                const input = document.getElementById("pac-input");
                const searchBox = new google.maps.places.SearchBox(input);
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
                // Bias the SearchBox results towards current map's viewport.
                map.addListener("bounds_changed", () => {
                    searchBox.setBounds(map.getBounds());
                });
                let markers = [];
                // Listen for the event fired when the user selects a prediction and retrieve
                // more details for that place.
                searchBox.addListener("places_changed", () => {
                    const places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }
                    // Clear out the old markers.
                    markers.forEach((marker) => {
                        marker.setMap(null);
                    });
                    markers = [];
                    // For each place, get the icon, name and location.
                    const bounds = new google.maps.LatLngBounds();
                    places.forEach((place) => {
                        if (!place.geometry || !place.geometry.location) {
                            return;
                        }
                        var mrkr = new google.maps.Marker({
                            map,
                            title: place.name,
                            position: place.geometry.location,
                        });
                        google.maps.event.addListener(mrkr, "click", function (event) {
                            document.getElementById('latitude').value = this.position.lat();
                            document.getElementById('longitude').value = this.position.lng();
                        });

                        markers.push(mrkr);

                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                });
            };
            initAutocomplete();
        });

        $('input, textarea').on('focus', function () {
            let val = $(this).val();
            $(this).val('').val(val);
        });
    </script>
@endpush
