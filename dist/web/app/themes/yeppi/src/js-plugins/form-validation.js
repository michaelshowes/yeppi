/**
 * Hyperform
 *
 * Hyperform is a pure JS implementation of the HTML 5 form validation API.
 *
 * @docs https://hyperform.js.org/docs
 *
 */
import hyperform from 'hyperform';

export default () => {

  if ($('form').length) {

    // Override email validation message for invalid email address
    $('form input[type="email"]').each((index, el) => {
      hyperform.setMessage(el, 'typeMismatch', 'Please enter a valid email address.');
    });

    // Override validation message for empty required <select>
    $('form select').each((index, el) => {
      hyperform.setMessage(el, 'valueMissing', 'Please select an option.');
    });

    // Call hyperform
    hyperform(window, {
      classes: {
        warning: 'error-message',
        validated: '-validated',
        valid: '-valid',
        invalid: '-invalid'
      }
    });
  }
};
