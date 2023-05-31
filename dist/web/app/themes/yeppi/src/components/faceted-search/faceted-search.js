import { Config, Facets } from 'is-search';

export default class Search extends Facets {
  /**
   * Initialize the Search.
   */
  constructor() {

    const cssPrefix = '.faceted-search';

    const config = new Config();
    config.serverSideRendered = true;
    config.keywordParam       = 'keywords';
    config.sortParam          = '_sort';
    config.orderParam         = '_order';
    config.defaultQueryParams = {
      _page: 1,
      keywords: '',
      _limit: 6
    };
    config.defaultSortParams = {
      _sort: 'date',
      _order: 'desc'
    };

    // Get data from backend.
    const dataFromBackend = $(cssPrefix).data();
    config.implementation = dataFromBackend.collection;
    config.endpoint       = encodeURI(dataFromBackend.endpoint);
    config.searchURL      = dataFromBackend.searchUrl ? encodeURI(dataFromBackend.searchURL) : window.location.pathname;

    // Facets
    config.facetOptions = [{
      name: 'topic',
      isMultiCardinality: true,
      default: null
    }, {
      name: 'type',
      isMultiCardinality: false,
      default: null
    }];

    super(config);

    this.totalPages = dataFromBackend.totalPages || null;
    this.facets     = dataFromBackend.facets || null;

    this._keywords          = '';
    this._searchContainer   = cssPrefix;
    this._searchField       = '.js-search-field';
    this._searchSubmit      = '.js-search-submit';
    this._clearButton       = '.js-filters-clear';
    this._resultsContainer  = `${ cssPrefix }-results`;
    this._resultsText       = `${ cssPrefix }-results-text`;
    this._activeFilters     = `${ cssPrefix }-active-facets`;
    this._activeFiltersLink = `${ cssPrefix }-active-facets-link`;
    this._checkFacets       = `${ cssPrefix }-facet[data-type="checkboxes"] input[type="checkbox"]`;
    this._radioFacets       = `${ cssPrefix }-facet[data-type="radios"] input[type="radio"]`;
    this._noResults         = `${ cssPrefix }-no-results`;
    this._sortContainer     = `${ cssPrefix }-results-sort`;
    this._sortElement       = `${ cssPrefix }-results-sort select`;
    this._pagination        = `${ cssPrefix } .pagination`;

    // Event callback for Fetch requests which build the reults and pager.
    $(this)
      // Loading
      .on('isLoading', this._responseIsLoading.bind(this))
      // Main search response
      .on('HandleResponse', this._searchResponse.bind(this))
      // Build pagination
      .on('HandleResponse', this._buildPagination.bind(this))
      // Build sorts
      .on('HandleResponse', this._buildSorts.bind(this))
      // Log errors (they are handled async with the HandleError event to avoid fataling the entire process, and nothing
      // is done with the event by default)
      // eslint-disable-next-line no-console
      .on('HandleError', (event, error) => console.error(error))
    ;

    // UI based events
    $('html')
      // Keyword search on button press
      .on('click', this._searchSubmit, this._keywordSearch.bind(this))
      // Check facet changes
      .on('change', this._checkFacets, this._checkFacetTrigger.bind(this))
      // Radio facet changes
      .on('change', this._radioFacets, this._radioFacetTrigger.bind(this))
      // Clear button press
      .on('click', this._clearButton, this._clearFilters.bind(this))
      // Active filter press
      .on('click', this._activeFiltersLink, this._checkFacetTrigger.bind(this))
      // Sort changes
      .on('change', this._sortElement, this._sortingTrigger.bind(this))
      // Pagination previous click
      .on('click', '.pagination-prev', this.previousPage.bind(this))
      // Pagination next click
      .on('click', '.pagination-next', this.nextPage.bind(this))
      // Pagination page number click
      .on('click', '.pagination-pages a', this._paginationTrigger.bind(this))
    ;

  }

  // Get search container
  get searchContainer() {
    return this._searchContainer || null;
  }

  // Set search container
  set searchContainer(container) {
    this._searchContainer = container;
  }


  /* ========================================================================
   *  Sorting
   * ======================================================================*/

  /**
   * Builds the sorting HTML on load.
   *
   * @private
   * @method _buildSorts
   */
  _buildSorts() {
    const wrapper = $(this._sortElement);
    wrapper.empty();
    this.sorts.map(this._buildSortItem.bind(this, wrapper));
  }

  /**
   * Builds the sorting select list.
   *
   * @private
   * @method _buildSortItem
   * @param { String } wrapper jQuery element.
   * @param { Object } sort The individual sort item.
   */
  _buildSortItem(wrapper, { label, order, value }) {
    const selected = this._isFacetActive('_sort', value) ? 'selected' : '';
    wrapper.append(`<option value="${ value }" data-order="${ order }" ${ selected }>${ label }</option>`);
  }

