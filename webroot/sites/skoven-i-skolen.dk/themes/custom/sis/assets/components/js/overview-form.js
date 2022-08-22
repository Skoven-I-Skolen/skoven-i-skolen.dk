import { disableBodyScroll, enableBodyScroll } from 'body-scroll-lock';

function overviewFilters(filterWrappers) {
  if (filterWrappers.length === 0) {
    return;
  }
  for (let i = 0; i < filterWrappers.length; i += 1) {
    filterWrappers[i].classList.add('loaded');
    const trigger = filterWrappers[i].querySelector('.js-accordion-item__expanadable--trigger');

    trigger.addEventListener('click', (e) => {
      filterWrappers[i].classList.toggle('expanded');
    });
  }
}

Drupal.behaviors.overviewFormFilters = {
  attach(context, settings) {
    overviewFilters(document.querySelectorAll('.accordion-item__expanadable:not(.loaded)'));
  },
};

// Mobile modal open close
const filterOpen = document.querySelector('.js-overview-form__toolbar__filter-button');
const filterWrapper = document.querySelector('.js-overview-form__search-filters');
const filterClose = document.querySelector('.js-search-filters-close');

filterOpen.addEventListener('click', (e) => {
  if (filterWrapper.classList.contains('overview-form__search-filters--active')) {
    filterWrapper.classList.remove('overview-form__search-filters--active');
  } else {
    filterWrapper.classList.add('overview-form__search-filters--active');
    disableBodyScroll(filterWrapper);
    filterWrapper.closest('section').classList.add('section--no-z-index');
  }
});
console.log(filterWrapper.closest('section'));

filterClose.addEventListener('click', (e) => {
  filterWrapper.classList.remove('overview-form__search-filters--active');
  enableBodyScroll(filterWrapper);
  filterWrapper.closest('section').classList.remove('section--no-z-index');
});
