// get modal element by class name
const modal = document.getElementsByClassName('js-modal-article');
// get modal open button
const modalOpen = document.getElementsByClassName('js-article-modal-button-open');
const modalClose = document.getElementsByClassName('js-modal-article__content__header__close-button');
console.log(modalOpen);

// body
const body = document.getElementsByTagName('body')[0];

// add event listener
if (modalOpen.length > 0) {
  modalOpen[0].addEventListener('click', () => {
    modal[0].classList.add('is-active');
    body.classList.add('is-locked');
  });
}

// close modal
if (modalClose.length > 0) {
  modalClose[0].addEventListener('click', () => {
    modal[0].classList.remove('is-active');
    body.classList.remove('is-locked');
  });
}

// close modal on esc
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    modal[0].classList.remove('is-active');
    body.classList.remove('is-locked');
  }
});
