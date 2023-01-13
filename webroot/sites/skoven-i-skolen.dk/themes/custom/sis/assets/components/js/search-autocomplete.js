/**
 * @file
 * Search autocomplete.
 */

import AutoComplete from '@tarekraafat/autocomplete.js';

document.addEventListener('DOMContentLoaded', () => {
  const autoCompleteJS = new AutoComplete({
    placeHolder: Drupal.t('Search entire site'),
    selector: '#js-header-search-input',
    submit: true,
    data: {
      src: async (query) => {
        try {
          // Fetch Data from external Source
          const source = await fetch(`/relewise/search_content/${query}`);
          // Data should be an array of `Objects` or `Strings`
          const data = await source.json();

          if (!data) {
            return [];
          }
          return data.results;
        } catch (error) {
          return error;
        }
      },

      // Data source 'Object' key to be searched
      keys: ['displayName'],
    },

    resultsList: {
      element: (list, data) => {
        const info = document.createElement('li');
        info.setAttribute('class', 'autoComplete_wrapper__info');
        if (data.results.length > 8) {
          info.innerHTML = `<a href="/search?text=${data.query}" class="button">${Drupal.t('Show all results (@count)', { '@count': data.matches.length })}</a>`;
        }

        if (!data.results.length) {
          info.innerHTML = `<span>${Drupal.t('Found No Results for "@query"', { '@query': data.query })}</span>`;
        }

        list.append(info);
      },
      noResults: true,
      maxResults: 8,
    },
    resultItem: {
      element: (item, data) => {
        const element = item;
        let type = '';
        if (typeof data.value.data.bundle_label !== 'undefined' && data.value.data.bundle.value !== 'page') {
          type = data.value.data.bundle_label.value;
        }
        if (typeof data.value.data.field_article_type_label !== 'undefined') {
          type = data.value.data.field_article_type_label.value;
        }
        const href = data.value.href.substring(1);
        element.innerHTML = `
            <span class="autoComplete_wrapper__match" data-href="${href}">${data.match}</span>
            <span class="autoComplete_wrapper__type">${type}</span>`;
      },
      highlight: true,
    },
    events: {
      input: {
        selection: (event) => {
          const selection = event.detail.selection.value.href.substring(1);
          window.location.assign(drupalSettings.path.baseUrl + selection);
        },
      },
    },
  });
  const autoCompleteWrapper = document.querySelector('.autoComplete_wrapper');
  autoCompleteWrapper.setAttribute('aria-labelledby', 'search');
  autoCompleteWrapper.lastChild.setAttribute('aria-labelledby', 'search');
});
