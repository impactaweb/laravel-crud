(function ($) {
    $(document).ready(function () {
          const hasInputWithMask = $("[data-input]")[0];

          if (!hasInputWithMask) {
          return false;
          }

          Inputmask({
              mask: '99/99/9999',
              placeholder: '__/__/____'
          }).mask(document.querySelectorAll('[data-input="date"]'));

          Inputmask({
              mask: '99/99/9999 99:99:99',
              placeholder: '__/__/____ __:__:__'
          }).mask(document.querySelectorAll('[data-input="date-time"]'))

          Inputmask({
              mask: '99:99:99',
              placeholder: '__:__:__'
          }).mask(document.querySelectorAll('[data-input="time"]'));

          Inputmask({
              mask: '999.999.999-99',
              placeholder: '___.___.___-__'
          }).mask(document.querySelectorAll('[data-input="cpf"]'));

          Inputmask({
              mask: '99999-999',
              placeholder: '_____-___',
          }).mask(document.querySelectorAll('[data-input="cep"]'));

          Inputmask({
              alias: 'numeric',
              groupSeparator: ',',
              digits: 2,
              digitsOptional: false,
              prefix: 'R$',
              placeholder: '0'
          }).mask(document.querySelectorAll('[data-input="money"]'));

          Inputmask({
              mask: '99999-9999',
              placeholder: '____-____',
          }).mask(document.querySelectorAll('[data-input="cel"]'));

          Inputmask({
              alias: 'numeric',
              placeholder: '0',
          }).mask(document.querySelectorAll('[data-input="number"]'));

          Inputmask({
              mask: '(99) 99999-9999',
              placeholder: '(__) _____-____',
          }).mask(document.querySelectorAll('[data-input="cel_with_ddd"]'));

          Inputmask({
              mask: '9999-9999',
              placeholder: '____-____'
          }).mask(document.querySelectorAll('[data-input="phone"]'))

          Inputmask({
              mask: '(99) 9999-9999',
              placeholder: '(__) ____-____'
          }).mask(document.querySelectorAll('[data-input="phone_with_ddd"]'))

          Inputmask({
              alias: 'email'
          }).mask(document.querySelectorAll('[data-input="email"]'));
    });
  })(window.jQuery);
