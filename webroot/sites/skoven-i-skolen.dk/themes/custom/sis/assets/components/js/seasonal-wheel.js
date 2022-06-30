import Vue from 'vue';

document.addEventListener('DOMContentLoaded', () => {
  const vm = new Vue({
    el: '#seasonal-wheel',
    data: {
      showModal: false,
      content: 'Default Modal Text',
    },
    methods: {
      testMethod() {
        console.log('testMethod');
      },
    },
  });
});
axios;
