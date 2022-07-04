import Vue from 'vue';
import axios from 'axios';

require('../../../scripts/vue.config')(Vue);

document.addEventListener('DOMContentLoaded', () => {
  const vm = new Vue({
    el: '.seasonal-wheel',
    delimiters: ['${', '}'],
    data: {
      seasonalWheelData: {},
    },
    methods: {
      async getSeasonalArticles() {
        await axios
          .get('sis/season-wheel/get/60')
          .then((response) => {
            this.seasonalWheelData = response.data;
            console.log(this.seasonalWheelData);

            // Drupal handling
            const ajaxObject = Drupal.ajax({
              url: '',
              base: false,
              element: false,
              progress: false,
            });
            ajaxObject.success(this.seasonalWheelData);
            // log
            console.log(this.seasonalWheelData);
          })
          .catch((error) => {
            console.log(error);
          });
      },
      seasonWheelController() {
        this.resetHover();
        this.addHoverClasses();
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
      getSeasonalArticlesByMonth(passedMonth) {
        this.resetHover();
        console.log('getSeasonalArticlesByMonth', passedMonth);
        if (passedMonth === 'Jan') {
          console.log('hitting');
          const Jan = document.querySelector('.st24');
          Jan.classList.add('hover-wheel');
        } else {
          console.log('not hitting');
        }

        // @click="getSeasonalArticlesByMonth('{{ 1 }}')"    --  twig

        // TODO
        // Get article data for each month - API CALL - then add to the DOM through drupal magic
        // On page load get articles for current month (below Current month name)
        // Dynamic month name On load and on click pizza wheel
        // pizza wheel months call different months and display data below vector image line
        // initial values on load - to always show current month's data -
        // Mainly Month name (below wheel) and articles that load
        // for that month (even before wheel spin)
      },
    },
    mounted() {
      this.getSeasonalArticles();
    },
  });
});
