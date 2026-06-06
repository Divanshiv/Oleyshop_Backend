<div class="d-flex justify-content-between align-items-center flex-wrap text-dark mb-3" id="chosen_price_div">
    <div class="product-description-label">{{ translate('Total Amount') }}:</div>
    <div class="product-price text-primary fs-18 fw-medium">
        <strong id="chosen_price">{{ number_format(num: $price, decimals: 2, thousands_separator: '') }}</strong>
        {{ Helpers::currency_symbol() }}
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <div class="product-quantity d-flex align-items-center">
            <div class="align-items-center d-flex gap-3">
                <span class="input-group-btn p-0">
                    <button class="btn btn--reset btn-number p-2 rounded-circle text-dark" type="button" id="minus_btn"
                        data-type="minus" data-field="quantity" {{$quantity <= 1 ? "disabled" : ""}}>
                        <i class="tio-remove font-weight-bold"></i>
                    </button>
                </span>

                <input type="hidden" id="check_max_qty" value="{{ $stock }}">

                <input type="text" name="quantity" id="quantity"
                    class="form-control h-auto input-number text-center cart-qty-field w-65px" placeholder="1"
                    value="{{  $quantity }}" min="1" max="{{ $stock }}">

                <span class="input-group-btn p-0">
                    <button class="btn btn--reset btn-number p-2 rounded-circle text-dark" type="button"
                        data-type="plus" id="plus_btn" data-field="quantity">
                        <i class="tio-add  font-weight-bold"></i>
                    </button>
                </span>
            </div>
        </div>

        <div class="warning_popup_wrapper">
            <div class="warning_popup rounded-lg p-3 d-none">
                <button type="button" class="close fs-24px close_warning_popup" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <div class="d-flex g-2 align-items-center">
                    <img src="{{ asset('public/assets/admin/img/warning.png') }}" alt="">
                    <div>
                        <h4 class="fw-normal">{{ translate('warning') }}</h4>
                        <p class="stock-validation-message">
                            {{ translate('There isn’t enough quantity on stock.') }}
                            {{ translate('Only') }} <strong class="product-stock-count">{{ $stock }}</strong>
                            {{ translate('items are available.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-primary add-to-btn h-auto px-lg-5" id="add_to_cart_btn" type="{{ $stock > 0 ? "button" : "submit" }}" {{ $stock > 0 ? "" : "disabled" }}>
        <i class="tio-shopping-cart"></i>
        {{ $stock > 0 ? $buttonText : translate("Out Of Stock") }}
    </button>
</div>
