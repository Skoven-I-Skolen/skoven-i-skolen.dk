/**
 * @file
 * Inline navigation.
 */

import Swiper, { Navigation, Lazy, Pagination } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

Drupal.behaviors.relatedArticles = {
  attach() {
    const slideLists = document.querySelectorAll('.article-page__related:not(.loaded)');
    for (let i = 0; i < slideLists.length; i += 1) {
      const slideList = slideLists[i];
      slideList.classList.add('loaded');
      // Build slider
      const swiper = new Swiper(slideList.querySelector('.js-article-page__related-items'), {
        modules: [Navigation, Lazy, Pagination],
        slidesPerView: 'auto',
        lazy: true,
        watchOverflow: true,
        slideClass: 'inspiration-overview__item',
        slideVisibleClass: 'swiper-slide-visible',
        currentClass: 'swiper-slide-active',
        pagination: {
          el: '.swiper-pagination',
          type: 'fraction',
          formatFractionCurrent(number) {
            return number.toString().padStart(2, '');
          },
          formatFractionTotal(number) {
            return number.toString().padStart(2, '');
          },
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
          disabledClass: 'swiper-button-disabled',
        },
      });
    }
  },
};
