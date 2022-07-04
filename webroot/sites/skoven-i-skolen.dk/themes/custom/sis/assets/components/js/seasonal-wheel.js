import Vue from 'vue';
import axios from 'axios';

require('../../../scripts/vue.config')(Vue);

document.addEventListener('DOMContentLoaded', () => {
  const vm = new Vue({
    el: '.seasonal-wheel',
    delimiters: ['${', '}'],
    data: {
      seasonalWheelData: {},
      month: '',
      currentMonth: '',
    },
    methods: {
      async getSeasonalArticles(month) {
        await axios
          .get(`/sis/season-wheel/get/${month}`)
          .then((response) => {
            this.seasonalWheelData = response.data;

            // Drupal handling
            const ajaxObject = Drupal.ajax({
              url: '',
              base: false,
              element: false,
              progress: false,
            });
            ajaxObject.success(this.seasonalWheelData);
          })
          .catch((error) => {
            console.log(error);
          });
      },
      getCurrentMonthArticles() {
        this.resetHover();
        this.addHoverClassesToCurrentMonth();
        this.getSeasonalArticles(this.currentMonth);
      },
      addHoverClassesToCurrentMonth() {
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
        if (passedMonth === 'january') {
          this.getSeasonalArticles('january');
          // Add hover class to selected month
          const Jan = document.querySelector('.st24');
          Jan.classList.add('hover-wheel');
        } else if (passedMonth === 'february') {
          this.getSeasonalArticles('february');
          const Feb = document.querySelector('.st22');
          Feb.classList.add('hover-wheel');
        } else if (passedMonth === 'march') {
          this.getSeasonalArticles('march');
          const March = document.querySelector('.st20');
          March.classList.add('hover-wheel');
        } else if (passedMonth === 'april') {
          this.getSeasonalArticles('april');
          const April = document.querySelector('.st18');
          April.classList.add('hover-wheel');
        } else if (passedMonth === 'may') {
          this.getSeasonalArticles('may');
          const May = document.querySelector('.st16');
          May.classList.add('hover-wheel');
        } else if (passedMonth === 'june') {
          this.getSeasonalArticles('june');
          const June = document.querySelector('.st14');
          June.classList.add('hover-wheel');
        } else if (passedMonth === 'july') {
          this.getSeasonalArticles('july');
          const July = document.querySelector('.st12');
          July.classList.add('hover-wheel');
        } else if (passedMonth === 'august') {
          this.getSeasonalArticles('august');
          const August = document.querySelector('.st10');
          August.classList.add('hover-wheel');
        } else if (passedMonth === 'september') {
          this.getSeasonalArticles('september');
          const Sept = document.querySelector('.st8');
          Sept.classList.add('hover-wheel');
        } else if (passedMonth === 'october') {
          this.getSeasonalArticles('october');
          const Oct = document.querySelector('.st6');
          Oct.classList.add('hover-wheel');
        } else if (passedMonth === 'november') {
          this.getSeasonalArticles('november');
          const Nov = document.querySelector('.st4');
          Nov.classList.add('hover-wheel');
        } else if (passedMonth === 'december') {
          this.getSeasonalArticles('december');
          const Dec = document.querySelector('.st1');
          Dec.classList.add('hover-wheel');
        }
      },

      // get current month
      getCurrentMonth() {
        const currentMonth = new Date().getMonth();
        const currentMonthIndex = currentMonth + 1;

        if (currentMonthIndex === 1) {
          this.currentMonth = 'january';
          return 'january';
        } if (currentMonthIndex === 2) {
          this.currentMonth = 'february';
          return 'february';
        } if (currentMonthIndex === 3) {
          this.currentMonth = 'march';
          return 'march';
        } if (currentMonthIndex === 4) {
          this.currentMonth = 'april';
          return 'april';
        } if (currentMonthIndex === 5) {
          this.currentMonth = 'may';
          return 'may';
        } if (currentMonthIndex === 6) {
          this.currentMonth = 'june';
          return 'june';
        } if (currentMonthIndex === 7) {
          this.currentMonth = 'july';
          return 'july';
        } if (currentMonthIndex === 8) {
          this.currentMonth = 'august';
          return 'august';
        } if (currentMonthIndex === 9) {
          this.currentMonth = 'september';
          return 'september';
        } if (currentMonthIndex === 10) {
          this.currentMonth = 'october';
          return 'october';
        } if (currentMonthIndex === 11) {
          this.currentMonth = 'november';
          return 'november';
        } if (currentMonthIndex === 12) {
          this.currentMonth = 'december';
          return 'december';
        }
        return this.getCurrentMonth();
      },
    },
    mounted() {
      this.getCurrentMonth();
      console.log('Mounted Current Month is: ', this.getCurrentMonth());
      this.getSeasonalArticles(this.getCurrentMonth());
    },
  });
});
