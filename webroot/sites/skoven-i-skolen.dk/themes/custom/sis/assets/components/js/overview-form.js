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
