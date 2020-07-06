/**
 * @file
 * Dark mode switcher.
 */

(function (Drupal) {

  Drupal.behaviors.loremasterDarkMode = {
    attach: function (context, settings) {
      // Force init to fast change after page loads.
      this.init();

      let toggles = context.querySelectorAll('.js-dark-mode-switcher');

      toggles.forEach(item => {
        if (item.processed) {
          return;
        }

        item.addEventListener('click', () => {
          this.toggle();
        });
      });
    },

    /**
     * Init script for dark mode.
     */
    init: function () {
      if (!this.isEnabled()) {
        return;
      }

      this.enable();
    },

    /**
     * Gets status for dark mode.
     *
     * @return boolean
     *   TRUE if dark mode is active, FALSE otherwise.
     */
    isEnabled: function () {
      return localStorage.getItem('loremaster-dark-mode') || false;
    },

    /**
     * Toggle dark mode.
     */
    toggle: function () {
      if (document.documentElement.hasAttribute('data-theme')) {
        this.disable();
      }
      else {
        this.enable();
      }
    },

    /**
     * Activates dark mode.
     */
    enable: function () {
      document.documentElement.setAttribute('data-theme', 'dark');
      localStorage.setItem('loremaster-dark-mode', true);
    },

    /**
     * Disable dark mode.
     */
    disable: function () {
      document.documentElement.removeAttribute('data-theme');
      localStorage.removeItem('loremaster-dark-mode');
    },
  };

})(Drupal);
