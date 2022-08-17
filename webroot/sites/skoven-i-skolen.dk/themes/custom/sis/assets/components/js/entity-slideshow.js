/**
 * @file
 * Image slideshow.
 */

import Flickity from 'flickity';
import FlickityFade from 'flickity-fade';
import FlickityAsNavFor from 'flickity-as-nav-for';
import 'flickity/css/flickity.css';

Drupal.behaviors.entitySlideshow = {
  attach(context) {
    const entitySlideShowsWrappers = document.querySelectorAll('.js-entity-slideshow-wrapper:not(.loaded)');
    if (entitySlideShowsWrappers.length === 0) {
      return;
    }

    for (let i = 0; i < entitySlideShowsWrappers.length; i += 1) {
      const current = entitySlideShowsWrappers[i];
      const slideshow = current.querySelector('.js-entity-slideshow');
      const navigation = current.querySelector('.js-entity-slideshow-navigation');
      current.classList.add('loaded');

      setTimeout(() => {
        const flktySlideshow = new Flickity(slideshow, {
          // options
          cellAlign: 'left',
          contain: true,
          pageDots: true,
          prevNextButtons: false,
          fade: true,
          wrapAround: true,
          autoPlay: current.dataset.autoplay === 'true' ? 3000 : '',
          cellSelector: '.entity-slideshow-slide',
        });

        const flktyNavigation = new Flickity(navigation, {
          // options
          contain: true,
          pageDots: false,
          prevNextButtons: false,
          asNavFor: slideshow,
        });
      });
    }
  },
};
