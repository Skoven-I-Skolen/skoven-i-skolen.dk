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
      const sliderPagerTotal = slideList.querySelector('.slider-pager-count--total');
      const sliderPagerShowing = slideList.querySelector('.slider-pager-count--showing');
      const sliderNext = slideList.querySelector('.swiper-button-next');
      const sliderPrev = slideList.querySelector('.swiper-button-prev');
      // Build slider
      const swiper = new Swiper(slideList.querySelector('.js-article-page__related-items'), {
        modules: [Navigation, Lazy],
        slidesPerView: 'auto',
        lazy: true,
        watchOverflow: true,
        slideClass: 'inspiration-overview__item',
        slideVisibleClass: 'swiper-slide-visible',
        navigation: {
          nextEl: sliderNext,
          prevEl: sliderPrev,
          disabledClass: 'swiper-button-disabled',
        },
        on: {
          init() {
            const slidesShowing = Math.round(this.width / this.slides[0].swiperSlideSize);
            sliderPagerTotal.innerHTML = this.slides.length;
            if (this.slides.length < slidesShowing) {
              sliderPagerShowing.innerHTML = this.slides.length;
            } else {
              sliderPagerShowing.innerHTML = slidesShowing;
            }
          },
          resize() {
            const slidesShowing = Math.round(this.width / this.slides[0].swiperSlideSize);
            if (this.slides.length < slidesShowing) {
              sliderPagerShowing.innerHTML = this.slides.length;
            } else {
              sliderPagerShowing.innerHTML = slidesShowing;
            }
          },
        },
      });
    }
  },
};
