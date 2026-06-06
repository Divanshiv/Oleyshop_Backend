$(document).on('ready', function () {
    $('.js-flatpickr').each(function () {
        $.HSCore.components.HSFlatpickr.init($(this));
    });
});

$('#start_date,#expire_date').change(function () {
    let fr = $('#start_date').val();
    let to = $('#expire_date').val();
    if (fr != '' && to != '') {
        if (fr > to) {
            $('#start_date').val('');
            $('#expire_date').val('');
            toastr.error('Invalid date range!', Error, {
                CloseButton: true,
                ProgressBar: true
            });
        }
    }
});

function toggleMaxAmount() {
    let selected_type = $("#discount_type").val();

    if (selected_type === 'percent') {
        $("#max_amount_div").removeClass('d-none');
        $("#maximum_amount").prop("disabled", false); // enable input
    } else {
        $("#max_amount_div").addClass('d-none');
        $("#maximum_amount").prop("disabled", true); // disable input
    }
}

// Run on load
$(document).ready(function () {
    toggleMaxAmount();

    // Run when discount_type changes
    $("#discount_type").on("change", function () {
        toggleMaxAmount();
    });
});

$(document).ready(function() {
    $('form').on('reset', function(e) {
        $("#max_amount_div").show();
    });
});
