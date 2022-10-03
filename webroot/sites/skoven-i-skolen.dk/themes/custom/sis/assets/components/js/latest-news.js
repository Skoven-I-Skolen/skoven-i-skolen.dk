/**
 * @file
 * Inline navigation.
 */

import Swiper, { Navigation, Lazy, Pagination } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

Drupal.behaviors.latestNews = {
  attach() {
    const slideLists = document.querySelectorAll('.news-page__latest:not(.loaded)');
    for (let i = 0; i < slideLists.length; i += 1) {
      const slideList = slideLists[i];
      slideList.classList.add('loaded');
      // Build slider
      const swiper = new Swiper(slideList.querySelector('.js-news-page__latest-items'), {
        modules: [Navigation, Lazy, Pagination],
        slidesPerView: 'auto',
        lazy: true,
        watchOverflow: true,
        slideClass: 'inspiration-overview__item',
        slideVisibleClass: 'swiper-slide-visible',
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
