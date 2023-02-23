function appLoop(apps, resize) {
  for (let i = 0; i < apps.length; i += 1) {
    const appContWrap = apps[i];
    const appContent = appContWrap.querySelector('.read-more__content');
    appContWrap.classList.add('loaded');

    setTimeout(() => {
      if (appContent.offsetHeight > 305) {
        appContWrap.classList.add('expandable');
      } else {
        appContWrap.classList.remove('expandable');
      }

      if (!appContWrap.classList.contains('expandable')) {
        if (!appContWrap.classList.contains('expanded')) {
          if (appContent.offsetHeight > appContWrap.offsetHeight) {
            appContWrap.classList.remove('show-more');
            appContWrap.classList.add('show-more');
          } else {
            appContWrap.classList.remove('show-more');
          }
        }
      }
    }, 100);

    if (!resize) {
      const appReadMore = appContWrap.querySelector('.read-more__trigger');
      appReadMore.addEventListener('click', (event) => {
        event.preventDefault();
        if (appContWrap.classList.contains('expanded')) {
          appContWrap.classList.remove('expanded');
        } else {
          appContWrap.classList.add('expanded');
        }
      });
    }
  }
}

function debounce(func, timeout) {
  let timer;
  return function foobar(event) {
    if (timer) clearTimeout(timer);
    timer = setTimeout(func, timeout, event);
  };
}

Drupal.behaviors.readMore = {
  attach(context, settings) {
    const apps = context.querySelectorAll('.read-more__content-wrapper:not(.loaded)');
    if (apps.length !== 0) {
      appLoop(apps, false);
    }
    window.addEventListener('resize', debounce(() => {
      appLoop(apps, true);
    }, 150));
  },
};
