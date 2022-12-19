document.addEventListener('DOMContentLoaded', () => {
  const bloggersWriters = document.querySelector('.js-bloggers__writers');
  const bloggersTrigger = document.querySelector('.js-bloggers__view-all-writers-toggle');
  if (bloggersWriters.length === 0) {
    return;
  }
  bloggersTrigger.addEventListener('click', () => {
    bloggersTrigger.classList.toggle('active');
    bloggersWriters.classList.toggle('bloggers__writers--expanded');
  });
});
