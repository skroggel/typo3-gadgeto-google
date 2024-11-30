
/*!
 * GeoPosition
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 * Last updated: 30.11.2024
 * v1.0.0
 */

/*
 * Example for initialization:
 *
 * $(() => {
 *  const geoPosition = new GeoPosition();
 * });
 */
class GeoPosition {

  config = {
    'buttonClass': 'js-gadgetogoogle-geoposition-btn',
    'searchFieldClass' : 'js-gadgetogoogle-geoposition-field'
  };

  /**
   * Constructor
   * @param config
   */
  constructor(config) {
    this.config = {...this.config, ...config }
    this.initGeoPosition();
  }

  /**
   * Init GeoPosition
   */
  initGeoPosition() {

    const buttons = Object.values(document.getElementsByClassName(this.config.buttonClass));
    if (buttons.length) {

      buttons.forEach(button => {
        button.addEventListener('click', (event) => {
          event.preventDefault();

          const button = event.target;
          const form = button.form;
          const searchField = form.getElementsByClassName(this.config.searchFieldClass)[0];

          if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition((position) => {
              let value = position.coords.latitude + ',' + position.coords.longitude;
              searchField.value = value;
              form.submit();
            })
          } else {
            alert('Unfortunately this function is not supported by your browser.')
          }
        });
      });
    }
  }
}
