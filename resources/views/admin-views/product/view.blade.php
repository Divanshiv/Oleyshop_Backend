@extends('layouts.admin.app')

@section('title', translate('Product Preview'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('/public/assets/admin/css/lightbox.min.css')}}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-between">
                <h1 class="page-header-title text-break">
                    <span class="page-header-icon">
                        <img src="{{asset('public/assets/admin/img/product.png')}}" alt="">
                    </span>
                    <span>{{Str::limit($product['name'], 30)}}</span>
                </h1>
            </div>
        </div>

        <div class="row review--information-wrapper g-2 mb-2">
            <div class="col-lg-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row align-items-md-center">
                            <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
                                <div class="d-flex flex-wrap align-items-center food--media justify-content-center">
                                @if (!empty(json_decode($product['image'],true)))
                                    <img class="avatar avatar-xxl avatar-4by3 mr-4"
                                        src="{{$product->identityImageFullPath[0]}}"
                                        alt="Image Description">
                                @else
                                    <img class="avatar avatar-xxl avatar-4by3 mr-4"
                                    src="{{asset('public/assets/admin/img/160x160/img2.jpg')}}">
                                @endif

                                    <div class="d-block">
                                        <div class="rating--review">
                                            <h4 class="title">{{count($product->all_rating)>0?number_format($product->all_rating[0]->average, 2, '.', ' '):0}}</h4>
                                            <div class="rating">
                                                @php
                                                    $avg_rating = count($product->all_rating)>0?number_format($product->all_rating[0]->average, 2, '.', ' '):0;
                                                @endphp
                                                @for($i=1;$i<=5;$i++)
                                                    @if($i<=$avg_rating)
                                                        <span><i class="tio-star"></i></span>
                                                    @else
                                                        <span><i class="tio-star-outlined"></i></span>
                                                    @endif
                                                @endfor
                                            </div>
                                            <p> {{translate('of')}} {{$product->reviews->count()}} {{translate('reviews')}}
                                                <span class="badge badge-soft-dark badge-pill ml-1"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-6 mx-auto">
                                <ul class="list-unstyled list-unstyled-py-2 mb-0 rating--review-right py-3">

                                @php($total=$product->reviews->count())
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($five=\App\CentralLogics\Helpers::rating_count($product['id'],5))
                                        <span class="progress-name mr-3">{{translate('excellent')}}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                    style="width: {{$total==0?0:($five/$total)*100}}%;"
                                                    aria-valuenow="{{$total==0?0:($five/$total)*100}}"
                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{$five}}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($four=\App\CentralLogics\Helpers::rating_count($product['id'],4))
                                        <span class="progress-name mr-3">{{translate('good')}}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                    style="width: {{$total==0?0:($four/$total)*100}}%;"
                                                    aria-valuenow="{{$total==0?0:($four/$total)*100}}"
                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{$four}}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($three=\App\CentralLogics\Helpers::rating_count($product['id'],3))
                                        <span class="progress-name mr-3">{{translate('average')}}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                    style="width: {{$total==0?0:($three/$total)*100}}%;"
                                                    aria-valuenow="{{$total==0?0:($three/$total)*100}}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ml-3">{{$three}}</span>
                                    </li>

                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($two=\App\CentralLogics\Helpers::rating_count($product['id'],2))
                                        <span class="progress-name mr-3">{{translate('below_average')}}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                    style="width: {{$total==0?0:($two/$total)*100}}%;"
                                                    aria-valuenow="{{$total==0?0:($two/$total)*100}}"
                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{$two}}</span>
                                    </li>

                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($one=\App\CentralLogics\Helpers::rating_count($product['id'],1))
                                        <span class="progress-name mr-3">{{translate('poor')}}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                    style="width: {{$total==0?0:($one/$total)*100}}%;"
                                                    aria-valuenow="{{$total==0?0:($one/$total)*100}}"
                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{$one}}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3 text-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-thead-bordered product--desc-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="px-4 border-0"><h4 class="m-0 text-capitalize">{{translate('short_description')}}</h4></th>
                                <th class="px-4 border-0"><h4 class="m-0 text-capitalize">{{translate('price')}}</h4></th>
                                <th class="px-4 border-0"><h4 class="m-0 text-capitalize">{{translate('variations')}}</h4></th>
                                <th class="px-4 border-0"><h4 class="m-0 text-capitalize">{{translate('Tags')}}</h4></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="max-300">
                                        {!! $product['description'] !!}
                                    </div>
                                </td>
                                <td>
                                   <div>
                                        <strong class="text--title">{{translate('price')}} :</strong>
                                        <span>{{ Helpers::set_symbol($product['price']) }} / {{translate(''.$product['unit'])}}</span>
                                   </div>
                                   <div>
                                        <strong class="text--title">{{translate('tax')}} :</strong>
                                        <span>{{ Helpers::set_symbol(\App\CentralLogics\Helpers::tax_calculate($product,$product['price'])) }}</span>
                                   </div>
                                   <div>
                                        <strong class="text--title">{{translate('discount')}} :</strong>
                                        <span>{{ Helpers::set_symbol(\App\CentralLogics\Helpers::discount_calculate($product,$product['price'])) }}</span>
                                   </div>
                                </td>
                                <td>
                                    @foreach(json_decode($product['variations'],true) as $variation)
                                        <div class="text-capitalize">
                                            {{$variation['type']}} : {{ Helpers::set_symbol($variation['price']) }}
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($product->tags as $tag)
                                        <span class="badge-soft-success mb-1 mr-1 d-inline-block px-2 py-1 rounded" >{{$tag->tag}} </span> <br>
                                    @endforeach
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <h5 class="card-title">{{translate('product reviews')}}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive datatable-custom">
                    <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table"
                        data-hs-datatables-options='{
                        "columnDefs": [{
                            "targets": [0, 3, 6],
                            "orderable": false
                        }],
                        "order": [],
                        "info": {
                        "totalQty": "#datatableWithPaginationInfoTotalQty"
                        },
                        "search": "#datatableSearch",
                        "entries": "#datatableEntries",
                        "pageLength": 25,
                        "isResponsive": false,
                        "isShowPaging": false,
                        "pagination": "datatablePagination"
                    }'>
                        <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('reviewer')}}</th>
                            <th class="border-0">{{translate('review')}}</th>
                            <th class="border-0">{{translate('date')}}</th>
                            <th class="border-0">{{translate('status')}}</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($reviews as $review)
                            <tr>
                                <td>
                                    @if(isset($review->customer))
                                        <a class="d-flex align-items-center"
                                        href="{{route('admin.customer.view',[$review['user_id']])}}">
                                            <div class="avatar avatar-circle">
                                                <img class="avatar-img" width="75" height="75"
                                                    src="{{$review->customer->imageFullPath}}"
                                                    alt="Image Description">
                                            </div>
                                            <div class="ml-3">
                                            <span class="d-block h5 text-capitalize text-hover-primary mb-0">{{$review->customer['f_name']." ".$review->customer['l_name']}} <i
                                                    class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                                    title="Verified Customer"></i></span>
                                                <span class="d-block font-size-sm text-body">{{$review->customer->email}}</span>
                                            </div>
                                        </a>
                                    @else
                                        <span class="badge-pill badge-soft-dark text-muted text-sm small">
                                            {{\App\CentralLogics\translate('Customer unavailable')}}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-wrap max-18rem">
                                        <div class="rating">
                                            {{$review->rating}} <i class="tio-star"></i>
                                        </div>
                                        <div class="m-0 __see-more-txt-item">
                                            @if (strlen($review['comment']) > 100)
                                            <span class="__see-more-txt line--limit-3">
                                                {{$review['comment']}}
                                            </span>
                                            <div class="text-right">
                                                <span class="see__more text-info cursor-pointer">{{translate('... See more')}}</span>
                                            </div>
                                            @else
                                                {{$review['comment']}}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="w-100">
                                        <?php
                                            $images = [];
                                            $attachments = json_decode($review->attachment, true) ?? [];

                                            foreach ($attachments as $k => $item) {
                                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists('review/' . $item)) {
                                                    $images[$k] = asset('storage/app/public/review/' . $item);
                                                } else {
                                                    $images[$k] = asset('public/assets/admin/img/160x160/2.png');
                                                }
                                            }
                                        ?>

                                        @foreach($images as $attachment)
                                            <a href="{{$attachment}}" data-lightbox >
                                                <img class="m-1 img-100" src="{{ $attachment }}" alt="Review Image" width="60">
                                            </a>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span class="d-block">{{date('d-m-Y',strtotime($review['created_at']))}}</span>
                                </td>
                                <td>
                                    <label class="toggle-switch">
                                        <input type="checkbox"
                                               onclick="status_change_alert('{{ route('admin.reviews.status', [$review->id, $review->is_active ? 0 : 1]) }}', '{{ $review->is_active? translate('you want to disable this review'): translate('you want to active this review') }}', event)"
                                               class="toggle-switch-input" id="stocksCheckbox{{ $review->id }}"
                                            {{ $review->is_active ? 'checked' : '' }}>
                                        <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer border-0">
                {!! $reviews->links() !!}
            </div>
            @if(count($reviews)==0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $('.ql-hidden').hide()

        $('.see__more').on('click', function(){
            $(this).closest('.__see-more-txt-item').find('.__see-more-txt').toggleClass('line--limit-3')
            if($(this).hasClass('active')) {
                $(this).text('{{translate('...See More')}}')
                $(this).removeClass('active')
            }else {
                $(this).text('{{translate('...See Less')}}')
                $(this).addClass('active')
            }
        })


        var lightbox = function (o) {
            var s = void 0,
                c = void 0,
                u = void 0,
                d = void 0,
                i = void 0,
                p = void 0,
                m = document,
                e = m.body,
                l = "fadeIn .3s",
                v = "fadeOut .3s",
                t = "scaleIn .3s",
                f = "scaleOut .3s",
                a = "lightbox-btn",
                n = "lightbox-gallery",
                b = "lightbox-trigger",
                g = "lightbox-active-item",
                y = function () {
                    return e.classList.toggle("remove-scroll");
                },
                r = function (e) {
                    if (
                        ("A" === o.tagName && (e = e.getAttribute("href")),
                            e.match(/\.(jpeg|jpg|gif|png)/))
                    ) {
                        var t = m.createElement("img");
                        return (
                            (t.className = "lightbox-image"),
                                (t.src = e),
                            "A" === o.tagName &&
                            (t.alt = o.getAttribute("data-image-alt")),
                                t
                        );
                    }
                    if (e.match(/(youtube|vimeo)/)) {
                        var a = [];
                        return (
                            e.match("youtube") &&
                            ((a.id = e
                                .split(/v\/|v=|youtu\.be\//)[1]
                                .split(/[?&]/)[0]),
                                (a.url = "youtube.com/embed/"),
                                (a.options = "?autoplay=1&rel=0")),
                            e.match("vimeo") &&
                            ((a.id = e
                                .split(/video\/|https:\/\/vimeo\.com\//)[1]
                                .split(/[?&]/)[0]),
                                (a.url = "player.vimeo.com/video/"),
                                (a.options = "?autoplay=1title=0&byline=0&portrait=0")),
                                (a.player = m.createElement("iframe")),
                                a.player.setAttribute("allowfullscreen", ""),
                                (a.player.className = "lightbox-video-player"),
                                (a.player.src = "https://" + a.url + a.id + a.options),
                                (a.wrapper = m.createElement("div")),
                                (a.wrapper.className = "lightbox-video-wrapper"),
                                a.wrapper.appendChild(a.player),
                                a.wrapper
                        );
                    }
                    return m.querySelector(e).children[0].cloneNode(!0);
                },
                h = function (e) {
                    var t = {
                        next: e.parentElement.nextElementSibling,
                        previous: e.parentElement.previousElementSibling,
                    };
                    for (var a in t)
                        null !== t[a] && (t[a] = t[a].querySelector("[data-lightbox]"));
                    return t;
                },
                x = function (e) {
                    p.removeAttribute("style");
                    var t = h(u)[e];
                    if (null !== t)
                        for (var a in ((i.style.animation = v),
                            setTimeout(function () {
                                i.replaceChild(r(t), i.children[0]),
                                    (i.style.animation = l);
                            }, 200),
                            u.classList.remove(g),
                            t.classList.add(g),
                            (u = t),
                            c))
                            c.hasOwnProperty(a) && (c[a].disabled = !h(t)[a]);
                },
                E = function (e) {
                    var t = e.target,
                        a = e.keyCode,
                        i = e.type;
                    ((("click" == i && -1 !== [d, s].indexOf(t)) ||
                        ("keyup" == i && 27 == a)) &&
                    d.parentElement === o.parentElement &&
                    (N("remove"),
                        (d.style.animation = v),
                        (p.style.animation = [f, v]),
                        setTimeout(function () {
                            if ((y(), o.parentNode.removeChild(d), "A" === o.tagName)) {
                                u.classList.remove(g);
                                var e = m.querySelector("." + b);
                                e.classList.remove(b), e.focus();
                            }
                        }, 200)),
                        c) &&
                    ((("click" == i && t == c.next) || ("keyup" == i && 39 == a)) &&
                    x("next"),
                    (("click" == i && t == c.previous) ||
                        ("keyup" == i && 37 == a)) &&
                    x("previous"));
                    if ("keydown" == i && 9 == a) {
                        var l = ["[href]", "button", "input", "select", "textarea"];
                        l = l.map(function (e) {
                            return e + ":not([disabled])";
                        });
                        var n = (l = d.querySelectorAll(l.toString()))[0],
                            r = l[l.length - 1];
                        e.shiftKey
                            ? m.activeElement == n && (r.focus(), e.preventDefault())
                            : (m.activeElement == r && (n.focus(), e.preventDefault()),
                                r.addEventListener("blur", function () {
                                    r.disabled && (n.focus(), e.preventDefault());
                                }));
                    }
                },
                N = function (t) {
                    ["click", "keyup", "keydown"].forEach(function (e) {
                        "remove" !== t
                            ? m.addEventListener(e, function (e) {
                                return E(e);
                            })
                            : m.removeEventListener(e, function (e) {
                                return E(e);
                            });
                    });
                };
            !(function () {
                if (
                    ((s = m.createElement("button")).setAttribute(
                        "aria-label",
                        "Close"
                    ),
                        (s.className = a + " " + a + "-close"),
                        ((i = m.createElement("div")).className = "lightbox-content"),
                        i.appendChild(r(o)),
                        ((p = i.cloneNode(!1)).className = "lightbox-wrapper"),
                        (p.style.animation = [t, l]),
                        p.appendChild(s),
                        p.appendChild(i),
                        ((d = i.cloneNode(!1)).className = "lightbox-container"),
                        (d.style.animation = l),
                        (d.onclick = function () {}),
                        d.appendChild(p),
                    "A" === o.tagName && "gallery" === o.getAttribute("data-lightbox"))
                )
                    for (var e in (d.classList.add(n),
                        (c = { previous: "", next: "" })))
                        c.hasOwnProperty(e) &&
                        ((c[e] = s.cloneNode(!1)),
                            c[e].setAttribute("aria-label", e),
                            (c[e].className = a + " " + a + "-" + e),
                            (c[e].disabled = !h(o)[e]),
                            p.appendChild(c[e]));
                "A" === o.tagName &&
                (o.blur(), (u = o).classList.add(g), o.classList.add(b)),
                    o.parentNode.insertBefore(d, o.nextSibling),
                    y();
            })(),
                N();
        };

        Array.prototype.forEach.call(
            document.querySelectorAll("[data-lightbox]"),
            function (t) {
                t.addEventListener("click", function (e) {
                    e.preventDefault(), new lightbox(t);
                });
            }
        );

    </script>
@endpush

