(function($, axios) {
    const $form = $('#meuForm')
    const $inputMethod = document.querySelector('[data-listing="methods"]')
    const $checkboxs = $form.find('input.listing-checkboxes')

    function handleActionClick(e) {
        e.preventDefault()

        const btnAction = this.getAttribute('btn-action-field')
        const btnMethod = this.getAttribute('btn-method')
        let ids = ''
        
        // Pega o path e remove todos "/" do final da string
        let pathname = window.location.pathname.replace(/\/+$/,'')

        const selecteds = getCheckeds()

        if (btnAction === 'inserir') {
            window.location.href = `${pathname}/criar?redir=${encodeURIComponent(pathname + window.location.search)}`
            return
        }

        if (!selecteds.length) {
            alert('Selecione no minimo 1 item da listagem')
            return
        }

        if (selecteds.length > 1) {
            ids = `ids=${selecteds.join(',')}&`
        }

        if (btnAction === 'editar') {
            window.location.href = `${pathname}/${selecteds[0]}/editar?${ids}&redir=${encodeURIComponent(pathname + window.location.search)}`
            return
        }

        if (btnAction === 'excluir') {
            modalExcluir(selecteds)
            return
        }

        $form.action = this.getAttribute('btn-url')
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
                window.location.reload()
                // document.querySelector(`input[value="${id}"]`).parentNode.parentNode.remove()
            })

        })
        .catch(function(err) {
            $('[data-container="loading"]').html('')
            alert('Falha ao excluir os items, entre em contato com suporte')
        })
    }

    function handleCheckboxChange(e) {
        if(this.checked) {
            $(this.parentNode.parentNode).addClass('active')
            return
        }

        $(this.parentNode.parentNode).removeClass('active')
    }

    function handleBuscaAvancada() {
        $('#formBuscaAvaçada').submit()
    }

    function handleTdClick() {
        if ($('input.listing-checkboxes',this).length) {
            e.preventDefault()
            return 
        }

        const $checkbox = $(this).parents('tr').find('.listing-checkboxes:first')
        if ($checkbox.is(':checked')) {
            $checkbox.prop('checked', false)
            $(this).parents('tr').removeClass('active')
        } else {
            $checkbox.prop('checked', 'checked')
            $(this).parents('tr').addClass('active')
        }
    }


    function handleDblClick() {
        const $item = $(this)

        $('.listing-checkboxes').prop('checked', false)
            .parents('tr')
            .removeClass('active')

        $item
            .addClass('active')
            .find('.listing-checkboxes')
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

    // Função para atualizar a flag de um registro:
    function handleListingFlag() {
        
        let id          = $(this).data('id');
        let field       = $(this).data('field');
        let currentFlag = $(this).data('current-flag');
        let flagTextOn  = $(this).data('flag-text-on');
        let flagTextOff = $(this).data('flag-text-off');

        // loading class :
        $(this).addClass('listing_loading');
        
        $.ajax({
            type: "GET",
            url: window.location.href,
            context: this,
            data: {
                'listingAction': 'flag', 
                'id': id, 
                'field': field, 
                'currentFlag': currentFlag
            },
            success: function(data, textStatus, xhr) {
                if(xhr.status == 200) {
                    // stamos a nova flag
                    if (currentFlag == '0') {
                        currentFlag = '1';
                        $(this).removeClass('listing_off');
                        $(this).addClass('listing_on');
                    } else {
                        currentFlag = '0';
                        $(this).removeClass('listing_on');
                        $(this).addClass('listing_off');
                    }
                    // alteramos o valor no atributo:
                    $(this).data('current-flag', '' + currentFlag);

                    // alteramos o texto
                    let text = flagTextOff;
                    // se a flag originalmente era 0, então voltou como 1:
                    if (currentFlag == '1') { 
                        text = flagTextOn;
                    }
                    $(this).html(text);
                }
            },
            error: function(err) {
                console.log(err);
            },
            complete: function() {
                $(this).removeClass('listing_loading');
            }
        });
    }

    function handleAllChecked() {
        if ($('[name="checkbox-listing"]').is(':checked')) {
            $('.listing-checkboxes')
                .prop('checked', 'checked')
                .parents('tr')
                .addClass('active');

        } else {
            $('.listing-checkboxes').prop('checked', false)
            .parents('tr')
            .removeClass('active')
        }

    }

    /*
    const search = new URLSearchParams(window.location.search)
    Array.from(document.querySelectorAll('[data-paginate]'))
        .forEach(function($pag) {
            const query = new URLSearchParams($pag.href)
            search.set('pp', search.get('pp') || query.get('pp') || '')
            search.set('page', query.get('page') || '1')
            $pag.href = '?' + search.toString()
        })
    */


    $('[btn-action-field]').click(handleActionClick)
    $('input.listing-checkboxes').change(handleCheckboxChange)
    $('[data-excluir="confirm"]').click(handleConfirm)
    $('#listagemTable').checkboxes('range', true)
    $('[data-avancada="buscar"]').click(handleBuscaAvancada)
    $('#listagemTable tbody tr:not(.empty) td').click(handleTdClick)
    $('#listagemTable tbody tr:not(.empty)').dblclick(handleDblClick)
    $('.listing_flag').click(handleListingFlag)    
    $('input[name="checkbox-listing"]').click(handleAllChecked)

    $checkboxs.each(function(idx, $item) {
        $item.checked = false
    })
    
    // Ativar tooltip para todos os actions
    if (!($('[data-toggle="tooltip"]:first').data && $('[data-toggle="tooltip"]:first').data('bs.tooltip'))) {
        $('[data-toggle="tooltip"]').tooltip()
    }
    
    
})(jQuery, axios)

