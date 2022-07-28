/**
 * @file
 * Burger menu.
 */

import { disableBodyScroll, enableBodyScroll } from 'body-scroll-lock';

const burgerNext = () => {
  const burgerNavLevel = document.querySelectorAll('.burger-nav-item--arrow-right');
  for (let i = 0; i < burgerNavLevel.length; i += 1) {
    const target = burgerNavLevel[i];
    const targetList = target.nextElementSibling;
    const parentList = targetList.parentNode.parentNode;
    target.addEventListener('click', () => {
      targetList.classList.add('burger-nav__open');
      parentList.classList.add('burger-nav__parent');
    });
  }
};

const burgerPrev = () => {
  const burgerNavLevel = document.querySelectorAll('.burger-nav-item--arrow-left');
  for (let i = 0; i < burgerNavLevel.length; i += 1) {
    const target = burgerNavLevel[i];
    const targetList = target.parentNode.parentNode.parentNode;
    const parentList = target.parentNode;
    target.addEventListener('click', () => {
      targetList.classList.remove('burger-nav__parent');
      parentList.classList.remove('burger-nav__parent', 'burger-nav__open');
    });
  }
};

const burgerReset = () => {
  const burgerNavs = document.querySelectorAll('.burger-nav');
  setTimeout(() => {
    for (let i = 0; i < burgerNavs.length; i += 1) {
      burgerNavs[i].classList.remove('burger-nav__parent', 'burger-nav__open');
    }
    burgerNavs[0].classList.add('burger-nav__open');
  }, 300);
  enableBodyScroll(document.getElementById('burger-menu'));
};

const mobileNav = () => {
  const hamburgerIcon = document.getElementById('hamburger-icon');
  const mobileNavigation = document.getElementById('burger-menu--overlay');
  const mobileNavigationClose = document.getElementById('burger-menu--close');

  hamburgerIcon.addEventListener('click', (e) => {
    e.preventDefault();
    if (hamburgerIcon.classList.contains('hamburger-icon--open')) {
      hamburgerIcon.classList.remove('hamburger-icon--open');
      mobileNavigation.classList.remove('burger-menu--overlay__active');
      burgerReset();
    } else {
      hamburgerIcon.classList.add('hamburger-icon--open');
      mobileNavigation.classList.add('burger-menu--overlay__active');
      disableBodyScroll(document.getElementById('burger-menu'));
    }
  });

  mobileNavigation.addEventListener('click', (e) => {
    const burgerMenu = document.getElementById('burger-menu');
    if (e.offsetX > burgerMenu.offsetWidth && hamburgerIcon.classList.contains('hamburger-icon--open')) {
      hamburgerIcon.classList.remove('hamburger-icon--open');
      mobileNavigation.classList.remove('burger-menu--overlay__active');
      burgerReset();
    }
  });

  mobileNavigationClose.addEventListener('click', (e) => {
    if (hamburgerIcon.classList.contains('hamburger-icon--open')) {
      hamburgerIcon.classList.remove('hamburger-icon--open');
      mobileNavigation.classList.remove('burger-menu--overlay__active');
      burgerReset();
    }
  });
};

document.addEventListener('DOMContentLoaded', () => {
  mobileNav();
  burgerNext();
  burgerPrev();
});
