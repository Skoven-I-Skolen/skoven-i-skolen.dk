/**
 * @file
 * Search autocomplete.
 */

import AutoComplete from '@tarekraafat/autocomplete.js';

document.addEventListener('DOMContentLoaded', () => {
  const autoCompleteJS = new AutoComplete({
    placeHolder: 'Search entire site',
    selector: '#js-header-search-input',
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
          info.innerHTML = `<a href="/search?q=${data.query}" class="button">${Drupal.t('Show all results (@count)', { '@count': data.matches.length })}</a>`;
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
        const type = data.value.data.field_article_type_label.value;
        element.innerHTML = `
            <span class="autoComplete_wrapper__match" data-href="${data.value.href}">${data.match}</span>
            <span class="autoComplete_wrapper__type">${type}</span>`;
      },
      highlight: true,
    },
    events: {
      input: {
        selection: (event) => {
          const selection = event.detail.selection.value.href;
          window.location.replace(drupalSettings.path.baseUrl + selection);
        },
      },
    },
  });
});