  /**
   * Trigger the search to update on sorting change.
   *
   * @private
   * @method _sortingTrigger
   * @param { Object } event The jQuery event passed from on().
   */
  _sortingTrigger(event) {
    event.preventDefault();

    const element = $(event.target),
          sort    = element[0].value,
          order   = element[0].selectedOptions[0].getAttribute('data-order');

    this._helperUpdateSorting(sort, order);
  }


  /* ========================================================================
   *  Pagination
   * ======================================================================*/

  /**
   * Callback for build the pagintation based off the returned results.
   *
   * @private
   * @method _buildPagination
   */
  _buildPagination() {

    // Empty out pagination page links
    $(`${ this._pagination } .pagination-pages`).empty();

    // Hide pagination arrows by default.
    // We trigger this once in the constructor() for the inital page load,
    // and this one is for sequential action.
    $(`${ this._pagination } .pagination-nav a`).attr('aria-hidden', 'true');

    // Do not load the pager if we get 1 or less pages to display.
    if (this.totalPages <= 1) {
      return;
    }

    // Show pagination if pages >= 1
    $(`${ this._searchContainer } .pagination`).attr('aria-hidden', 'false');

    // Show pagination arrows
    $(`${ this._pagination } .pagination-nav a`).attr('aria-hidden', 'false').attr('data-disabled', 'false');

    // Disable left arrow on first page
    $(`${ this._pagination } .pagination-prev`).prop('disabled', this.currentPage === 1);

    // Disable right arrow on last page
    $(`${ this._pagination } .pagination-next`).prop('disabled', this.currentPage === this.totalPages);

    this.buildPager().map(this._buildPaginationItem);
  }

  /**
   * Callback for building an individual pager item.
   *
   * @private
   * @method _buildPaginationItem
   * @param { Object } page The page object context for the current search results.
   */
  _buildPaginationItem(page) {
    const { current, link, text, value } = page;

    let pageText = text;

    if (typeof text === 'number') {
      pageText = text.toLocaleString();
    }

    // Create typical page link
    let defaultHTML = `<a href="${ link }" data-value="${ value }"><span class="sr-text">Page </span>${ pageText }</a>`;

    // Create dots
    if (text === '...') {
      defaultHTML = `<span class="pagination-pages-dots">${ text }</span>`;
    }

    // Create current page HTML
    const currentHTML = `<a href="#" data-value="${ value }" data-current="true"><span class="sr-text">Page </span>${ pageText }</a>`;

    $('.pagination-pages').append(current ? currentHTML : defaultHTML);
  }

  /**
   * Callback being used to as the click event for the pagination
   *
   * @private
   * @method _paginationTrigger
   * @param { Object } event The jQuery event passed from on().
   */
  _paginationTrigger(event) {
    event.preventDefault();
    this._helperUpdatePage($(event.target).data('value'));
  }


  /* ========================================================================
   *  Keyword search
   * ======================================================================*/

  /**
   * Callback for processing the keywords entered into the search.
   *
   * @private
   * @method _keywordSearch
   * @param { Object } event The jQuery event passed from on().
   */
  _keywordSearch(event) {
    event.preventDefault();
    this._keywords = $(this._searchField).val();
    this._helperUpdateKeywords(this._keywords);
  }


  /* ========================================================================
   *  Facets
   * ======================================================================*/

  /**
   * Triggers the updating of the search on check facet changes
   *
   * @private
   * @method _checkFacetTrigger
   * @param { Object } event The jQuery event passed from on().
   */
  _checkFacetTrigger(event) {
    event.preventDefault();

    const $element = $(event.currentTarget),
          { name, value } = $element[0]
    ;

    // Call function in IS search to update facets
    this._updateQuery(name, value, 'check');

    return this._updatePushState();
  }

  /**
   * Triggers the updating of the search on check facet changes
   *
   * @private
   * @method _checkFacetTrigger
   * @param { Object } event The jQuery event passed from on().
   */
  _radioFacetTrigger(event) {
    event.preventDefault();

    const $element = $(event.currentTarget),
          { name, value } = $element[0]
    ;

    // Call function in IS search to update facets
    this._updateQuery(name, value, 'radio');

    return this._updatePushState();
  }

