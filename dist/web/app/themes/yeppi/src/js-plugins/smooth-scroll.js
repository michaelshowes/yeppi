/**
 * Smooth scroll to a target point.
 *
 * @param  {jQuery|Element|Number} target - target element to scroll to, or raw
 *   offset number if precalculated
 * @param  {Boolean} [changeAnchor=true] - whether to change the browser URL
 *   hash after making the scroll. Defaults to true, but requires that an
 *   element with an ID be passed to actually happen.
 */
export default (target, changeAnchor = true) => {
  function changeAnchorForTarget(target) {
    if (target instanceof jQuery && target.attr('id')) {
      history.pushState(null, null, '#' + target.attr('id'));
    }
  }

  // For accessibility, focuses on the targeted element
  function focusOnTarget(target) {
    if (target instanceof jQuery) {
      target.focus();
      // Check that the target actually received focus. If not, it is not
      // normally focusable and we need to mark it as focusable
      if (!target.is(':focus')) {
        target.attr('tabindex', '-1');
        target.focus();
      }
    }
  }

  if (target instanceof jQuery && !target.length) {
    // Fail silently if passed an empty jQuery object
    return;
  }

  // If passed a vanilla DOM object, instantiate jQuery
  if (target instanceof Element) {
    target = $(target);
  }

  let scrollTop;
  if (!(target instanceof jQuery)) {
    scrollTop = Number(target);
    if (Number.isNaN(scrollTop)) {
      throw 'invalid smooth scroll target ' + target;
    }
  } else {
    scrollTop = target.offset().top;
  }

  const reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (reducedMotionQuery.matches) {
    // If user has reduced motion preference, don't animate, just jump
    $('html, body').scrollTop(scrollTop);
    if (changeAnchor) {
      changeAnchorForTarget(target);
    }
    focusOnTarget(target);
  } else {
    // Animate the user to the target point
    $('html, body').stop().animate({
      scrollTop
    }, 500, () => {
      if (changeAnchor) {
        changeAnchorForTarget(target);
        focusOnTarget(target);
      }
    });
  }
};
