/**
 * @file
 * Inline navigation.
 */

import Swiper, { Navigation, Lazy } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';

Drupal.behaviors.latestNews = {
  attach() {
    const slideLists = document.querySelectorAll('.news-page__latest:not(.loaded)');
    for (let i = 0; i < slideLists.length; i += 1) {
      const slideList = slideLists[i];
      slideList.classList.add('loaded');
      const sliderPagerTotal = slideList.parentNode.querySelector('.slider-pager-count').lastElementChild;
      const sliderPagerShowing = slideList.parentNode.querySelector('.slider-pager-count').firstElementChild;
      // Build slider
      const swiper = new Swiper(slideList.querySelector('.js-news-page__latest-items'), {
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
        on: {
          init() {
            sliderPagerTotal.innerHTML = this.slides.length;
            sliderPagerShowing.innerHTML = Math.round(this.width / this.slides[0].swiperSlideSize);
          },
          resize() {
            sliderPagerShowing.innerHTML = Math.round(this.width / this.slides[0].swiperSlideSize);
          },
        },
      });
    }
  },
};
