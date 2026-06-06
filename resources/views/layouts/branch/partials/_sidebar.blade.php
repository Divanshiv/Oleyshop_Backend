<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">
                    @php($logo = \App\Model\BusinessSetting::where(['key' => 'logo'])->first()->value)
                    <a class="navbar-brand" href="{{ route('branch.dashboard') }}" aria-label="Front">
                        <img class="w-100 side-logo"
                            src="{{ App\CentralLogics\Helpers::onErrorImage($logo, asset('storage/app/public/restaurant') . '/' . $logo, asset('public/assets/admin/img/160x160/img2.jpg'), 'restaurant/') }}"
                            alt="{{ translate('logo') }}">
                    </a>

                    <button type="button"
                        class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>

                    <div class="navbar-nav-wrap-content-left d-none d-xl-block">
                        <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                            <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                                data-placement="right" title="Collapse"></i>
                            <i class="tio-last-page navbar-vertical-aside-toggle-full-align"></i>
                        </button>
                    </div>
                </div>

                <div class="navbar-vertical-content" id="navbar-vertical-content">
                    <form class="sidebar--search-form">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="form-control form--control"
                                placeholder="{{ translate('Search Menu...') }}" id="search-sidebar-menu">
                        </div>
                    </form>
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('branch') ? 'show active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.dashboard') }}" title="{{ translate('dashboard') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('dashboard') }}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('branch/pos*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="{{ translate('POS') }}">
                                <i class="tio-shopping nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('POS') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('branch/pos*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('branch/pos') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('branch.pos.index') }}"
                                        title="{{ translate('pos') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('new Sale') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('branch/pos/orders') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('branch.pos.orders') }}"
                                        title="{{ translate('orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('orders') }}
                                            </span>
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Model\Order::where('branch_id', auth('branch')->id())->Pos()->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item nav-item_title">
                            <small class="nav-subtitle">{{ translate('order_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/verify-offline-payment*') ? 'show active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.verify-offline-payment', ['pending']) }}"
                                title="{{ translate('Verify_Offline_Payment') }}">
                                <i class="tio-shopping-basket nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Verify_Offline_Payment') }}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('branch/orders*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="{{ translate('order') }}">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('order') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('branch/orders*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('branch/orders/list/all') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('branch.orders.list', ['all']) }}"
                                        title="{{ translate('all_orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('all') }}
                                            </span>
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Model\Order::notPos()->where(['branch_id' => auth('branch')->id()])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('branch/orders/list/pending') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('branch.orders.list', ['pending']) }}"
                                        title="{{ translate('pending_orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('pending') }}
                                            </span>
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'pending', 'branch_id' => auth('branch')->id()])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('branch/orders/list/confirmed') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('branch.orders.list', ['confirmed']) }}"
                                        title="{{ translate('confirmed_orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('confirmed') }}
                                            </span>
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'confirmed', 'branch_id' => auth('branch')->id()])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('branch/orders/list/processing') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('branch.orders.list', ['processing']) }}"
                                        title="{{ translate('processing_orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('packaging') }}
                                            </span>
                                            <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'processing', 'branch_id' => auth('branch')->id()])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('branch/orders/list/out_for_delivery') ? 'active' : '' }}">
                                    <a class="nav-link "
                                        href="{{ route('branch.orders.list', ['out_for_delivery']) }}"
                                        title="{{ translate('out_for_delivery_orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('out_for_delivery') }}
                                            </span>
                                            <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'out_for_delivery', 'branch_id' => auth('branch')->id()])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('branch/orders/list/delivered') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('branch.orders.list', ['delivered']) }}"
                                        title="{{ translate('delivered_orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('delivered') }}
                                            </span>
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{ \App\Model\Order::notPos()->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('branch/orders/list/returned') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('branch.orders.list', ['returned']) }}"
                                        title="{{ translate('returned_orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('returned') }}
                                            </span>
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'returned', 'branch_id' => auth('branch')->id()])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('branch/orders/list/failed') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('branch.orders.list', ['failed']) }}"
                                        title="{{ translate('failed_orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('failed') }}
                                            </span>
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'failed', 'branch_id' => auth('branch')->id()])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('branch/orders/list/canceled') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('branch.orders.list', ['canceled']) }}"
                                        title="{{ translate('canceled_orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>
                                                {{ translate('canceled') }}
                                            </span>
                                            <span class="badge badge-soft-light badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'canceled', 'branch_id' => auth('branch')->id()])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- report_and_analytics - start -->
                        <li class="nav-item nav-item_title">
                            <small class="nav-subtitle"
                                title="Documentation">{{ translate('report_and_analytics') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/report/sale-report') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('branch.report.sale-report') }}"
                                title="{{ translate('sale') }} {{ translate('report') }}">
                                <span class="tio-chart-bar-1 nav-icon"></span>
                                <span class="text-truncate">{{ translate('Sale Report') }}</span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/report/order-report') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('branch.report.order-report') }}"
                                title="{{ translate('order') }} {{ translate('report') }}">
                                <span class="tio-chart-bar-2 nav-icon"></span>
                                <span class="text-truncate">{{ translate('Order Report') }}</span>
                            </a>
                        </li>
                        <!-- report_and_analytics - end -->
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>


@push('script_2')
    <script>
        // Active Item scrollTop show
        $(window).on('load', function () {
            if ($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        // Search
        $(document).ready(function () {
            function resetSidebar($navItems) {
                $navItems.each(function () {
                    const $el = $(this);
                    const $submenu = $el.find(".js-navbar-vertical-aside-submenu");
                    if ($submenu.length) {
                        $submenu.hide();
                        $el.removeClass("show").show();
                        $submenu.find("li").show();
                    } else {
                        $el.show();
                    }
                    const $prevTitle = $el.prevAll(".nav-item_title").first();
                    if ($prevTitle.length) $prevTitle.show();
                });
                const $active = $(".navbar-vertical-content li.active");
                if ($active.length) {
                    $active.parents("li").addClass("show")
                        .children(".js-navbar-vertical-aside-submenu").show();
                }
            }

            $(document).on("keyup", "#search-sidebar-menu", function () {
                const value = $(this).val().toLowerCase().trim();
                const $container = $(this).closest("#navbar-vertical-content");
                const $navItems = $container.find("ul.navbar-nav > li");

                if (!value) {
                    resetSidebar($navItems);
                    return;
                }

                $navItems.hide().removeClass("show");
                $navItems.find(".js-navbar-vertical-aside-submenu").hide().find("li").hide();

                $navItems.each(function () {
                    const $el = $(this);
                    const text = $el.text().toLowerCase().trim();
                    const $submenu = $el.find(".js-navbar-vertical-aside-submenu");

                    if ($el.hasClass("nav-item_title") && text.includes(value)) {
                        $el.show();

                        let $next = $el.next();
                        while ($next.length && !$next.hasClass("nav-item_title")) {
                            $next.show();
                            $next.find(".js-navbar-vertical-aside-submenu")
                                .show().find("li").show();
                            $next = $next.next();
                        }
                    }

                    if (!$el.hasClass("nav-item_title")) {
                        let isVisible = false;

                        if ($submenu.length) {
                            const $subItems = $submenu.find("li");
                            const hasSubMatch = $subItems.toArray().some(sub =>
                                $(sub).text().toLowerCase().trim().includes(value)
                            );

                            if (text.includes(value) || hasSubMatch) {
                                $subItems.show();
                                $submenu.show();
                                $el.show().addClass("show");
                                isVisible = true;
                            }
                        } else {
                            if (text.includes(value)) {
                                $el.show();
                                isVisible = true;
                            }
                        }

                        const $prevTitle = $el.prevAll(".nav-item_title").first();
                        if ($prevTitle.length && isVisible) $prevTitle.show();
                    }
                });
            });
        });
    </script>
@endpush
