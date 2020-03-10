(function($, axios) {
    const $form = $('#meuForm')
    const $inputMethod = document.querySelector('[data-listing="methods"]')
    const $checkboxs = $form.find('input.listing-checkboxes')

    function handleActionClick(e) {
        e.preventDefault()

        const btnAction = this.getAttribute('btn-action-field')
        const btnMethod = this.getAttribute('btn-method')
        const btnUrl = this.getAttribute('btn-url')
        let ids = ''

        const selecteds = getCheckeds()

        if(btnAction === 'inserir') {
            window.location.href = btnUrl
            return
        }

        if(!selecteds.length) {
            alert('Selecione no minimo 1 item da listagem')
            return
        }

        const querys = new URLSearchParams(window.location.search)

        querys.delete('operators[]')
        querys.delete('terms[]')
        querys.delete('fields[]')

        if(!querys.has('redir')) {
            querys.set('redir', window.location.pathname)
        }

        if(selecteds.length > 1) {
            ids = `ids=${selecteds.join(',')}&`
        }

        if(btnAction === 'editar') {
            window.location.href = `${window.location.pathname}/${selecteds[0]}/editar?${ids}${querys.toString()}`
            return
        }

        if(btnAction === 'excluir') {
            modalExcluir(selecteds)
            return
        }

        $form.action = btnUrl
        $form.method = btnMethod
        $form.submit()
    }

    function modalExcluir() {
        $('[data-excluir="abrirModal"]').click()
    }

    function handleConfirm() {
        const btnExcluir = $('[btn-action-field="excluir"]')
        $('[data-excluir="cancel"]').click()

        const selecteds = getCheckeds()

        if(!selecteds) {
            alert('Selecione no minimo 1 item da listagem')
            return
        }

        $('[data-container="loading"]').html(`
            <div class="loading-container fixed">
                <div class="lds-roller">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        `)

        axios.post(btnExcluir.attr('btn-url'), {
            _token: $('[name="_token"]').val(),
            _method: 'DELETE',
            item: getCheckeds()
        })
        .then(function(res) {
            const data = res.data
            $('[data-container="loading"]').html('')

            if(data.errors.length) {
                alert('Não foi possivel excluir todos os items selecionados')
                window.location.reload()
                return
            }
            data.success.forEach(function(id) {
                document.querySelector(`input[value="${id}"]`).parentNode.parentNode.remove()
            })

        })
        .catch(function(err) {
            $('[data-container="loading"]').html('')
            alert('Falha ao excluir os items, entre em contato com suporte')
        })
    }

    function handleChange() {
        if(this.checked) {
            $(this.parentNode.parentNode).addClass('active')
            return
        }

        $(this.parentNode.parentNode).removeClass('active')
    }

    function handleBuscaAvancada() {
        $('#formBuscaAvaçada').submit()
    }

    function handleDblClick() {
        const $item = $(this)

        $item
            .find('input[type="checkbox"]:first')
            .prop('checked', false)
            .parents('tr')
            .removeClass('active')

        $item
            .addClass('active')
            .find('input[type="checkbox"]:first')
            .prop('checked', 'checked')

        if ($('#psListing [btn-action-field="visualizar"]').length > 0){
            $('#psListing [btn-action-field="visualizar"]').click();
        } else if ($('#psListing [btn-action-field="editar"]').length > 0){
            $('#psListing [btn-action-field="editar"]').click();
        }
    }

    function getCheckeds() {
        if(!$checkboxs) return

        const ids = Array.from($checkboxs).filter(function($item) {
            return $item.checked
        }).map(function($item) {
            return $item.value
        })

        return ids.length > 0 ? ids : false
    }

    const search = new URLSearchParams(window.location.search)
    Array.from(document.querySelectorAll('[data-paginate]'))
        .forEach(function($pag) {
            const query = new URLSearchParams($pag.href)
            search.set('pp', search.get('pp') || query.get('pp') || '')
            search.set('page', query.get('page') || '1')
            $pag.href = '?' + search.toString()
        })

    $('[btn-action-field]').click(handleActionClick)
    $('input.listing-checkboxes').change(handleChange)
    $('[data-excluir="confirm"]').click(handleConfirm)
    $('#listagemTable').checkboxes('range', true)
    $('[data-avancada="buscar"]').click(handleBuscaAvancada)
    $('#listagemTable tbody tr').dblclick(handleDblClick)

    $checkboxs.each(function(idx, $item) {
        $item.checked = false
    })

})(jQuery, axios)
