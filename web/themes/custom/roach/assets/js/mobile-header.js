/**
 * @file
 * Contains mobile-header.js behaviors.
 */

(function (Drupal) {

  Drupal.behaviors.roachMobileHeader = {

    /**
     * The distance in px, after which header will be hidden.
     *
     * Before page will be scrolled to this value, header will remain untouched.
     */
    hideHeaderAfter: 200,

    /**
     * The mobile header element.
     */
    mobileHeaderElement: null,

    /**
     * The class token used for hidden header.
     */
    hiddenClassToken: 'header-mobile--hidden',

    /**
     * Constructs a roachMobileHeader behavior.
     */
    attach: function (context, settings) {
      this.mobileHeaderElement = context.querySelector('.header-mobile');

      if (!this.mobileHeaderElement.processed) {
        this.init();
      }
    },

    /**
     * Initialize behavior.
     */
    init: function () {
      let lastYOffset = 0;

      // Listener for scroll event.
      let scrollListener = () => {
        // Don't process on lg+ breakpoints.
        if (document.documentElement.clientWidth >= 960) {
          return;
        }

        let isThresholdPassed = (window.pageYOffset < this.hideHeaderAfter);
        let isScrolledTop = (window.pageYOffset < lastYOffset);

        if (isThresholdPassed || isScrolledTop) {
          this.show();
        }
        else {
          this.hide();
        }

        lastYOffset = window.pageYOffset;
      };

      window.addEventListener('scroll', Drupal.throttle(scrollListener, 100));

      this.mobileHeaderElement.processed = true;
    },

    /**
     * Hides header.
     */
    hide: function () {
      if (this.mobileHeaderElement.classList.contains(this.hiddenClassToken)) {
        return;
      }

      this.mobileHeaderElement.classList.add(this.hiddenClassToken);
    },

    /**
     * Show header.
     */
    show: function () {
      if (!this.mobileHeaderElement.classList.contains(this.hiddenClassToken)) {
        return;
      }

      this.mobileHeaderElement.classList.remove(this.hiddenClassToken);
    },

  };

})(Drupal);
