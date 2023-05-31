/**
 * Social Share Buttons
 */
export default (el) => {

  $(el).find('button').on('click', ({ currentTarget }) => {
    let url;
    const currPage = window.location.href;

    switch ($(currentTarget).data('service')) {
      case 'facebook':
        url = 'https://www.facebook.com/sharer/sharer.php?u=' + currPage;
        break;
      case 'twitter':
        url = 'https://twitter.com/intent/tweet/?url=' + currPage;
        break;
      case 'linkedin':
        url = 'https://www.linkedin.com/shareArticle?mini=true&url=' + currPage;
        break;
    }

    windowPopup(url);
  });

};

function windowPopup(url) {
  const top = (screen.height / 3) - 150;
  const left = (screen.width / 2) - 250;

  // Calculate the position of the popup so it's centered on the screen.
  window.open(
    url,
    '',
    'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=500,height=300,top=' + top + ',left=' + left
  );
}
