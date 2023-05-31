import { Loader } from '@googlemaps/js-api-loader';
import { default as axios } from 'axios';

export default () => {

  // Renders Google Map based on address
  getMap();

  function getMap() {

    const mapsApiKey = 'AIzaSyAgq-UrT351Xtpl5wCkXmKqkgXqGVqj1sE',
          street1 = document.getElementById('map').dataset.street,
          street2 = document.getElementById('map').dataset.streettwo,
          city = document.getElementById('map').dataset.city,
          state = document.getElementById('map').dataset.state,
          zip = document.getElementById('map').dataset.zip,
          location = `${street1}, ${street2}, ${city}, ${state} ${zip}`;

    // GET request for geocode data from address
    axios.get('https://maps.googleapis.com/maps/api/geocode/json?', {
      params: {
        address: location,
        key: mapsApiKey
      }
    })
      .then((res) => {
        // Stores Latitude and Longitude data
        const latPos = parseFloat(res.data.results[0].geometry.location.lat),
              lngPos = parseFloat(res.data.results[0].geometry.location.lng),
              location = { lat: latPos, lng: lngPos };

        // Renders Google Map
        const loader = new Loader({
          apiKey: mapsApiKey,
          version: 'weekly'
        });

        loader.load()
          .then(() => {
            const map = new google.maps.Map(document.getElementById('map'), { // eslint-disable-line no-undef
              center: location,
              zoom: 16,
              disableDefaultUI: true,
              zoomControl: true
              // styles: stylesArray
            });

            const contentString = `
            <div class="info-window-content">
              <h3 class="info-company-name">YEPP<span>i</span> NAIL & SPA</h3>
              <div>${street1}</div>
              <div>${street2}</div>
              <div>${city}, ${state} ${zip}</div>
            </div>
            `;
            const infoWindow = new google.maps.InfoWindow({ // eslint-disable-line no-undef
              content: contentString,
              ariaLabel: 'Yeppi Nail Spa'
            });
            const marker = new google.maps.Marker({ // eslint-disable-line no-undef, no-unused-vars
              position: location,
              map: map, // eslint-disable-line object-shorthand
              animation: google.maps.Animation.DROP // eslint-disable-line no-undef
            });
            infoWindow.open({
              anchor: marker,
              map
            });
          });
      })
      .catch((err) => {
        console.log(err); // eslint-disable-line no-console
      });
  }

};
