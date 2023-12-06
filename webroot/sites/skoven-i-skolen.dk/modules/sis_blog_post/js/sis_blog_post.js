document.addEventListener('DOMContentLoaded', () => {
  const bloggersWriters = document.querySelector('.blog-post--view-all-writers');
  const bloggersTrigger = document.querySelector('.view-all-writers-link');
  const bloggersHideTrigger = document.querySelector('.hide-all-writers-link');
  if (bloggersWriters.length === 0) {
    return;
  }
  bloggersTrigger.addEventListener('click', () => {
    bloggersTrigger.classList.toggle('hidden');
    bloggersWriters.classList.toggle('expanded');
    var headerOffset = 180;
    var elementPosition = bloggersWriters.getBoundingClientRect().top;
    var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
    window.scrollTo({
      top: offsetPosition,
      behavior: "smooth"
    });
  });

  bloggersHideTrigger.addEventListener('click', () => {
    bloggersTrigger.classList.toggle('hidden');
    bloggersWriters.classList.toggle('expanded');
    var headerOffset = 180;
    var elementPosition = bloggersTrigger.getBoundingClientRect().top;
    var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
    window.scrollTo({
      top: offsetPosition,
      behavior: "smooth"
    });
  });
});
