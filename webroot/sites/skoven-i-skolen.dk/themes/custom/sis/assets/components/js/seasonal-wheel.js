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
        if (month === 'january') {
          const Jan = document.querySelector('.january');
          Jan.classList.add('hover-wheel');
        } else if (month === 'february') {
          const Feb = document.querySelector('.february');
          Feb.classList.add('hover-wheel');
        } else if (month === 'march') {
          const Mar = document.querySelector('.march');
          Mar.classList.add('hover-wheel');
        } else if (month === 'april') {
          const Apr = document.querySelector('.april');
          Apr.classList.add('hover-wheel');
        } else if (month === 'may') {
          const May = document.querySelector('.may');
          May.classList.add('hover-wheel');
        } else if (month === 'june') {
          const Jun = document.querySelector('.june');
          Jun.classList.add('hover-wheel');
        } else if (month === 'july') {
          const Jul = document.querySelector('.july');
          Jul.classList.add('hover-wheel');
        } else if (month === 'august') {
          const Aug = document.querySelector('.august');
          Aug.classList.add('hover-wheel');
        } else if (month === 'september') {
          const Sep = document.querySelector('.september');
          Sep.classList.add('hover-wheel');
        } else if (month === 'october') {
          const Oct = document.querySelector('.october');
          Oct.classList.add('hover-wheel');
        } else if (month === 'november') {
          const Nov = document.querySelector('.november');
          Nov.classList.add('hover-wheel');
        } else if (month === 'december') {
          const Dec = document.querySelector('.december');
          Dec.classList.add('hover-wheel');
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
        // Add hover class to selected month
        if (passedMonth === 'january') {
          this.getSeasonalArticles('january');
          const Jan = document.querySelector('.january');
          Jan.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'january';
        } else if (passedMonth === 'february') {
          this.getSeasonalArticles('february');
          const Feb = document.querySelector('.february');
          Feb.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'february';
        } else if (passedMonth === 'march') {
          this.getSeasonalArticles('march');
          const March = document.querySelector('.march');
          March.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'march';
        } else if (passedMonth === 'april') {
          this.getSeasonalArticles('april');
          const April = document.querySelector('.april');
          April.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'april';
        } else if (passedMonth === 'may') {
          this.getSeasonalArticles('may');
          const May = document.querySelector('.may');
          May.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'may';
        } else if (passedMonth === 'june') {
          this.getSeasonalArticles('june');
          const June = document.querySelector('.june');
          June.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'june';
        } else if (passedMonth === 'july') {
          this.getSeasonalArticles('july');
          const July = document.querySelector('.july');
          July.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'july';
        } else if (passedMonth === 'august') {
          this.getSeasonalArticles('august');
          const August = document.querySelector('.august');
          August.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'august';
        } else if (passedMonth === 'september') {
          this.getSeasonalArticles('september');
          const Sept = document.querySelector('.september');
          Sept.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'september';
        } else if (passedMonth === 'october') {
          this.getSeasonalArticles('october');
          const Oct = document.querySelector('.october');
          Oct.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'october';
        } else if (passedMonth === 'november') {
          this.getSeasonalArticles('november');
          const Nov = document.querySelector('.november');
          Nov.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'november';
        } else if (passedMonth === 'december') {
          this.getSeasonalArticles('december');
          const Dec = document.querySelector('.december');
          Dec.classList.add('hover-wheel');
          this.currentlySelectedMonth = 'december';
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
