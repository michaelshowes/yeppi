// -----------------------------------------------------------------------------
// Components
// -----------------------------------------------------------------------------
// import FacetedSearch    from '@components/faceted-search';
// import hamburgerMenu    from '@components/site-header/hamburger-menu';
// import siteHeader       from '@components/site-header';
// import siteHeaderSearch from '@components/site-header/site-header-search';
import socialShare      from '@components/social-share';
import heroHome         from '@components/hero-home';
import contact          from '@components/contact';
import siteHeader       from '@components/site-header';

// -----------------------------------------------------------------------------
// Plugins
// -----------------------------------------------------------------------------
import anchorSmoothScroll from '@plugins/anchor-smooth-scroll';
import Animate            from '@plugins/animate';
import extendJquery       from '@plugins/extend-jquery';
import formValidation     from '@plugins/form-validation';
import jsLinkEvent        from '@plugins/js-link-event';
import loading            from '@plugins/loading';

window.$ = jQuery;

const initializer = {
  components: [
    // ['.hamburger-menu', hamburgerMenu],
    // ['.site-header', siteHeader],
    // ['.site-header-search', siteHeaderSearch],
    ['.social-share', socialShare],
    ['.hero-home', heroHome],
    ['.contact', contact],
    ['.site-header', siteHeader]
  ],
  // behaviors: [
  // ],
  plugins: [
    extendJquery,
    loading,
    formValidation,
    anchorSmoothScroll,
    jsLinkEvent
  ]
};

class FalcoreJS {

  constructor(initializer) {
    this.initializer = initializer;

    // Initialize all plugins
    this.callAll(this.initializer.plugins);

    // Initialize animations
    new Animate('[data-animation]');

    // Document ready
    $(() => {
      this.init();
    });
  }

  init() {
    // Initalize faceted search
    // if ($('.faceted-search').length) {
    //   new FacetedSearch().init();
    // }

    // Initialize all components
    this.callAll(this.initializer.components, this.initComponent);

    // -----------------------------------------------------------------------------
    // Service Worker registration
    // -----------------------------------------------------------------------------
    if (process.env.USE_SERVICEWORKER !== false) {
      window.addEventListener('load', () => {
        navigator.serviceWorker
          .register('/service-worker.js', {
            scope: '/'
          });
        // .then((registration) => { })
        // .catch((registrationError) => { });
      });
    }
  }

  callAll(items, fn = (fn) => fn()) {
    items.forEach(fn);
  }

  initComponent([selector, handler]) {
    const $component = $(selector);

    if (!$component.length) {
      return null;
    }

    return $component.each((i, item) => {
      return handler(item);
    });
  }
}

new FalcoreJS(initializer);
