/**
 * @file
 * Image slideshow.
 */

import Swiper, { Autoplay, Pagination, Lazy } from 'swiper';
import 'swiper/css';
import 'swiper/css/pagination';

Drupal.behaviors.entitySlideshow = {
  attach(context, settings) {
    const entitySlideShowsWrappers = document.querySelectorAll('.js-entity-slideshow-wrapper:not(.loaded)');
    for (let i = 0; i < entitySlideShowsWrappers.length; i += 1) {
      const slideshow = entitySlideShowsWrappers[i];
      slideshow.classList.add('loaded');
      // Build slider
      if (slideshow.dataset.autoplay === 'true') {
        const swiperSlide = new Swiper(slideshow, {
          modules: [Autoplay, Pagination, Lazy],
          lazy: true,
          loop: true,
          slideClass: 'entity-slideshow-slide',
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          autoplay: {
            delay: 6000,
            disableOnInteraction: false,
          },
        });
      } else {
        const swiperSlide = new Swiper(slideshow, {
          modules: [Autoplay, Pagination, Lazy],
          lazy: true,
          loop: true,
          slideClass: 'entity-slideshow-slide',
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
        });
      }
    }
  },
};
