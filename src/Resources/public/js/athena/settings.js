(function($) {
  'use strict';
  $(function() {
    //background Constants
    var body = "dark-version default";
    var $body = $("body");

    // Body Background
    $("#dark-version").on("click" , function(){
      $body.removeClass(body);
      $body.removeClass("light-version");
      $body.addClass("dark-version left-dark navbar-dark");
      $(".tiles4").removeClass("switch");
      $(this).addClass("switch");
    });
    $("#default-version").on("click" , function(){
      $body.removeClass(body);
      $body.removeClass("left-dark");
      $body.removeClass("navbar-dark");
      $body.addClass("light-version");
      $(".tiles4").removeClass("switch");
      $(this).addClass("switch");
    });
  });
})(jQuery);

// Celender
(function($) {
  'use strict';
  $(function() {

    if ($("#inline-datepicker-example").length) {
      $('#inline-datepicker-example').datepicker({
        enableOnReadonly: true,
        todayHighlight: true,
        templates: {
          leftArrow: '<i class="mdi mdi-chevron-left"></i>',
          rightArrow: '<i class="mdi mdi-chevron-right"></i>'
        }
      });
    }
  });
})(jQuery);