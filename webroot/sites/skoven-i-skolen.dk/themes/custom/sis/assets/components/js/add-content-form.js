Drupal.behaviors.addArticleForm = {
  attach(context, settings) {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach((e) => {
      if (!e.classList.contains('initialized')) {
        if (e.name === 'radio_group_2[0]') {
          // eslint-disable-next-line no-param-reassign
          e.checked = true;
        }
        e.classList.add('initialized');
        e.addEventListener('change', (ev) => {
          if (ev.target.checked) {
            checkboxes.forEach((checkbox) => {
              if (ev.target.name !== checkbox.name) {
                // eslint-disable-next-line no-param-reassign
                checkbox.checked = false;
              }
            });
          }
        });
      }
    });
  },
};
