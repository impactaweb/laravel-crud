
(function($, moment){

    $("[data-input='date-time']").change(function() {
        $(this).next('input:hidden').val(
            moment(
                $(this).val(), 
                $(this).data('input-format')
            ).format("YYYY-MM-DD hh:mm:ss")
        )
    }).each(function() {
        $(this).val(
            moment(
                $(this).next('input:hidden').val(), 
                "YYYY-MM-DD hh:mm:ss"
            ).format($(this).data('input-format'))
        )
    })


    $("[data-input='date']").change(function() {
        $(this).next('input:hidden').val(
            moment(
                $(this).val(), 
                $(this).data('input-format')
            ).format("YYYY-MM-DD")
        )
    }).each(function() {
        $(this).val(
            moment(
                $(this).next('input:hidden').val(), 
                "YYYY-MM-DD"
            ).format($(this).data('input-format'))
        )
    })


})(jQuery, moment)
