Drupal.behaviors.sis_map_okapi_integration = {
  attach: function (context, settings) {
    var defaultDotIcon = '/sites/skoven-i-skolen.dk/themes/custom/sis/assets/icons/stedsbaserede-materialer.svg';
    var filters = [];
    let markers = [];
    var autoZoom = false;
    var firstRender = true;
    var query = new URL(window.location).searchParams;

    if (settings.sis_map) {
      markers = settings.sis_map.markers;
    }
    const TOKEN = '9f667a80fc5d9b3f0f8dac7ae6492048';

    if (settings.sis_map) {
      // Format the icons array to add the actual .svg file paths.
      Object.keys(settings.sis_map.icons).forEach(function (filterName) {
          let iconName = settings.sis_map.icons[filterName];
          settings.sis_map.icons[formatDataType(filterName)] = '/sites/skoven-i-skolen.dk/themes/custom/sis/assets/icons/' + iconName + '.svg';
        }
      );
      settings.sis_map.icons['default'] = defaultDotIcon;
    }

    // Add "checked" event to each filter checkbox.
    document.querySelectorAll('.filter-checkbox').forEach(function (element) {
      if (!element.classList.contains('enabled')) {
        element.addEventListener('change', (event) => {
          if (!event.currentTarget.classList.contains('js-see-all-checkbox')) {
            applyFilter(event.currentTarget.name, event.currentTarget.value, event.currentTarget.checked);
          }
        });
        element.classList.add('enabled');
      }
    });

    function addOrRemoveFilterToQuery(category, value, add = true) {
      if (add) {
        if (!query.getAll(category).includes(value)) {
          query.append(category, value);
          let pageTitle = document.getElementsByTagName("title")[0].innerHTML;
          window.history.pushState(window.location.pathname,
            pageTitle, '?' + query.toString());
        }
      }
      else {
        let currentParams = new URL(window.location).searchParams.toString();
        let toRemoveParams = new URLSearchParams({[category] : value}).toString();
        currentParams = currentParams.replaceAll('&' + toRemoveParams, '');
        currentParams.replaceAll('?$', '?');
        currentParams.replaceAll('&&', '&');
        query = new URLSearchParams(currentParams);
        window.history.pushState('page2', 'Title', '?' + currentParams);
      }
    }

    function applyFiltersFromSearchQuery(query) {
      let iterator = query.keys();
      for (const i of iterator) {
        query.getAll(i).forEach(function (f) {
          if (i !== 'uid') {
            var checkbox = document.querySelector('[id="' + decodeURIComponent(f) + '"]');
            if (!checkbox.checked) {
              checkbox.click();
              checkbox.parentElement.parentElement.classList.add('expanded');
            }
          }
        })
      }
    }

    function applyFilter(category, value, status) {
      if (status) {
        // Add a filter.
        if (!filters[category]) {
          filters[category] = []
          filters[category].push(value);
          addOrRemoveFilterToQuery(category, value, true);
        }
        else if (!filters[category].includes(value)){
          filters[category].push(value);
          addOrRemoveFilterToQuery(category, value, true);
        }
      }
      else {
        // Remove a filter.
        if (filters[category]) {
          filters[category] = filters[category].filter(f => f !== value);
          if (filters[category].length === 0) {
            delete filters[category];
          }
          addOrRemoveFilterToQuery(category, value, false);
        }
      }

      refreshMarkers();
    }

    function refreshMarkers() {
      var resultSet = [];

      // Apply the currently selected filters to the markers.
      Object.keys(markers).forEach(function (index) {
        var marker = markers[index];
        let intersection = Object.keys(marker.filters).filter(x => Object.keys(filters).includes(x));
        if (intersection.length > 0) {
          // The marker contains at least one of the selected filters.
          var matchCount = 0;
          intersection.forEach(function (match) {
            let matches = markers[index].filters[match].filter(x => filters[match].includes(x));
            if (matches.length > 0) {
              matchCount++;
            }
          });
          if (matchCount === Object.keys(filters).length) {
            resultSet.push(markers[index]);
          }
        }
      });

      // Erase all current map markers and entries on result list.
      document.querySelectorAll('.geomarker, .result-item').forEach(function (e) {
        e.remove();
      })


      // Render each map new marker.
      resultSet.forEach(function (element) {
        renderMapMarker(element);
      });

      renderResultList(resultSet);

      // Rebuild the map, so it reads the new marker list.
      autoZoom = resultSet.length > 0;
      buildMap();
    }

    function renderMapMarker(marker) {
      let lastSelectedFilter = (Object.keys(filters)[Object.keys(filters).length - 1]);
      let dataType = 'default';
      if (marker['filters'][lastSelectedFilter] && settings.sis_map.icons[marker['filters'][lastSelectedFilter][0]]) {
        dataType = marker['filters'][lastSelectedFilter][0];
      }
      let m = document.createElement('span');
      m.setAttribute('class', 'geomarker');
      m.setAttribute('data-type', formatDataType(dataType));
      m.setAttribute('data-title', marker['node']['title'][0]['value']);
      var address = '';

      if (marker['address']) {
        // The dawa address comes in the format "HASH, Address",
        // we need to split the string to the get the actual address.
        address = marker['address'].substring(marker['address'].indexOf(',') + 1);
      }

      var description = '';

      if (marker['node']['field_summary'] && marker['node']['field_summary'][0]
        && marker['node']['field_summary'][0]['value']) {
        description += marker['node']['field_summary'][0]['value'].substring(0, 60) + '...'; + '<br>';
      }

      if (marker['node']['field_description'] && marker['node']['field_description'][0]
        && marker['node']['field_description'][0]['value']) {
        description += marker['node']['field_description'][0]['value'].substring(0, 60) + '...'; + '<br>';
      }

      else if (marker['node']['body'] && marker['node']['body'][0]
        && marker['node']['body'][0]['summary']) {
        description += marker['node']['body'][0]['summary'].substring(0, 60) + '...'; + '<br>';
      }

      if (marker['lat'] && marker['lon']) {
        m.setAttribute('data-lat', marker['lat']);
        m.setAttribute('data-lon', marker['lon']);
        description += '<br>Breddegrad: ' + marker['lat'] + "<br>Længdegrad: " + marker['lon'];
      }

      if (address) {
        m.setAttribute('data-address', address);
        description += '<br>Addresse:<br>' + address;
      }

      if (marker['url']) {
        description += '<br><a href="' + marker['url'] + '">' + Drupal.t('Se mere') + '</a>';
      }

      m.setAttribute('data-description', description);

      let markersDiv = document.querySelector('.markers');
      markersDiv.appendChild(m);
    }

    function renderResultList(resultSet) {
      var listElement = document.querySelector('.result-list');
      listElement.innerHTML = '';
      var resultList = [];

      var selectedFilters = Object.values(filters).flat();

      selectedFilters.forEach(function (f){
        if (!resultList[f]) {
          resultList[f] = [];
        }
      });

      resultSet.forEach(function (element) {
        var allFilters = Object.values(element['filters']).flat().filter(
          function(item, pos, self) { return self.indexOf(item) == pos; })
        let intersection = allFilters.filter(x => selectedFilters.includes(x)).sort();
        intersection = intersection.join(' & ');
        if (!resultList[intersection]) { resultList[intersection] = [];}
        if (!resultList[intersection].includes(element)) {
          resultList[intersection].push(element);
        }
      });

      Object.keys(resultList).forEach(function (key) {
        if (resultList[key].length > 0) {
          var categoryTitleWrapper = document.createElement('div')
          categoryTitleWrapper.classList.add('category-title-wrapper');
          var categoryIcon = document.createElement('img');
          categoryIcon.classList.add('result-list-category-title-icon');
          if (settings.sis_map.icons[key.split(', ')[0]]) {
            categoryIcon.src = settings.sis_map.icons[formatDataType(key.split(', ')[0])];
          }
          else {
            categoryIcon.src = settings.sis_map.icons['default'];
          }
          var category = document.createElement('h4');
          category.classList.add('result-list-category-title');
          category.innerText = key;
          var seeAllLink = document.createElement('p');
          seeAllLink.classList.add('category-title-see-all-link');
          seeAllLink.innerText = 'Se alle ' + key.toLowerCase();
          seeAllLink.setAttribute('name', key);
          seeAllLink.addEventListener('click', function (event) {
            var children = document.querySelectorAll('[name="' + event.currentTarget.getAttribute('name') + '"]');
            children.forEach(function (child) {
              if (child.classList.contains('can-be-hidden')) {
                if (child.classList.contains('is-hidden')) {
                  child.classList.remove('is-hidden');
                }
                else {
                  child.classList.add('is-hidden');
                }
              }
            });
            if (event.currentTarget.innerText.includes('Se alle')) {
              event.currentTarget.innerText = 'Luk ' + key.toLowerCase();
            }
            else {
              event.currentTarget.innerText = 'Se alle ' + key.toLowerCase();
            }
            var icon = event.currentTarget.parentElement.lastChild;
            if (icon) {
              if (icon.classList.contains('inverted')) {
                icon.classList.remove('inverted');
              } else {
                icon.classList.add('inverted');
              }
            }
          });
          var seeAllIcon = document.createElement('div');
          seeAllIcon.classList.add('category-title-see-all-link-icon');
          categoryTitleWrapper.appendChild(categoryIcon);
          categoryTitleWrapper.appendChild(category);
          if (resultList[key].length > 0) {
            categoryTitleWrapper.appendChild(seeAllLink);
          }
          categoryTitleWrapper.appendChild(seeAllIcon);
          listElement.appendChild(categoryTitleWrapper);
          var count = 0;
          resultList[key].forEach(function (element) {
            var link = document.createElement('a');
            link.setAttribute('href', element['url']);
            var item = document.createElement('div');
            item.classList.add('result-list-item');
            item.setAttribute('name', key);
            if (count % 2 == 0) {
              item.classList.add('is-even');
            }
            if (count > -1) {
              item.classList.add('is-hidden');
              item.classList.add('can-be-hidden');
            }
            item.innerText = element['node']['title'][0]['value'];
            link.appendChild(item);
            listElement.appendChild(link);
            count++;
          });
          var separator = document.createElement('div')
          separator.classList.add('filter-separator')
          separator.classList.add('can-be-hidden')
          separator.setAttribute('name', key)
          listElement.appendChild(separator);
        }
      });
    }

    function formatDataType(type) {
      return type.replaceAll(' ', '-');
    }

    function buildMap() {
      if (document.getElementById('map')) {
        document.getElementById('map').remove();
      }
      let m = document.createElement('div');
      m.setAttribute('id', 'map');
      m.setAttribute('class', 'geomap');
      if (autoZoom) {
        m.setAttribute('data-center', 'auto');
        m.setAttribute('data-zoom', 'auto');
      }
      m.setAttribute('data-token', TOKEN);
      m.setAttribute('data-show-popup', true);
      if (settings.sis_map) {
        m.setAttribute('data-zoomslider', Boolean(settings.sis_map.display_map_helpers));
        m.setAttribute('data-layerswitcher', Boolean(settings.sis_map.display_map_helpers));
      }
      else {
        // These default values apply when we're embedding the map on a node page, with a single map dot,
        // and not as a block with multiple filters.
        m.setAttribute('data-zoomslider', "true");
        m.setAttribute('data-layerswitcher', "true");
        m.setAttribute('data-center', 'auto');
        m.setAttribute('data-zoom', 'auto');
        m.setAttribute('data-background', 'orto_foraar');
      }

      document.querySelector('.map-container').prepend(m);
      autoZoom = true;

      if (settings.sis_map) {
        var map = new okapi.Initialize({icons: settings.sis_map.icons});
      }

      else {
        var map = new okapi.Initialize({icons: {'default':defaultDotIcon}});
      }

      if (firstRender) {
        if (settings.sis_map.render_all_markers_on_build) {
          Object.keys(markers).forEach(function (index) {
            renderMapMarker(markers[index]);
          });
        }
        applyFiltersFromSearchQuery(query);
        firstRender = false;
      }
    }

    buildMap();

    document.querySelectorAll('.category-name').forEach(function (element) {
      if (!element.classList.contains('enabled')) {
        element.addEventListener('click', function (event) {
          var filterList = document.querySelector('[name="filters for ' + event.currentTarget.getAttribute('name') + '"]');
          if (!filterList.classList.contains('expanded')) {
            filterList.classList.add('expanded');
          } else {
            filterList.classList.remove('expanded');
          }
          var icon = event.currentTarget.querySelector('svg');
          if (icon.classList.contains('inverted')) {
            icon.classList.remove('inverted');
          } else {
            icon.classList.add('inverted');
          }
        })
        element.classList.add('enabled');
      }
    }, {once: true});

    document.querySelectorAll('.js-filter-icon').forEach(function (element) {
      element.addEventListener('click', function(event){
        event.currentTarget.parentElement.querySelector('input').click();
      });
    });

    document.querySelectorAll('.js-see-all-checkbox').forEach(function (seeAllCheckbox) {
      seeAllCheckbox.addEventListener('change', function (changedEvent) {
        seeAllCheckbox.parentElement.querySelectorAll('.filter-checkbox').forEach(function (filter) {
          if (!filter.classList.contains('js-see-all-checkbox')) {
            if (!filter.checked) {
              filter.click();
            }
            if (!changedEvent.currentTarget.checked) {
              if (filter.checked) {
                filter.click();
              }
            }
          }
        });
        var seeAllCheckboxLabel = changedEvent.currentTarget.parentElement.children[1];
        if (seeAllCheckboxLabel) {
          if (seeAllCheckboxLabel.innerText === 'Vælg alle') {
            seeAllCheckboxLabel.innerText = 'Fjern alle';
          }
          else {
            seeAllCheckboxLabel.innerText = 'Vælg alle'
          }
        }
      })
    });

    document.querySelectorAll('.see-all-filters-link').forEach(function (link) {
      link.addEventListener('click', function(event) {
        event.currentTarget.parentElement.querySelectorAll('.same-line-text-and-icon').forEach(function (filter){
          if (filter.classList.contains('can-be-hidden')) {
            if (filter.classList.contains('is-hidden')) {
              filter.classList.remove('is-hidden');
            } else if (!filter.classList.contains('is-hidden')) {
              filter.classList.add('is-hidden');
            }
          }
        });

        if (event.currentTarget.innerText.includes('Se færre valg')) {
          event.currentTarget.innerText = '+ Se alle valg';
        }
        else {
          event.currentTarget.innerText = '- Se færre valg';
        }

      });
    });

  }
};

