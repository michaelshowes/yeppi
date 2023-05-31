export default () => {

  const $header = $('.js-site-header'),
        $menuBtn = $('.js-hamburger-menu-btn');

  $menuBtn.on('click', () => {
    $header.attr('data-menu-open', $header.attr('data-menu-open') === 'false' ? 'true' : 'false');
  });
};