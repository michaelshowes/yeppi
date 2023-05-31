/**
 * Applies a specified class to a specificied element when any of
 * it's descendants are focused and removes upon blur.
 *
 * @param  {String} selector - jQuery selector for element
 * @param  {String} [activeClass='-focused-within'] - Class to toggle
 *
 * @examples
 * focusWithin('.site-header-nav-item', '-dropdown-open');
 * focusWithin('.site-header-nav-item');
 *
 */
export default (selector, activeClass = '-focused-within') => {

  // jQuery object for selector
  const $topEl = $(selector);

  $topEl
    // add class on focus of descendants of top-level element
    .on('focus', ':focusable', ({ delegateTarget }) => {
      $(delegateTarget).addClass(activeClass);
    })
    // remove class on blur outside of the current top-level element
    .on('blur', ':focusable', ({ relatedTarget }) => {
      const $currentTopEl = $(relatedTarget).parents(selector);
      $topEl.not($currentTopEl).removeClass(activeClass);
    })
  ;

};
