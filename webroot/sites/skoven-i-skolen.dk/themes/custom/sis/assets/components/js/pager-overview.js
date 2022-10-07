import Vue from 'vue';

require('../../../scripts/vue.config')(Vue);

document.addEventListener('DOMContentLoaded', () => {
  const vm = new Vue({
    el: '.js-pager',
    delimiters: ['${', '}'],
    data: {
      seasonalWheelData: {},
      month: '',
      currentMonth: '',
      currentlySelectedMonth: '',
      randomMonthData: {},
      showModal: false,
    },
    methods: {
      sendToTop() {
        window.scrollTo({
          top: 600,
          behavior: 'smooth',
        });
      },
    },

    mounted() {
      // this.sendToTop();
      // console.log('mounted');
    },
  });
});
