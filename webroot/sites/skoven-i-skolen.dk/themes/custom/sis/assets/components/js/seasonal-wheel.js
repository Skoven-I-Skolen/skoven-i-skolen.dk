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
      currentlySelectedMonth: '',
      randomMonthData: {},
      showModal: false,
    },
    methods: {
      async getSeasonalArticles(month) {
        await axios
          .get(`/sis/season-wheel/get/${month}`)
          .then((response) => {
            this.seasonalWheelData = response.data;
            // Drupal handling - showing month cards
            const ajaxObject = Drupal.ajax({
              url: '',
              base: false,
              element: false,
              progress: false,
            });
            ajaxObject.success(this.seasonalWheelData);
          })
          .catch((error) => error.response);
        this.addHoverEffectOnCurrentMonth(month);
        this.currentlySelectedMonth = month;
      },

      // Sets highlight effect on current month card
      addHoverEffectOnCurrentMonth(month) {
        if (month) {
          const currentMonth = document.querySelector(`.${month}`);
          currentMonth.classList.add('hover-wheel');
        }
      },

      getCurrentMonthRandomArticle() {
        this.disableClick();
        this.resetHover();
        this.addHoverClassesToCurrentMonth();
        this.getRandomArticleForCurrentMonth();
        setTimeout(() => {
          this.openModal();
          this.$nextTick(() => {
            // dynamically attach image to modal
            const modalImage = document.querySelector('.modal__image');
            modalImage.src = this.randomMonthData.image;

            // dynamically attach link to title
            const linkTitle = document.querySelector('.modal-link__title');
            linkTitle.href = this.randomMonthData.url;

            // dynamically attach link to image
            const linkImage = document.querySelector('.modal-link__image');
            linkImage.href = this.randomMonthData.url;

            // remove hidden class from modal to show it -
            // this is used as workaround because of modal flicering on page load
            document
              .querySelector('.hidden-modal')
              .classList.remove('hidden-modal');
          });
        }, 1200);
      },

      // disable click event on all months and red button for x seconds
      disableClick() {
        const months = [
          document.querySelector('.january'),
          document.querySelector('.february'),
          document.querySelector('.march'),
          document.querySelector('.april'),
          document.querySelector('.may'),
          document.querySelector('.june'),
          document.querySelector('.july'),
          document.querySelector('.august'),
          document.querySelector('.september'),
          document.querySelector('.october'),
          document.querySelector('.november'),
          document.querySelector('.december'),
          document.querySelector('.red-knob'),
        ];
        months.forEach((month) => {
          month.classList.add('disabled');
        });
        setTimeout(() => {
          months.forEach((month) => {
            month.classList.remove('disabled');
          });
        }, 1300);
      },

      resetHover() {
        // Month Hover classes
        const Jan = document.querySelector('.january');
        const Feb = document.querySelector('.february');
        const March = document.querySelector('.march');
        const April = document.querySelector('.april');
        const May = document.querySelector('.may');
        const June = document.querySelector('.june');
        const July = document.querySelector('.july');
        const August = document.querySelector('.august');
        const Sept = document.querySelector('.september');
        const Oct = document.querySelector('.october');
        const Nov = document.querySelector('.november');
        const Dec = document.querySelector('.december');

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

      addHoverClassesToCurrentMonth() {
        // Month Hover classes
        const Jan = document.querySelector('.january');
        const Feb = document.querySelector('.february');
        const March = document.querySelector('.march');
        const April = document.querySelector('.april');
        const May = document.querySelector('.may');
        const June = document.querySelector('.june');
        const July = document.querySelector('.july');
        const August = document.querySelector('.august');
        const Sept = document.querySelector('.september');
        const Oct = document.querySelector('.october');
        const Nov = document.querySelector('.november');
        const Dec = document.querySelector('.december');

        // go through all the months and add the hover class
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

        const offset = currentMonthIndex;
        for (let i = 0; i < months.length; i += 1) {
          const pointer = (i + offset) % months.length;

          setTimeout(() => {
            months[pointer].classList.add('hover-wheel');
          }, i * 100);
          setTimeout(() => {
            if (months[pointer] === currentMonthIndex) {
              months[pointer].classList.add('hover-wheel');
            }

            // on last loop iteration
            if (i === months.length - 1) {
              months[pointer].classList.add('hover-wheel');
            } else months[pointer].classList.remove('hover-wheel');
          }, i * 100 + 100);
        }
      },

      async getRandomArticleForCurrentMonth() {
        await axios
          .get(`/sis/season-wheel/get/random/${this.currentMonth}`)
          .then((response) => {
            this.randomMonthData = response.data;
          })
          .catch((error) => error.response);
      },

      getSeasonalArticlesByMonth(passedMonth) {
        // dont call if the same month is selected twice
        if (passedMonth === this.currentlySelectedMonth) {
          return;
        }

        this.resetHover();

        if (passedMonth) {
          this.getSeasonalArticles(passedMonth);
          const currentMonth = document.querySelector(`.${passedMonth}`);
          currentMonth.classList.add('hover-wheel');
          this.currentlySelectedMonth = passedMonth;
        }
      },

      getCurrentMonth() {
        const currentMonth = new Date().getMonth();
        const currentMonthIndex = currentMonth + 1;

        if (currentMonthIndex === 1) {
          this.currentMonth = 'january';
          return 'january';
        }
        if (currentMonthIndex === 2) {
          this.currentMonth = 'february';
          return 'february';
        }
        if (currentMonthIndex === 3) {
          this.currentMonth = 'march';
          return 'march';
        }
        if (currentMonthIndex === 4) {
          this.currentMonth = 'april';
          return 'april';
        }
        if (currentMonthIndex === 5) {
          this.currentMonth = 'may';
          return 'may';
        }
        if (currentMonthIndex === 6) {
          this.currentMonth = 'june';
          return 'june';
        }
        if (currentMonthIndex === 7) {
          this.currentMonth = 'july';
          return 'july';
        }
        if (currentMonthIndex === 8) {
          this.currentMonth = 'august';
          return 'august';
        }
        if (currentMonthIndex === 9) {
          this.currentMonth = 'september';
          return 'september';
        }
        if (currentMonthIndex === 10) {
          this.currentMonth = 'october';
          return 'october';
        }
        if (currentMonthIndex === 11) {
          this.currentMonth = 'november';
          return 'november';
        }
        if (currentMonthIndex === 12) {
          this.currentMonth = 'december';
          return 'december';
        }
        return this.getCurrentMonth();
      },

      closeModal() {
        this.showModal = false;
        document.querySelector('body').classList.remove('overflow-hidden');
      },

      openModal() {
        this.showModal = true;
        document.querySelector('body').classList.add('overflow-hidden');
      },

      onKeyPress(e) {
        if (e.keyCode === 27) {
          this.closeModal();
        }
      },
    },

    mounted() {
      this.getCurrentMonth();
      this.addHoverEffectOnCurrentMonth(this.currentMonth);
    },
    created() {
      window.addEventListener('keyup', this.onKeyPress);
    },
    beforeDestroy() {
      window.removeEventListener('keyup', this.onKeyPress);
    },
  });
});
