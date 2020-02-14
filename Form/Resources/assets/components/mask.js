(function($){
    $(document).ready(function() {
        const hasInputWithMask = $('[data-input]')[0]

        if(!hasInputWithMask) {
            return false
        }

        $('[data-input="date-time"]').inputmask({
            mask: '99/99/9999 99:99:99',
            placeholder: '__/__/____ __:__:__',
        })

        $('[data-input="date"]').inputmask({
            alias: 'datetime',
            inputFormat: 'dd/mm/yyyy',
            placeholder: '__/__/____',
        })

        $('[data-input="time"]').inputmask({
            mask: '99:99:99',
            placeholder: '__:__:__',
        })

        $('[data-input="cpf"]').inputmask({
            mask: '999.999.999-99',
            placeholder: '___.___.___-__',
        })

        $('[data-input="cep"]').inputmask({
            mask: '99999-999',
            placeholder: '_____-___'
        })

        $('[data-input="money"]').inputmask({
            alias: 'numeric',
            groupSeparator: ',',
            digits: 2,
            digitsOptional: false,
            prefix: 'R$',
            placeholder: '0'
        })

        $('[data-input="cel"]').inputmask({
            mask: '99999-9999',
            placeholder: '____-____'
        })

        $('[data-input="number"]').inputmask({
            alias: 'decimal',
            placeholder: '0'
        })

        $('[data-input="cel_with_ddd"]').inputmask('(99) 99999-9999')

        $('[data-input="phone"]').inputmask('9999-9999')

        $('[data-input="phone_with_ddd"]').inputmask('(99) 9999-9999')

        $('[data-input="email"]').inputmask({ alias: "email"})
    })
})(jQuery)
