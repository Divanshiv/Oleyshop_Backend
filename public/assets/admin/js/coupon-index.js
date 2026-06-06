$("#discount_type").change(function(){
    if(this.value === 'amount') {
        $("#max_discount_div").addClass('d-none');
    }
    else if(this.value === 'percent') {
        $("#max_discount_div").removeClass('d-none');
    }
});
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

$('.coupon-type').change(function() {
    let order_type = (this.value);
    if(order_type==='first_order'){
        $('#user-limit').removeAttr('required');
        $('#limit-for-user').addClass('d-none');

        $('#customer_id').removeAttr('required',true);
        $('#customer_div').addClass('d-none');

        $('#discount_type_div').removeClass('d-none');

        $('#discount_amount_div').removeClass('d-none');
        $('#discount_amount').prop('required', true);

        $('#discount_amount').removeAttr('disabled');
    }
    else if(order_type==='customer_wise'){
        $('#user-limit').prop('required');
        $('#limit-for-user').removeClass('d-none');

        $('#customer_id').prop('required', true);
        $('#customer_div').removeClass('d-none');

        $('#discount_type_div').removeClass('d-none');

        $('#discount_amount_div').removeClass('d-none');
        $('#discount_amount').prop('required', true);

        $('#discount_amount').removeAttr('disabled');
    }
    else if(order_type==='free_delivery'){
        $('#user-limit').prop('required');
        $('#limit-for-user').removeClass('d-none');

        $('#customer_id').removeAttr('required',true);
        $('#customer_div').addClass('d-none');

        $('#discount_type_div').addClass('d-none');

        $('#discount_amount_div').addClass('d-none');
        $('#discount_amount').removeAttr('required');
        $('#discount_amount').prop('disabled', true);

    }
    else{
        $('#user-limit').prop('required',true);
        $('#limit-for-user').removeClass('d-none');

        $('#customer_id').removeAttr('required',true);
        $('#customer_div').addClass('d-none');

        $('#discount_type_div').removeClass('d-none');

        $('#discount_amount_div').removeClass('d-none');
        $('#discount_amount').prop('required', true);

        $('#discount_amount').removeAttr('disabled');
    }
})

$('.generate-code').on('click', function (){
    let code = Math.random().toString(36).substring(2,12);
    $('#code').val(code)
})
