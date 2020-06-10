(function($) {
    $(document).ready(function() {
        $('.btn-multiselect').click(function() {
            $(this).parent().find('.dropdown-menu').toggleClass('show');
        })
    })
})(jQuery)
