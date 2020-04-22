
(function($, moment){

    $("[data-input='date-time']").change(function() {
        $(this).next('input:hidden').val(
            moment(
                $(this).val(), 
                $(this).data('input-format-client')
            ).format($(this).data('input-format-server'))
        )
    }).each(function() {
        $(this).val(
            moment(
                $(this).next('input:hidden').val(), 
                $(this).data('input-format-server')
            ).format($(this).data('input-format-client'))
        )
    })


    $("[data-input='date']").change(function() {
        $(this).next('input:hidden').val(
            moment(
                $(this).val(), 
                $(this).data('input-format-client')
            ).format($(this).data('input-format-server'))
        )
    }).each(function() {
        $(this).val(
            moment(
                $(this).next('input:hidden').val(), 
                $(this).data('input-format-server')
            ).format($(this).data('input-format-client'))
        )
    })


})(jQuery, moment)
