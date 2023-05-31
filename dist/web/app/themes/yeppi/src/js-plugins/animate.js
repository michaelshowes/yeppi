export default class Animate {

  constructor(selector, rootMargin) {
    this.selector = selector;
    this.rootMargin = rootMargin || '0px 0px -100px 0px';

    if (window.Drupal?.behaviors && window.once) {
      // If using Drupal, use behavior so that AJAXed elements can be animated
      window.Drupal.behaviors.falcoreAnimation = this;
    } else {
      // Otherwise, use document.ready
      $(() => {
        this.observe(document.querySelectorAll(this.selector));
      });
    }
  }

  // Drupal-specific method
  attach(context) {
    this.observe(window.once('falcoreAnimation', this.selector, context));
  }

  getObserver() {
    if (this.observer !== undefined) {
      return this.observer;
    }
    const supportsIntersectionObserver = !!window.IntersectionObserver;
    if (!supportsIntersectionObserver) {
      this.observer = false;
    } else {
      this.observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const $element = $(entry.target),
                  data = $element.data(),
                  animation = data.animation,
                  delay = data.animationDelay || null,
                  duration = data.animationDuration || null
            ;
            $element
              .css({
                animationDelay: delay ? `${delay}ms` : '',
                animationDuration: duration ? `${duration}ms` : ''
              })
              .addClass(`-animated ${animation}`)
            ;
            observer.unobserve(entry.target);
          }
        });
      }, {
        rootMargin: this.rootMargin
      });
    }
    return this.observer;
  }

  observe(elements) {
    const observer = this.getObserver();
    if (!observer) {
      return false;
    }
    elements.forEach(element => {
      observer.observe(element);
    });
    return true;
  }
}
