$(document).on('click', '.variation_tag_setup_auto_fill', function () {
    window.ignoreChoiceChange = true;
    const $button = $(this);
    const lang = $button.data('lang');
    const route = $button.data('route');
    const name = $('#' + lang + '_name').val();
    const $textarea = $('#' + lang + '_hiddenArea');
    let description = $textarea ? $textarea.summernote('code') : '';
    const cleanDescription = description
        .replace(/<p><br><\/p>/gi, '')
        .replace(/<[^>]*>/g, '')
        .trim();
    if (!cleanDescription) {
        description = '';
    }

    const $wrapper = $('.variation_wrapper');

    if (!name) {
        toastr.error("Product name is required");
        return;
    }
    if (!description) {
        toastr.error("Product description is required");
        return;
    }

    const existingData = {};
    $wrapper.find('input, select, textarea').each(function () {
        const $field = $(this);
        const fieldName = $field.attr('name');
        if (!fieldName) return;

        if ($field.is('select[multiple]')) {
            existingData[fieldName] = $field.val() || [];
        } else if ($field.is(':checkbox')) {
            existingData[fieldName] = $field.prop('checked');
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
            const searchTags = response.data.search_tags;
            const $tagsInput = $('input[name="tags"]');

            $tagsInput.tagsinput('removeAll');
            searchTags.forEach(tag => {
                $tagsInput.tagsinput('add', tag);
            });

            $('#customer_choice_options, #variant_combination').empty();

            if (Array.isArray(response.data.choice_attributes) && response.data.choice_attributes.length > 0) {
                const selectedValues = response.data.choice_attributes.map(attr => ({
                    id: attr.id.toString(),
                    name: attr.name,
                    variation: Array.isArray(attr.variation) ? attr.variation.join(',') : ''
                }));

                setAttributeForAI(selectedValues);
                let selectedChoiceAttributes = $('#choice_attributes option:selected');
                if (selectedChoiceAttributes.length === 0) return;
                generateCombinationVariationTable(response.data.generate_variation);
                update_qty();
            }
        },
        error: function (xhr) {
            const previousData = $button.data('item');
            Object.keys(previousData).forEach(key => {
                const $field = $wrapper.find(`[name="${key}"]`);
                if (!$field.length) return;

                if ($field.is('select[multiple]')) {
                    $field.val(previousData[key]).trigger('change');

                    if (key === 'choice_attributes[]') {
                        const selectedValues = previousData[key].map(id => {
                            const $option = $field.find(`option[value="${id}"]`);
                            return {
                                id: id,
                                name: $option.text(),
                                variation: previousData[`choice_options_${id}[]`] || ''
                            };
                        });
                        $('#customer_choice_options').empty();
                        selectedValues.forEach(item => {
                            addMoreCustomerChoiceOptionWithAI(item.id, item.name, item.variation);
                        });
                    }
                } else {
                    $field.val(previousData[key]);
                }
            });

            toastr.error(xhr.responseJSON?.message || 'An unexpected error occurred.');
        },
        complete: function () {
            setTimeout(() => {
                $container.removeClass('outline-animating');
                $container.find('.bg-animate').removeClass('active');
            }, 500);

            $button.prop('disabled', false);
            $button.find('.btn-text').text('Re-generate');
            $aiText.addClass('d-none').removeClass('ai-text-animation-visible');
            window.ignoreChoiceChange = false;
            $('#analyzeImageBtn').prop('disabled', false);
            $('#analyzeImageBtn').find('.btn-text').text('Generate Product');
            $('#analyzeImageBtn').find('.ai-btn-animation').addClass('d-none');
            $('#analyzeImageBtn').find('i').removeClass('d-none');
            $('#chooseImageBtn').removeClass('disabled');

            setTimeout(function () {
                $('#aiAssistantModal').modal('hide');
            }, 1000);

            $('#aiAssistantModal').on('hidden.bs.modal', function () {
                const imageUpload = document.getElementById('aiImageUpload');
                const imagePreview = document.getElementById('imagePreview');
                imageUpload.value = '';
                imagePreview.style.display = 'none';
                $('#chooseImageBtn').find('.text-box').removeClass('d-none');

                $('.upload-image-for-generating-content').css('pointer-events', 'auto')
            });
        }
    });
});

function setAttributeForAI(selectedValues) {
    let selectedIds = selectedValues.map(item => item.id);

    $('#choice_attributes option')
        .prop('selected', false)
        .filter(function () {
            return selectedIds.includes($(this).val());
        })
        .prop('selected', true)
        .trigger('change');
    $('#customer_choice_options').empty();
    const selectedOptions = $('#choice_attributes option:selected');
    if (selectedOptions.length === 0) return;
    selectedValues.forEach(item => {
        addMoreCustomerChoiceOptionWithAI(item.id, item.name, item.variation);
    });
}

function addMoreCustomerChoiceOptionWithAI(index, name, variation) {
    let genHtml = `<div class="row g-1">
                <div class="col-md-3 col-sm-4">
                    <input type="hidden" name="choice_no[]" value="${index}">
                    <input type="text" class="form-control" name="choice[]" value="${name}" placeholder="Choice Title" readonly>
                </div>
                <div class="col-lg-9 col-sm-8">
                    <input type="text" class="form-control" name="choice_options_${index}[]" value="${variation}" placeholder="Enter choice values" data-role="tagsinput">
                </div>
            </div>`

    document.getElementById("customer_choice_options")
        .insertAdjacentHTML("beforeend", genHtml);

    document.querySelectorAll("input[data-role=tagsinput], select[multiple][data-role=tagsinput]")
        .forEach(function (input) {
            $(input).tagsinput();
        });
}

function generateCombinationVariationTable(variationData) {
    let rowsHtml = '';

    variationData.forEach((variation) => {
        let variationName = variation.option.replace(/\s+/g, '');
        rowsHtml += `<tr>
            <td>
                <label class="control-label">${variationName}</label>
            </td>
            <td>
                <input type="number" name="price_${variationName}" value="${variation.price}" min="0" step="any"
                       class="form-control" required>
            </td>
            <td>
                <input type="number" name="stock_${variationName}" value="${variation.stock}" min="0" max="1000000"
                       class="form-control" onkeyup="update_qty()" required>
            </td>
        </tr>`;
    });

    let tableHtml = `<table class="table table-bordered">
        <thead>
            <tr>
                <td class="text-center"><label class="control-label">Variant</label></td>
                <td class="text-center"><label class="control-label">Variant Price</label></td>
                <td class="text-center"><label class="control-label">Variant Stock</label></td>
            </tr>
        </thead>
        <tbody>
            ${rowsHtml}
        </tbody>
    </table>`;

    $('#variant_combination').empty().html(tableHtml)
}
