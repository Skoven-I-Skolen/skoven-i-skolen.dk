/**
 * @file
 * Inline navigation.
 */

import Flickity from 'flickity';
import 'flickity/css/flickity.css';

const flkty = [];

function addSlider() {
  const inlineNavigations = document.querySelectorAll('.js-inline-navigation-slider:not(.loaded)');
  if (inlineNavigations.length === 0) {
    return;
  }

  for (let i = 0; i < inlineNavigations.length; i += 1) {
    const current = inlineNavigations[i];
    current.classList.add('loaded');
    const sliderPagerTotal = current.parentNode.querySelector('.slider-pager-count').lastElementChild;
    const sliderPagerShowing = current.parentNode.querySelector('.slider-pager-count').firstElementChild;
    setTimeout(() => {
      const currentFlkty = new Flickity(current, {
        cellAlign: 'left',
        contain: true,
        cellSelector: '.inline-navigation-item',
        pageDots: false,
        on: {
          ready() {
            const sliderWidth = this.size.width;
            const slideWidth = this.slides[0].outerWidth;
            sliderPagerTotal.innerHTML = this.slides.length;
            sliderPagerShowing.innerHTML = Math.round(sliderWidth / slideWidth);
          },
        },
      });
      flkty.push(currentFlkty);
    });
  }
}

function removeSlider() {
  for (let i = 0; i < flkty.length; i += 1) {
    flkty[i].destroy();
  }
}

function checkMediaQuery(x) {
  if (x.matches) { // If media query matches
    removeSlider();
  } else {
    addSlider();
  }
}

Drupal.behaviors.inlineNavigation = {
  attach(context, settings) {
    const mediaQuery = window.matchMedia('(min-width: 768px)');
    checkMediaQuery(mediaQuery); // Call listener function at run time
    mediaQuery.addListener(checkMediaQuery); // Attach listener function on state changes
  },
};
