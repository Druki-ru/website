/**
 * @file
 * Druki Header branding navigation behaviors.
 */
(function (Drupal) {

  Drupal.behaviors.drukiHeaderBrandingNavigation = {
    attach: function (context, settings) {
      let trigger;
      if (window.requestIdleCallback) {
        trigger = (callback) => {
          requestIdleCallback(callback)
        }
      }
      else {
        // Fallback for browsers doesn't support IDLE callbacks.
        trigger = (callback) => {
          callback()
        }
      }

      trigger(() => {
        this.attachNavigation(context, settings);
      });
    },

    attachNavigation: function (context, settings) {
      const burgerEl = context.querySelector('.js-header-branding-burger');
      const dropdownEl = context.querySelector('.js-header-branding-dropdown');

      if (burgerEl && !burgerEl.processed && dropdownEl) {
        burgerEl.processed = true;

        const clickListener = () => {
          dropdownEl.classList.toggle('druki-header-branding-navigation__dropdown--active')
        };
        burgerEl.addEventListener('click', clickListener);
      }
    }
  };

})(Drupal);
