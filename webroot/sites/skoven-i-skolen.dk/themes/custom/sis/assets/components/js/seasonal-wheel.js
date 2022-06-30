import Vue from 'vue';
import axios from 'axios';

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
    mounted() {
      axios.get('/').then((response) => {
        this.content = response.data.content;
        console.log(this.content);

        const ajaxObject = Drupal.ajax({
          url: '',
          base: false,
          element: false,
          progress: false,
        });
        ajaxObject.success(response, status);
        ajaxObject.error(response, status);
      }).catch((error) => {
        console.log(error);
      });
    },
  });
});
