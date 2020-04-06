(function($, axios) {
    const $form = $('#listingForm')

    $('.actionButton').click(function() {

        var url = $(this).data('url')
        var _method = $(this).data('method')
        var confirmationText = $(this).data('confirmation')
        var method = ($(this).data('method') == 'GET' ? 'GET' : 'POST')
        if (! $.inArray(method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']) == -1) {
            method = 'GET'
        }

        $checkboxes = $('.listing-checkboxes:checked')
        if (url.indexOf('{id}') >= 0 && !($checkboxes.length > 0)) {
            alert('Selecione no minimo 1 item da listagem')
            return
        }

        var ids = []
        $checkboxes.each(function(){
            ids.push($(this).val())
        })
        var id = ids[0]
        idsFormatado = ids.join(',')

        url = url.replace('{id}', id).replace('{ids}', idsFormatado)

        if (method == 'GET') {
            var continueFunction = function() {
                listagemLoading()
                window.location.href = url
            }
        } else {
            $form.prop('action', url)
            $form.prop('method', method)
            $form.find('input[name="_method"]').val(_method)
            var continueFunction = function() {
                listagemLoading()
                $form.submit()
            }
        }

        if (confirmationText.length > 0) {
            $('#confirmationModal').data('executar', continueFunction).modal('show')
            $('#confirmationModal .modal-body').html(confirmationText);
            $('#confirmationModal .btnConfirm').click(function() {
                var func = $('#confirmationModal').data('executar')
                func()
                e.preventDefault()
            })
        } else {
            continueFunction()
        }

    });

    function listagemLoading() {
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
    }

    const $checkboxs = $form.find('input.listing-checkboxes')

    function handleCheckboxChange(e) {
        if(this.checked) {
            $(this.parentNode.parentNode).addClass('active')
            return
        }
        $(this.parentNode.parentNode).removeClass('active')
    }

    function handleBuscaAvancada() {
        $('#formBuscaAvacada').submit()
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

        if ($('.actionButton[data-verb="edit"]').length > 0){
            $('.actionButton[data-verb="edit"]').trigger('click');
        }
    }

    // Funcão para atualizar a flag de um registro:
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

    $('input.listing-checkboxes').change(handleCheckboxChange)
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

    $('#listingForm th order-asc').addClass('fas fa-sort-up');
    $('#listingForm th order-desc').addClass('fas fa-sort-down');
    
    
})(jQuery, axios)

