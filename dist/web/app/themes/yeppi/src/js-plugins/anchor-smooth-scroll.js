import smoothScroll from './smooth-scroll';

/**
 * Smooth scroll internal links
 */
export default () => {

  $('html').on('click', 'a[href^="#"]:not([href="#"])', (e) => {
    if (e.currentTarget.hash !== '#maincontent') {
      e.preventDefault();
      const target = e.currentTarget.hash,
            $target = $(target);
      if ($target.length) {
        smoothScroll($target);
      }
    }
  });

};
