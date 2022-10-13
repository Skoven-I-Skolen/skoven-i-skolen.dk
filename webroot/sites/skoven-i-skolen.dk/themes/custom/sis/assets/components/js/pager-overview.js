import Vue from 'vue';

require('../../../scripts/vue.config')(Vue);

Drupal.behaviors.pageUp = {
  attach(context, settings) {
    const vm = new Vue({
      el: '.js-pager',
      delimiters: ['${', '}'],
      data: {
      },
      methods: {
        // Scroll to class
        scrollToClass() {
          const el = document.querySelector('.overview-form__header');
          el.scrollIntoView({
          });
        },
      },

      mounted() {
        // check url to have page
        const url = window.location.href;
        if (url.indexOf('page') > -1) {
          this.scrollToClass();
        }
      },
    });
  },
};
