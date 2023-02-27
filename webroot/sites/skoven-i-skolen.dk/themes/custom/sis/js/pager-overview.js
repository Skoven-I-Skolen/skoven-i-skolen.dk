Drupal.behaviors.overview_pager_scroll = {
  attach: function (context, settings) {
    jQuery('.overview-form-contents .pager .pager__item a', context).click(function(e) {
      jQuery('.form-overview-header').get(0).scrollIntoView(true);
    });
  }
};
