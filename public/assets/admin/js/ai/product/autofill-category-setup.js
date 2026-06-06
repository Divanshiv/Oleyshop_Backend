$(document).on('click', '.category_setup_auto_fill', function () {
    const $button = $(this);
    const lang = $button.data('lang');
    const route = $button.data('route');
    const $wrapper = $button.closest('.category_wrapper');
    const $textarea = $('#' + lang + '_hiddenArea');
    let description = $textarea ? $textarea.summernote('code') : '';
    const cleanDescription = description
        .replace(/<p><br><\/p>/gi, '')
        .replace(/<[^>]*>/g, '')
        .trim();
    if (!cleanDescription) {
        description = '';
    }
    const name = $('#' + lang + '_name').val();

    if (!name) {
        toastr.error("Product name is required");
        return;
    }

    const existingData = {};
    $wrapper.find('input, select, textarea').each(function () {
        const $field = $(this);
        const fieldName = $field.attr('name');
        if (!fieldName) return;
        if ($field.is('select[multiple]')) {
            existingData[fieldName] = $field.val() || [];
        } else {
            existingData[fieldName] = $field.val();
        }
    });

    $button.data('item', existingData);

    const $container = $wrapper.find('.outline-wrapper');
    $container.addClass('outline-animating');
    $container.find('.bg-animate').addClass('active');
    $button.prop('disabled', true);
    $button.find('.btn-text').text('');
    const $aiText = $button.find('.ai-text-animation');
    $aiText.removeClass('d-none').addClass('ai-text-animation-visible');


    $.ajax({
        url: route,
        type: 'GET',
        dataType: 'json',
        data: {
            name: name,
            description: description,
        },
        success: function (response) {
            let data = response.data;
            if (data.category_id) $('#category_id').val(data.category_id).trigger('change');

            if (data.sub_category_id) {
                setTimeout(() => $('#sub-categories').val(data.sub_category_id).trigger('change'), 1000);
            }

            if (data.unit_name) $('#unit').val(data.unit_name).trigger('change');

            if (data.quantity) $('#capacity').val(data.quantity);
            if (data.maximum_order_quantity) $('#maximum_order_quantity').val(data.maximum_order_quantity);
            $('#weight').val(data.weight);

            if ($button.data('next-action')?.toString() === 'price-setup') {
                const $card = $('.card:has('+ '.price_others_auto_fill'+ ')');
                if ($card.length > 0) {
                    $('html, body').animate({
                        scrollTop: $card.offset().top - 100
                    }, 800);
                }
            }
        },
        error: function (xhr, status, error) {

            const previousData = $button.data('item');
            Object.keys(previousData).forEach(key => {
                const $field = $wrapper.find(`[name="${key}"]`);
                if ($field.length) {
                    $field.val(previousData[key]);
                }
            });

            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('An unexpected error occurred.');
            }
        },
        complete: function () {
            if ($button.data('next-action')?.toString() === 'price-setup') {
                setTimeout(function () {
                    const target = document.querySelector('.price_others_auto_fill');
                    if (target) {
                        target.setAttribute('data-next-action', 'variation-tag-setup');
                        target.click();
                    }
                }, 2000);
            }

            $button.removeAttr('data-next-action');
            $button.removeData('next-action');
            if ($button[0] && $button[0].dataset) {
                delete $button[0].dataset.nextAction;
            }

            setTimeout(() => {
                $container.removeClass('outline-animating');
                $container.find('.bg-animate').removeClass('active');
            }, 500);

            $button.prop('disabled', false);
            $button.find('.btn-text').text('Re-generate');
            $aiText.addClass('d-none').removeClass('ai-text-animation-visible');
        }
    });
});
