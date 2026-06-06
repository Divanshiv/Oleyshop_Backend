@extends('layouts.admin.app')

@section('title', translate('Review List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/public/assets/admin/css/lightbox.min.css') }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/review.png') }}" class="w--24" alt="">
                </span>
                <span>
                    {{ translate('product reviews') }} <span
                        class="badge badge-pill badge-soft-secondary">{{ $reviews->total() }}</span>
                </span>
            </h1>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header flex-wrap gap-2 border-0">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control h-30"
                                    placeholder="{{ translate('Search by ID or Name') }}" aria-label="Search"
                                    value="{{ $search }}" autocomplete="off">

                                <div class="input-group-append h-30">
                                    <button type="submit" class="input-group-text title-bg3 p-2 text-white">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive datatable-custom">
                        <table
                            class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('#') }}</th>
                                    <th>{{ translate('product name') }}</th>
                                    <th>{{ translate('ratings') }}</th>
                                    <th>{{ translate('customer info') }}</th>
                                    <th>{{ translate('status') }}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach ($reviews as $key => $review)
                                    <tr>
                                        <td>{{ $reviews->firstItem() + $key }}</td>
                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                @if ($review->product)
                                                    @if (!empty(json_decode($review->product['image'], true)))
                                                        <a href="{{ route('admin.product.view', [$review['product_id']]) }}"
                                                            class="short-media">
                                                            <img src="{{ $review->product->identityImageFullPath[0] }}">
                                                            <div class="text-cont line--limit-2 max-150px">
                                                                {{ $review->product['name'] }}
                                                            </div>
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="badge-pill badge-soft-dark text-muted text-sm small">
                                                        {{ translate('Product unavailable') }}
                                                    </span>
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-info">
                                                {{ $review->rating }} <i class="tio-star"></i>
                                            </span>
                                            <div class="max-200px line--limit-3">
                                                {{ $review->comment }}
                                            </div>
                                            {{-- <div class="w-100">
                                                @php
                                                    $images = [];
                                                    $attachments = json_decode($review->attachment, true) ?? [];

                                                    foreach ($attachments as $k => $item) {
                                                        if (
                                                            \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                                'review/' . $item,
                                                            )
                                                        ) {
                                                            $images[$k] = asset('storage/app/public/review/' . $item);
                                                        } else {
                                                            $images[$k] = asset(
                                                                'public/assets/admin/img/160x160/2.png',
                                                            );
                                                        }
                                                    }
                                                @endphp

                                                @foreach ($images as $attachment)
                                                    <a href="{{ $attachment }}" data-lightbox>
                                                        <img class="m-1 img-100" src="{{ $attachment }}"
                                                            alt="Review Image" width="60">
                                                    </a>
                                                @endforeach
                                            </div> --}}
                                        </td>
                                        <td>
                                            @if (isset($review->customer))
                                                <a href="{{ route('admin.customer.view', [$review->user_id]) }}"
                                                    class="text-body">
                                                    <h6 class="text-capitalize short-title max-w--160px">
                                                        {{ $review->customer->f_name . ' ' . $review->customer->l_name }}
                                                    </h6>
                                                    <span>{{ $review->customer->phone }}</span>
                                                </a>
                                            @else
                                                <span class="badge-pill badge-soft-dark text-muted text-sm small">
                                                    {{ translate('Customer unavailable') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <label class="toggle-switch">
                                                <input type="checkbox" class="toggle-switch-input status-change-alert"
                                                    id="stocksCheckbox{{ $review->id }}"
                                                    data-route="{{ route('admin.reviews.status', [$review->id, $review->is_active ? 0 : 1]) }}"
                                                    data-message="{{ $review->is_active ? translate('you_want_to_disable_this_review') : translate('you_want_to_active_this_review') }}"
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

                    <div class="">
                        {!! $reviews->links('layouts/admin/partials/_pagination', ['perPage' => $perPage]) !!}
                    </div>

                    @if (count($reviews) == 0)
                        <div class="text-center p-4">
                            <img class="w-120px mb-3" src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                                alt="{{ translate('Image Description') }}">
                            <p class="mb-0">{{ translate('No_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";
        var lightbox = function(o) {
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
                y = function() {
                    return e.classList.toggle("remove-scroll");
                },
                r = function(e) {
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
                h = function(e) {
                    var t = {
                        next: e.parentElement.nextElementSibling,
                        previous: e.parentElement.previousElementSibling,
                    };
                    for (var a in t)
                        null !== t[a] && (t[a] = t[a].querySelector("[data-lightbox]"));
                    return t;
                },
                x = function(e) {
                    p.removeAttribute("style");
                    var t = h(u)[e];
                    if (null !== t)
                        for (var a in ((i.style.animation = v),
                                setTimeout(function() {
                                    i.replaceChild(r(t), i.children[0]),
                                        (i.style.animation = l);
                                }, 200),
                                u.classList.remove(g),
                                t.classList.add(g),
                                (u = t),
                                c))
                            c.hasOwnProperty(a) && (c[a].disabled = !h(t)[a]);
                },
                E = function(e) {
                    var t = e.target,
                        a = e.keyCode,
                        i = e.type;
                    ((("click" == i && -1 !== [d, s].indexOf(t)) ||
                            ("keyup" == i && 27 == a)) &&
                        d.parentElement === o.parentElement &&
                        (N("remove"),
                            (d.style.animation = v),
                            (p.style.animation = [f, v]),
                            setTimeout(function() {
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
                        l = l.map(function(e) {
                            return e + ":not([disabled])";
                        });
                        var n = (l = d.querySelectorAll(l.toString()))[0],
                            r = l[l.length - 1];
                        e.shiftKey ?
                            m.activeElement == n && (r.focus(), e.preventDefault()) :
                            (m.activeElement == r && (n.focus(), e.preventDefault()),
                                r.addEventListener("blur", function() {
                                    r.disabled && (n.focus(), e.preventDefault());
                                }));
                    }
                },
                N = function(t) {
                    ["click", "keyup", "keydown"].forEach(function(e) {
                        "remove" !== t
                            ?
                            m.addEventListener(e, function(e) {
                                return E(e);
                            }) :
                            m.removeEventListener(e, function(e) {
                                return E(e);
                            });
                    });
                };
            !(function() {
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
                        (d.onclick = function() {}),
                        d.appendChild(p),
                        "A" === o.tagName && "gallery" === o.getAttribute("data-lightbox"))
                )
                    for (var e in (d.classList.add(n),
                            (c = {
                                previous: "",
                                next: ""
                            })))
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
            function(t) {
                t.addEventListener("click", function(e) {
                    e.preventDefault(), new lightbox(t);
                });
            }
        );
    </script>
@endpush
