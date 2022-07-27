function dropdownSelect(wrapper) {
  if (wrapper.length > 0) {
    for (let i = 0; i < wrapper.length; i += 1) {
      const element = wrapper[i];
      const dropdownTrigger = element.firstElementChild;
      const dropdownMenu = element.lastElementChild;
      const selectList = element.previousElementSibling;
      element.classList.add('loaded');

      if (dropdownTrigger) {
        dropdownTrigger.addEventListener('click', (e) => {
          e.preventDefault();
          if (dropdownTrigger.classList.contains('is-selected')) {
            dropdownTrigger.classList.remove('is-selected');
            dropdownMenu.classList.remove('expanded');
          } else {
            dropdownTrigger.classList.add('is-selected');
            dropdownMenu.classList.add('expanded');
          }
        });
      }

      if (dropdownMenu) {
        for (let j = 0; j < dropdownMenu.children.length; j += 1) {
          dropdownMenu.children[j].addEventListener('click', (e) => {
            e.preventDefault();
            for (let k = 0; k < selectList.children.length; k += 1) {
              if (selectList.children[k].value === dropdownMenu.children[j].dataset.id) {
                selectList.options[k].selected = true;
                if ('createEvent' in document) {
                  const evt = document.createEvent('HTMLEvents');
                  evt.initEvent('change', false, true);
                  selectList.dispatchEvent(evt);
                } else {
                  selectList.fireEvent('onchange');
                }
              }
            }
            dropdownTrigger.classList.remove('is-selected');
            dropdownMenu.classList.remove('expanded');
            dropdownTrigger.querySelector('span').innerText = dropdownMenu.children[j].text;
          });
        }
      }
    }
  }
}

Drupal.behaviors.premiumFormSelect = {
  attach(context, settings) {
    dropdownSelect(context.querySelectorAll('.premium-dropdown--wrapper:not(.loaded)'));
  },
};