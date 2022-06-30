import Vue from 'vue';
import axios from 'axios';

require('../../../scripts/vue.config')(Vue);

document.addEventListener('DOMContentLoaded', () => {
  const vm = new Vue({
    el: '.seasonal-wheel',
    data: {
      seasonalWheelData: {},
    },
    methods: {
      async getSeasonalArticles() {
        await axios
          .get('/api/v1/content/seasonal-articles')
          .then((response) => {
            this.seasonalWheelData = response.data;

            // Drupal handling
            // const ajaxObject = Drupal.ajax({
            //   url: '',
            //   base: false,
            //   element: false,
            //   progress: false,
            // });
            // ajaxObject.success(response, status);
          })
          .catch((error) => {
            console.log(error);
          });
      },
      addHoverClasses() {
        // Month Hover classes
        const Jan = document.querySelector('.st24');
        const Feb = document.querySelector('.st22');
        const March = document.querySelector('.st20');
        const April = document.querySelector('.st18');
        const May = document.querySelector('.st16');
        const June = document.querySelector('.st14');
        const July = document.querySelector('.st12');
        const August = document.querySelector('.st10');
        const Sept = document.querySelector('.st8');
        const Oct = document.querySelector('.st6');
        const Nov = document.querySelector('.st4');
        const Dec = document.querySelector('.st1');

        // Dec.classList.add('hover-wheel');
        // Nov.classList.add('hover-wheel');
        // Oct.classList.add('hover-wheel');
        // Sept.classList.add('hover-wheel');
        // August.classList.add('hover-wheel');
        // July.classList.add('hover-wheel');
        // June.classList.add('hover-wheel');
        // May.classList.add('hover-wheel');
        // April.classList.add('hover-wheel');
        // March.classList.add('hover-wheel');
        // Feb.classList.add('hover-wheel');
        // Jan.classList.add("hover-wheel");

        //    go through all the months and add the hover classList
        const months = [Jan, Feb, March, April, May, June, July, August, Sept, Oct, Nov, Dec];
        months.forEach((month, i) => {
          setTimeout(() => {
            month.classList.add('hover-wheel');
          },
          i * 300);
          setTimeout(() => {
            month.classList.remove('hover-wheel');
          },
          i * 300 + 300);
        });
      },
    },
    mounted() {},
  });
});
