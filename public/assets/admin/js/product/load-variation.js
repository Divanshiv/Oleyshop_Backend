window.ignoreChoiceChange = false;

$('#choice_attributes').on('change', function () {
    $('#customer_choice_options').html(null);
    $('#variant_combination').empty();
    $.each($("#choice_attributes option:selected"), function () {
        add_more_customer_choice_option($(this).val(), $(this).text());
    });
});

function add_more_customer_choice_option(i, name) {
    let n = name.split(' ').join('');
    $('#customer_choice_options').append(
        '<div class="row g-1"><div class="col-md-3 col-sm-4"><input type="hidden" name="choice_no[]" value="' +
        i + '"><input type="text" class="form-control" name="choice[]" value="' + n +
        '" placeholder="Choice Title" readonly></div><div class="col-lg-9 col-sm-8"><input type="text" class="form-control" name="choice_options_' +
        i +
        '[]" placeholder="Enter choice values" data-role="tagsinput"></div></div>'
    );
    $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
}

function combination_update() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: "POST",
        url: $('.data-to-js').data('variant-combination-route'),
        data: $('#product_form').serialize(),
        success: function(data) {
            $('#variant_combination').html(data.view);
            if (data.length > 1) {
                $('#quantity').hide();
            } else {
                $('#quantity').show();
            }
        }
    });
}

$(document).on('change', '[name^="choice_options_"]', function () {
    if (window.ignoreChoiceChange) return;
    combination_update();
    update_qty();
});

function update_qty() {
    setTimeout(() => {
        let total_qty = 0;
        const qty_elements = $('input[name^="stock_"]');
        qty_elements.each(function () {
            total_qty += parseInt($(this).val()) || 0;
        });

        if (qty_elements.length > 0) {
            $('input[name="total_stock"]').attr("readonly", true).val(total_qty);
        } else {
            $('input[name="total_stock"]').attr("readonly", false).val('');
        }
    }, 1000);
}

