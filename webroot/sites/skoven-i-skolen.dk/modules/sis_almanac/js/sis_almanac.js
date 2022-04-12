(function($) {
  Drupal.behaviors.sisAlmanak = {
    attach: function(context, settings) {
      $("a.js-almanac__list-navigation-left").click(function() {
        var $that = $(this);
        if ($that.attr('disabled') === 'disabled') {
          return false;
        }
        $that.parent().find('.js-almanac__list-navigation-right').removeAttr('disabled');
        $that.attr('disabled', 'disabled');
        $(this).parent().prev().find(".almanac__list-item--active").fadeOut('fast', function() {
          $(this).prev().fadeIn('fast', function() {
            $(this).next().removeClass('almanac__list-item--active');
            if ($(this).is(':first-child')) {
              var day = $(this).children(".almanac__list-item-title").attr('almanac-day');
              var month = $(this).children(".almanac__list-item-title").attr('almanac-month');
              $.getJSON('/sis/almanac/get/before/' + day + '/' + month, function(json) {
                var $html = $(json.data);
                $("ul.almanac__list").prepend('<li class="almanac__list-item">' + $html[0] + '</li>');
                $that.removeAttr('disabled');
              });
            }
            else {
              $that.removeAttr('disabled');
            }
          }).addClass('almanac__list-item--active');
        });
      });

      $("a.js-almanac__list-navigation-right").click(function() {
        var $that = $(this);
        if ($that.attr('disabled') === 'disabled') {
          return false;
        }
        $that.parent().find('.js-almanac__list-navigation-left').removeAttr('disabled');
        $that.attr('disabled', 'disabled');
        $(this).parent().prev().find(".almanac__list-item--active").fadeOut('fast', function() {
          $(this).next().fadeIn('fast', function() {
            $(this).prev().removeClass('almanac__list-item--active');
            if ($(this).is(':last-child')) {
              var day = $(this).children(".almanac__list-item-title").attr('almanac-day');
              var month = $(this).children(".almanac__list-item-title").attr('almanac-month');
              $.getJSON('/sis/almanac/get/after/' + day + '/' + month, function(json) {
                var $html = $(json.data);
                $("ul.almanac__list").append('<li class="almanac__list-item">' + $html[0] + '</li>');
                $that.removeAttr('disabled');
              });
            }
            else {
              $that.removeAttr('disabled');
            }
          }).addClass('almanac__list-item--active');
        });
      });
    }
  };
})(jQuery);
