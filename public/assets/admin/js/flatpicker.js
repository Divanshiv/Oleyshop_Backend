
"use strict";
$(document).on('ready', function () {
    $('.js-flatpickr').each(function () {
        $.HSCore.components.HSFlatpickr.init($(this), {
        });
    });
});


$('#start_date,#end_date').change(function () {
    let fr = $('#start_date').val();
    let to = $('#end_date').val();
    if (fr != '' && to != '') {
        if (fr > to) {
            $('#start_date').val('');
            $('#end_date').val('');
            toastr.error('Invalid date range!', Error, {
                CloseButton: true,
                ProgressBar: true
            });
        }
    }
});

$("#date_type").change(function () {
    let val = $(this).val();
    $('#start_date_div').toggle(val === 'custom_date');
    $('#end_date_div').toggle(val === 'custom_date');

    if (val === 'custom_date') {
        $('#start_date').prop('required', true);
        $('#end_date').prop('required', true);
    } else {
        $('#start_date').val(null).prop('required', false)
        $('#end_date').val(null).prop('required', false)
    }
}).change();

$("#date_range").change(function () {
    let val = $(this).val();

    $('#start_date_div').toggle(val === 'custom_date');
    $('#end_date_div').toggle(val === 'custom_date');

    const $start = $('#start_date');
    const $end = $('#end_date');

    if (val === 'custom_date') {
        $start.prop('disabled', false).prop('required', true);
        $end.prop('disabled', false).prop('required', true);

        $start.css({ 'cursor': '', 'background-color': '' });
        $end.css({ 'cursor': '', 'background-color': '' });

    } else {
        $start.prop('disabled', true).prop('required', false).val(null);
        $end.prop('disabled', true).prop('required', false).val(null);

        $start.css({ 'cursor': 'not-allowed', 'background-color': '#f5f5f5' });
        $end.css({ 'cursor': 'not-allowed', 'background-color': '#f5f5f5' });
    }
}).change();




