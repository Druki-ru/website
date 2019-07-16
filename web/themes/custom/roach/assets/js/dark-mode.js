/**
 * @file
 * dark-mode.js behaviors.
 */

(function (Drupal) {

  Drupal.roachDarkMode = Drupal.roachDarkMode || {};

  Drupal.behaviors.roachDarkMode = {
    attach: function (context, settings) {
      // Force init to fast change after page loads.
      Drupal.roachDarkMode.init();

      let toggles = context.querySelectorAll('.dark-mode-toggle');

      toggles.forEach(item => {
        if (item.processed) {
          return;
        }

        item.addEventListener('click', () => {
          Drupal.roachDarkMode.toggle();
        });
      });
    },
  };

  /**
   * Init script for dark mode.
   */
  Drupal.roachDarkMode.init = function () {
    if (!Drupal.roachDarkMode.isEnabled()) {
      return;
    }

    Drupal.roachDarkMode.enable();
  };

  /**
   * Gets status for dark mode.
   *
   * @return boolean
   *   TRUE if dark mode is active, FALSE otherwise.
   */
  Drupal.roachDarkMode.isEnabled = function () {
    return localStorage.getItem('roach-dark-mode') || false;
  };

  /**
   * Toggle dark mode.
   */
  Drupal.roachDarkMode.toggle = function () {
    if (document.documentElement.hasAttribute('data-theme')) {
      Drupal.roachDarkMode.disable();
    }
    else {
      Drupal.roachDarkMode.enable();
    }
  };

  /**
   * Activates dark mode.
   */
  Drupal.roachDarkMode.enable = function () {
    document.documentElement.setAttribute('data-theme', 'dark');
    localStorage.setItem('roach-dark-mode', true);
  };

  /**
   * Disable dark mode.
   */
  Drupal.roachDarkMode.disable = function () {
    document.documentElement.removeAttribute('data-theme');
    localStorage.removeItem('roach-dark-mode');
  };

})(Drupal);
