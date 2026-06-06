<div class="product-card card product-card-clickable {{ 'product-id-' . $product->id }}"
    data-product-id="{{ $product->id }}">
    <?php
    $category_id = null;
    foreach (json_decode($product['category_ids'], true) as $cat) {
        if ($cat['position'] == 1) {
            $category_id = $cat['id'];
        }
    }

    $category_discount = \App\CentralLogics\Helpers::category_discount_calculate($category_id, $product['price']);
    $product_discount = \App\CentralLogics\Helpers::discount_calculate($product, $product['price']);

    if ($category_discount >= $product['price']) {
        $discount = $product_discount;
    } else {
        $discount = max($category_discount, $product_discount);
    }

    $cartProduct = $cartProducts->where('id', $product->id)->values();
    ?>

    <div class="card-header position-relative inline_product clickable p-0">
        @if (!empty(json_decode($product['image'], true)))
            <img src="{{ $product->identityImageFullPath[0] }}" class="w-100 h-100 object-cover aspect-ratio-80">
        @else
            <img src="{{ asset('public/assets/admin/img/160x160/2.png') }}"
                class="w-100 h-100 object-cover aspect-ratio-80">
        @endif

        <div class="hover-add-cart position-absolute">
            <button
                class="btn p-0 bg-transparent font-weight-bolder fs-16 text-nowrap text-white text-add-to-cart {{ empty($cartProduct) || $cartProduct->isEmpty() ? '' : 'd-none' }}"
                type="button">
                {{ translate('Add to cart') }}
            </button>
        </div>


        <div class="total-cart-count {{ !empty($cartProduct) && $cartProduct->isNotEmpty() ? '' : 'd-none' }}">
            <div
                class="btn p-0 bg-white fs-14 font-weight-bolder text-white w-35 h-35 rounded-circle mx-auto d-center count-product min-w-35px">
                {{ $cartProduct->sum('quantity') ?? 0 }}
            </div>
        </div>
    </div>

    <div class="card-body inline_product text-center p-1 clickable">
        <div class="product-title1 text-dark font-weight-bold">
            {{ Str::limit($product['name'], 12) }}
        </div>
        <div class="justify-content-between text-center">
            <div class="mb-2 text-center">
                @if ($discount > 0)
                    <strike class="pr-1">
                        {{ Helpers::set_symbol($product['price']) }}
                    </strike>
                @endif

                <span class="product-price">
                    {{ Helpers::set_symbol($product['price'] - $discount) }}
                </span>
            </div>
        </div>
    </div>
</div>
