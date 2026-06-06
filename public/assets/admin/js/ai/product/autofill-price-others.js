$(document).on('click', '.price_others_auto_fill', function () {
    const $button = $(this);
    const lang = $button.data('lang');
    const route = $button.data('route');
    const $wrapper = $button.closest('.price_wrapper');

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

        existingData[fieldName] = $field.val();

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
            const data = response.data || {};
            if (data.discount_type) {
                let discountType = data.discount_type;
                $('#discount_type').val(discountType).trigger('change');
            }

            if (typeof data.discount_amount !== 'undefined' && data.discount_amount !== null) {
                $('#discount').val(data.discount_amount);
            }
            if (typeof data.current_stock !== 'undefined' && data.current_stock !== null) {
                $('#total_stock').val(data.current_stock);
            }

            if (typeof data.unit_price !== 'undefined' && data.unit_price !== null) {
                $('#price').val(data.unit_price);
            }

            if (data.tax_type) {
                let taxType = data.tax_type;
                $('#tax_type').val(taxType).trigger('change');
            }

            if (typeof data.tax !== 'undefined' && data.tax !== null) {
                $('#tax').val(data.tax);
            }

            if ($button.data('next-action')?.toString() === 'variation-tag-setup') {
                const $card = $('.card:has('+ '.variation_tag_setup_auto_fill'+ ')');
                if ($card.length > 0) {
                    $('html, body').animate({
                        scrollTop: $card.offset().top - 100
                    }, 800);
                }
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);

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
            if ($button.data('next-action')?.toString() === 'variation-tag-setup') {
                setTimeout(function () {
                    const target = document.querySelector('.variation_tag_setup_auto_fill');
                    if (target) {
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
