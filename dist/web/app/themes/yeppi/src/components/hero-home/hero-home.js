export default () => {

  const swiper = new Swiper('.swiper', {  // eslint-disable-line no-unused-vars, no-undef
    speed: 2000,
    keyboard: {
      enabled: true
    },
    pagination: {
      hideOnClick: false,
      clickable: true,
      el: '.swiper-pagination'
    },
    autoplay: {
      enabled: true,
      delay: 6000,
      disableOnInteraction: false
    },
    parallax: {
      enabled: true
    },
    loop: true
  });

};