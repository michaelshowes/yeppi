// Open external links in new tab
export default () => {

  $('a:not([target]), a[target=""]')
    .filter('[href^="http"], [href^="//"]')
    .not('[href*="' + window.location.host + '"]')
    .attr('rel', 'noopener')
    .attr('target', '_blank')
  ;

};
