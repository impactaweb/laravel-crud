module.exports = (function($){
    if($('[data-target="content"]').length){
        const $loading = `
        <div class="loading-container">
            <div class="lds-roller">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        `
        const err = 'kjdfjkdngdf'

        function getList() {
            const $paths = window.location.pathname.split('/')
            //TODO: Fazer funcionar com paginação
            const url = `${$paths.join('/')}/listagem`

            $.get(url, function (list) {
                $('[data-target="content"]').html(list);
            })
            // TODO: Fazer gargalo pra erro
            // .catch(function(err) {

            // })
        }
        $(document).ready(function () {
            $('[data-target="content"]').html($loading);
            getList()
        })
    }
})(window.jQuery)
