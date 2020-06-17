(function($){
  $(document).ready(function($){

    let traducao = {
      firstDayOfWeek: 1,
      weekdays: {
        shorthand: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
        longhand: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"],
      }, 
      months: {
        shorthand: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
        longhand: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto","Setembro", "Outubro", "Novembro", "Dezembro"],
      },
    };
      
    $('[data-date-picker]').flatpickr({
      dateFormat: "d/m/Y",
      locale:traducao
    });

    $('[data-datetime-picker]').flatpickr({
      enableTime: true,
      enableSeconds: true,
      time_24hr: true,
      dateFormat: "d/m/Y H:i:ss",
      locale:traducao
    });

    $('[data-time-picker]').flatpickr({
      enableTime: true,
      enableSeconds: true,
      dateFormat: "H:i:s",
      time_24hr: true,
      locale:traducao
    });
  });
 
})(window.jQuery);