/**
 * @file
 * Inline navigation.
 */

import Swiper, { Navigation, Lazy } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';

Drupal.behaviors.relatedArticles = {
  attach() {
    const slideLists = document.querySelectorAll('.article-page__related:not(.loaded)');
    for (let i = 0; i < slideLists.length; i += 1) {
      const slideList = slideLists[i];
      slideList.classList.add('loaded');
      // Build slider
      const swiper = new Swiper(slideList.querySelector('.js-article-page__related-items'), {
        modules: [Navigation, Lazy],
        slidesPerView: 'auto',
        lazy: true,
        watchOverflow: true,
        slideClass: 'inspiration-overview__item',
        slideVisibleClass: 'swiper-slide-visible',
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
          disabledClass: 'swiper-button-disabled',
        },
      });
    }
  },
};
