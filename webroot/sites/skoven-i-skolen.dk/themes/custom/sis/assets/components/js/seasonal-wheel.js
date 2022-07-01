import Vue from 'vue';
import axios from 'axios';

require('../../../scripts/vue.config')(Vue);

document.addEventListener('DOMContentLoaded', () => {
  const vm = new Vue({
    el: '.seasonal-wheel',
    data: {
      seasonalWheelData: {},
      isLoading: false,
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
      seasonWheelController() {
        if (this.isLoading) {
          console.log('isLoading');
        } else {
          this.resetHover();
          this.addHoverClasses();
        }
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

        //    go through all the months and add the hover classList
        const months = [
          Jan,
          Feb,
          March,
          April,
          May,
          June,
          July,
          August,
          Sept,
          Oct,
          Nov,
          Dec,
        ];

        // get current month
        const currentMonth = new Date().getMonth();
        const currentMonthIndex = currentMonth + 1;

        // Stop loop at the current month
        for (let i = 0; i < currentMonthIndex; i += 1) {
          setTimeout(() => {
            months[i].classList.add('hover-wheel');
          }, i * 200);
          setTimeout(() => {
            if (i === currentMonthIndex - 1) {
              months[i].classList.add('hover-wheel');
            } else {
              months[i].classList.remove('hover-wheel');
            }
          }, i * 200 + 200);
        }
      },
      resetHover() {
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

        //    go through all the months and add the hover classList
        const months = [
          Jan,
          Feb,
          March,
          April,
          May,
          June,
          July,
          August,
          Sept,
          Oct,
          Nov,
          Dec,
        ];
        // remove all classes from the hover classList
        months.forEach((month) => {
          month.classList.remove('hover-wheel');
        });
      },
    },
    mounted() {},
  });
});
