/**
 * Provide a centralized class for triggering loading element. Can keep track of
 * multiple loading events, to ensure the element stays on the screen until all
 * AJAX events are truly finished in the rare event multiple ones occur at once.
 */
export default () => {

  class Falcore_Loading {
    constructor() {
      this._loadingEvents = new Map();
      this._markup = '<div class="loading-spinner"><div class="loading-spinner-text">Loading</div></div>';

      if (window.Drupal?.theme) {
        window.Drupal.theme.ajaxProgressIndicatorFullscreen = () => {
          return this._markup;
        };
      }
    }

    /**
     * Register a loading event as ongoing. Either the given name will be
     * registered with a count of 1, or the count will be increased by 1 if
     * already registered.
     *
     * Pass true as the second parameter to deem the event unique. Unique events
     * will not have their count increased if already registered. This is useful
     * if the same starting event may be called multiple times before a single
     * ending event finally occurs.
     */
    addEvent(name, unique) {
      if (!this._loadingEvents.has(name)) {
        this._loadingEvents.set(name, 1);
      } else if (!unique) {
        const newCount = this._loadingEvents.get(name) + 1;
        this._loadingEvents.set(name, newCount);
      }

      this._checkStatus();
    }

    endEvent(name) {
      const currentCount = this._loadingEvents.get(name);

      if (typeof currentCount === 'undefined') {
        return;
      }

      if (currentCount > 1) {
        this._loadingEvents.set(name, currentCount - 1);
      } else {
        this._loadingEvents.delete(name);
      }

      this._checkStatus();
    }

    _checkStatus() {
      if (this._loadingEvents.size) {
        if (!this._$loadingElement || !this._$loadingElement.parent().length) {
          this._$loadingElement = $(this._markup);
          this._$loadingElement.appendTo('body');
        }
      } else {
        // No loading events. If spinner is present, remove it.
        if (this._$loadingElement && this._$loadingElement.parent().length) {
          this._$loadingElement.remove();
        }
        this._$loadingElement = null;
      }
    }
  }

  if (typeof window.falcoreLoading === 'undefined') {
    window.falcoreLoading = new Falcore_Loading();
  }

};
