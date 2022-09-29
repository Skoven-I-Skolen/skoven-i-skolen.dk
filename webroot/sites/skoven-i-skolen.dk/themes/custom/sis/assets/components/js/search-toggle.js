/**
 * @file
 * Search toggle.
 */

document.addEventListener('DOMContentLoaded', () => {
  const searchOverlayTriggers = document.querySelectorAll('.js-search-toggle');
  if (searchOverlayTriggers.length === 0) {
    return;
  }

  for (let i = 0; i < searchOverlayTriggers.length; i += 1) {
    searchOverlayTriggers[i].addEventListener('click', () => {
      const event = new CustomEvent('searchToggle');
      document.dispatchEvent(event);
    });
  }
});

// add event listener submit on enter
document.addEventListener('DOMContentLoaded', () => {
  const searchOverlayTriggers = document.querySelectorAll('.js-search-toggle');
  if (searchOverlayTriggers.length === 0) {
    return;
  }

  for (let i = 0; i < searchOverlayTriggers.length; i += 1) {
    searchOverlayTriggers[i].addEventListener('keydown', (e) => {
      if (e.keyCode === 13) {
        const event = new CustomEvent('searchToggle');
        document.dispatchEvent(event);
      }
    });
  }
});
