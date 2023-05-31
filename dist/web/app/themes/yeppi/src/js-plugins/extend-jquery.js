/**
 * Extending jQuery
 */
export default () => {

  /**
   * Create :focusable selector for jQuery
   *
   * @examples
   * $(:focusable).on('focus', () => { ... });
   * $(.element > :focusable).on('focus', () => { ... });
   * $variable.find(':focusable').on('focus', () => { ... });
   *
   */
  $.extend($.expr[':'], {
    focusable(el) {
      return $(el).is('a, button, :input, [tabindex]');
    }
  });

  // $.fn.slideFadeToggle = function(speed, easing, callback) {
  //   return this.animate({
  //     opacity: 'toggle',
  //     height: 'toggle'
  //   }, speed, easing, callback);
  // };
  //
  // $.fn.slideFadeIn = function(speed, easing, callback) {
  //   return this.animate({
  //     opacity: 'show',
  //     height: 'show',
  //     marginTop: 'show',
  //     marginBottom: 'show',
  //     paddingTop: 'show',
  //     paddingBottom: 'show'
  //   }, speed, easing, callback);
  // };
  //
  // $.fn.slideFadeOut = function(speed, easing, callback) {
  //   return this.animate({
  //     opacity: 'hide',
  //     height: 'hide',
  //     marginTop: 'hide',
  //     marginBottom: 'hide',
  //     paddingTop: 'hide',
  //     paddingBottom: 'hide'
  //   }, speed, easing, callback);
  // };

};