  /**
   * Updates query string and pushState on facet change
   *
   * @private
   * @method _updateQuery
   * @param { string } name The name of the input
   * @param { string } value The value of the input
   * @param { string } type The type of the input
   */
  _updateQuery(name, value) {
    const { facetSettings, query } = this,
          current  = query[name] || null,
          settings = facetSettings[name]
    ;

    query[this._pagerParam] = 1;

    // If this is the keywords param...
    if (name === 'keywords') {
      delete query[name];
      query[name] = '';
      $(this._searchField).val('');
      return;
    }

    // If nothing is set go ahead and set it.
    if (!current) {
      query[name] = value;
    }

    // If the value exists go ahead and remove it.
    if (current === value) {
      delete query[name];

      // for radio facets...
      if (!settings.isMultiCardinality) {
        // uncheck this option
        $(`input[name="${ name }"][value="${ value }"]`).prop('checked', false);

        // if the first option has a null value (i.e., it is an "all" option), check it.
        if (!settings.options[0].value) {
          $(`input[name="${ name }"]`).first().prop('checked', true);
        }
      }
    }

    // handle facets where isMultiCardinality is set to false.
    if (!settings.isMultiCardinality) {
      delete query[name];

      // only add this value to query if it's not the value we previously toggled (off)
      if (current !== value) {
        query[name] = value;
      }
    }

    // If the current facet allows multiple values...
    if (settings.isMultiCardinality) {
      const options = (!current ? [] : current.split(',')),
            index   = options.indexOf(value)
      ;

      // toggling to remove this filter:
      // if this option is found in the csv of the applied values for this facet,
      // remove it from array & uncheck it.
      if (index >= 0) {
        delete options[index];
        $(`input[name="${ name }"][value="${ value }"]`).prop('checked', false);
      } else {
        // toggling to apply this filter:
        // add to array of csv values & check it
        options.push(value);
        $(`input[name="${ name }"][value="${ value }"]`).prop('checked', true);
      }

      // update CSV values in URL for this facet
      query[name] = this.compact(options).join(',');
    }
  }


  /* ========================================================================
   *  Loading
   * ======================================================================*/

  /**
   * Callback that allows for a progress loaded to be initialized and deinitialized.
   *
   * @private
   * @method _responseIsLoading
   * @param { Object } event The jQuery event passed from on().
   * @param { Boolean } isLoading true|false
   */
  _responseIsLoading(event, isLoading) {

    if (isLoading === true) {

      window.falcoreLoading.addEvent(this.cssPrefix);

      // Hide results container, pagination and results text
      $(this._activeFilters).attr('aria-hidden', 'true');
      $(this._sortContainer).attr('aria-hidden', 'true');
      $(this._resultsContainer).attr('aria-hidden', 'true');
      $(this._pagination).attr('aria-hidden', 'true');
      $(this._resultsText).attr('aria-hidden', 'true');
      $(this._noResults).attr('aria-hidden', 'true');

    } else {

      // Show results container
      $(this._resultsContainer).attr('aria-hidden', 'false');

      window.falcoreLoading.endEvent(this.cssPrefix);
    }
  }


  /* ========================================================================
   *  Clear
   * ======================================================================*/

  /**
   * Clear all facets
   *
   * @private
   * @method _clearFilters
   * @param { Object } event The jQuery event passed from on().
   */
  _clearFilters(event) {
    event.preventDefault();
    event.stopPropagation();

    const { query } = this;

    // Set to page 1
    query._page = 1;

    // Set to initial sort
    this.query._sort  = this.defaultSortParams._sort;
    this.query._order = this.defaultSortParams._order;

    // Remove keywords
    query.keywords = '';
    $(this._searchField).val('');

    // Re-init facets
    this._initFacets();
    $(this._checkFacets).prop('checked', false);
    $(this._radioFacets).prop('checked', false);

    // If the first radio button has a null value (for all/any), check it
    if (!$(this._radioFacets).first().val()) {
      $(this._radioFacets).first().prop('checked', true);
    }

    // Update push state for back/forward buttons
    this._updatePushState();

    // Hide active filters container
    $(this._activeFilters).attr('aria-hidden', 'true');
  }


  /* ========================================================================
   *  Search Response
   * ======================================================================*/

  /**
   * Callback for the primary Fetch responses.
   *
   * @private
   * @method _searchResponse
   * @param { Object } event The jQuery event passed from on().
   * @param { JSON } response The raw response object.
   */
  _searchResponse(event, response) {

    const { meta, results } = response,
          container = $(this._resultsContainer),
          numResults = meta.totalResults.toLocaleString(),
          $activeLinks = $('.faceted-search-active-facets-links'),
          $activeLink = $('.faceted-search-active-facets-link')
    ;

    $activeLink.remove();

    if (meta.activeFacets.length !== 0) {
      meta.activeFacets.map((el) => {
        $activeLinks.prepend(`<button type="button" class="faceted-search-active-facets-link" name="${ el.name }" value="${ el.value }">${ el.label }</button>`);
      });
      $(this._activeFilters).attr('aria-hidden', 'false');
    } else {
      $(this._activeFilters).attr('aria-hidden', 'true');
    }

    // Empty out the current search results container and results text
    container.empty();

    // Stop if there are no results to show.
    if (!results.length) {
      $(this._resultsText).attr('aria-hidden', 'true');
      $(this._noResults).attr('aria-hidden', 'false');
      $(this._pagination).attr('aria-hidden', 'true');
      $(this._sortContainer).attr('aria-hidden', 'true');
      return;
    }

    // Hide no results message and show sort
    $(this._noResults).attr('aria-hidden', 'true');
    $(this._sortContainer).attr('aria-hidden', 'false');

    // Create results text and show
    $('.faceted-search-results-text-num').text(numResults);
    $(this._resultsText).attr('aria-hidden', 'false');

    // Build and output each individual search result.
    results.map(({rendered}) => {
      container.append(rendered);
    });
  }
}
