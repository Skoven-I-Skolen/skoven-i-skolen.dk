function appLoop(resize) {
  if (document.querySelector('.read-more__content').clientHeight < 305) {
    document.querySelector('.read-more__trigger').style.display = 'none';
  }

  const apps = document.querySelectorAll('.read-more__content-wrapper');
  if (apps.length === 0) {
    return;
  }

  for (let i = 0; i < apps.length; i += 1) {
    const appContWrap = apps[i];
    const appContent = apps[i].querySelector('.read-more__content');

    if (!appContWrap.classList.contains('expandable')) {
      appContWrap.classList.add('expandable');

      if (!appContWrap.classList.contains('expanded')) {
        if (appContent.offsetHeight > appContWrap.offsetHeight) {
          apps[i].classList.remove('show-more');
          apps[i].classList.add('show-more');
        } else {
          apps[i].classList.remove('show-more');
        }
      }

      if (!resize) {
        const appReadMore = apps[i].querySelector('.read-more__trigger');
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
}

function debounce(func, timeout) {
  let timer;
  return function foobar(event) {
    if (timer) clearTimeout(timer);
    timer = setTimeout(func, timeout, event);
  };
}

document.addEventListener('DOMContentLoaded', () => appLoop(false));

window.addEventListener('resize', debounce(() => {
  appLoop(true);
}, 150));
