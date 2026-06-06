<button class="close fs-24px call-when-done" type="button" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>

<div class="modal-body position-relative">
    <div class="d-flex gap-2 gap-lg-3">
        <div class="modal--media-avatar">
            @if (!empty(json_decode($product['image'], true)))
                <img class="img-responsive border" src="{{ $product->identityImageFullPath[0] }}"
                    data-zoom="{{ $product->identityImageFullPath[0] }}" alt="{{ translate('Product image') }}">
            @else
                <img src="{{ asset('public/assets/admin/img/160x160/2.png') }}">
            @endif
            <div class="cz-image-zoom-pane"></div>
        </div>

        <div class="details">
            <div class="product-name mb-1"><a href="#"
                    class="fw-medium text-dark mb-2 product-title line--limit-2 ">{{ Str::limit($product->name, 100) }}</a>
            </div>
            <div class="mb-2 text-dark">
                @if ($discount > 0)
                    <strike>
                        {{ Helpers::set_symbol($product['price']) }}
                    </strike>
                @endif
                <span class="h3 font-weight-normal text-accent ml-1">
                    {{ Helpers::set_symbol($product['price'] - $discount) }}
                </span>
            </div>

            <div class="badge badge-light rounded-pill font-weight-normal">
                <span>{{ translate('Current Stock') }} : </span>
                <strong id="current-stock-count">{{ $stock }}</strong>
            </div>
            <div>
                @if ($discount > 0)
                    <div class="mt-2 bg-static-clr py-1 px-3 fs-14 text-dark d-inline-block rounded-pill">
                        {{ translate('discount')}} : <strong>{{  Helpers::set_symbol($discount) }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row pt-4">
        <div class="col-12">
            @if(!empty(trim(strip_tags($product->description))))
                <h3>{{ translate('description') }}</h3>
                <div class="overflow-y-auto max-h-300px">
                    <div class="d-block text-break __descripiton-txt __not-first-hidden">
                        <span>
                            {!! $product->description !!}
                        </span>
                        <span class="show-more text--title text-right">
                            <span>
                                {{ translate('see more') }}
                            </span>
                        </span>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
<div class="modal-footer border-0 shadow-lg d-block">
    <div id="add-to-cart-form">
        @csrf

        @foreach (json_decode($product->choice_options) as $key => $choice)
            <div class="h3 p-0 pt-2 text-break">{{ $choice->title }}</div>

            <div class="d-flex justify-content-left flex-wrap">
                @foreach ($choice->options as $key => $option)
                    <input class="btn-check variation-choice-input" type="radio" id="{{ $choice->name }}-{{ $option }}"
                        name="{{ $choice->name }}" value="{{ $option }}" @if ($key == 0) checked @endif autocomplete="off">
                    <label class="btn btn-sm check-label mx-1 choice-input"
                        for="{{ $choice->name }}-{{ $option }}">{{ $option }}</label>
                @endforeach
            </div>
        @endforeach

        <input type="hidden" name="id" value="{{ $product->id }}">
        <div id="quick-view-modal-footer">
            @include('branch-views.pos.partials._quick-view-modal-footer')
        </div>
    </div>
</div>


<script type="text/javascript">
    "use strict";

    cartQuantityInitialize();

    $('#add-to-cart-form input[type="radio"]').on('change', function () {
        getVariantPrice(true);
    });

    $('#add-to-cart-form input[name="quantity"]').on('change', function () {
        getVariantPrice();
    });

    $(document).off('click', '#add_to_cart_btn')
        .on('click', '#add_to_cart_btn', function (e) {
            e.preventDefault();
            addToCart();
        });


    $('.show-more span').on('click', function () {
        $('.__descripiton-txt').toggleClass('__not-first-hidden')
        if ($(this).hasClass('active')) {
            $('.show-more span').text('{{ translate('See More') }}')
            $(this).removeClass('active')
        } else {
            $('.show-more span').text('{{ translate('See Less') }}')
            $(this).addClass('active')
        }
    })

    $('.variation-choice-input').on('change', function () {
        $('.cart-qty-field').val('1');
    });
</script>
