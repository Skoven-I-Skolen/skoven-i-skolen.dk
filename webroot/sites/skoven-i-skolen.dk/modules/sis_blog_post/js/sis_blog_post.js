(function($) {
  Drupal.behaviors.sis_blog_post = {
    attach: function(context, settings) {
      var show_all_link = document.querySelector('.view-all-writers-link');
      if (show_all_link) {
        show_all_link.addEventListener('click', function (event) {
          hideOrDisplayAllWritersRegion();
          if (show_all_link.classList.contains('hidden')) {
            show_all_link.classList.remove('hidden');
          }
          else {
            show_all_link.classList.add('hidden');
          }
        });
      }
      var hide_all_link = document.querySelector('.hide-all-writers-link');
      if (hide_all_link) {
        hide_all_link.addEventListener('click', function (event) {
          hideOrDisplayAllWritersRegion();
          var show_all_link = document.querySelector('.view-all-writers-link');
          if (show_all_link) {
            if (show_all_link.classList.contains('hidden')) {
              show_all_link.classList.remove('hidden');
            }
          }
        });
      }

      function hideOrDisplayAllWritersRegion() {
        var allWritersRegion = document.querySelector('.blog-post--view-all-writers');
        if (allWritersRegion) {
          if (allWritersRegion.classList.contains('expanded')) {
            allWritersRegion.classList.remove('expanded');
          }
          else {
            allWritersRegion.classList.add('expanded');
            var headerOffset = 180;
            var elementPosition = allWritersRegion.getBoundingClientRect().top;
            var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
            window.scrollTo({
              top: offsetPosition,
              behavior: "smooth"
            });
          }
        }
      }

    }
  };
})(jQuery);
