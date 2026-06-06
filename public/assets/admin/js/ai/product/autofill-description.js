$(document).on('click', '.auto_fill_description', function () {
    const $button = $(this);
    const lang = $button.data('lang');
    const route = $button.data('route');
    const $nameInput = $('#' + lang + '_name');
    const name = ($nameInput.val() || '').trim();
    const $editorContainer = $('#editor-container-' + lang);
    const $textarea = $('#' + lang + '_hiddenArea');

    if (name.length === 0) {
        toastr.error("Product name is required to generate description");
        return;
    }


    let $existingDescription = $textarea.summernote('code') ?? '';

    $editorContainer.addClass('outline-animating');
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
            langCode: lang
        },
        success: function (response) {
            $textarea.summernote('code', response.data);

            if ($button.data('next-action')?.toString() === 'category-setup') {
                const $card = $('.card:has('+ '.category_setup_auto_fill'+ ')');
                if ($card.length > 0) {
                    $('html, body').animate({
                        scrollTop: $card.offset().top - 100
                    }, 800);
                }
            }
        },
        error: function (xhr, status, error) {

            $textarea.summernote('code', $existingDescription);

            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('An unexpected error occurred.');
            }
        },
        complete: function () {
            if ($button.data('next-action')?.toString() === 'category-setup') {
                setTimeout(function () {
                    const target = document.querySelector('.category_setup_auto_fill');
                    if (target) {
                        target.setAttribute('data-next-action', 'price-setup');
                        target.click();
                    }
                }, 2000);
            }

            $button.removeAttr('data-next-action');
            $button.removeData('next-action');
            if ($button[0] && $button[0].dataset) {
                delete $button[0].dataset.nextAction;
            }

            setTimeout(function () {
                $editorContainer.removeClass('outline-animating');
            }, 500);

            $button.prop('disabled', false);
            $button.find('.btn-text').text('Re-generate');
            $aiText.addClass('d-none').removeClass('ai-text-animation-visible');
        }
    });
});
