/**
 * @file
 * dark-mode.js behaviors.
 */

(function (Drupal) {

  Drupal.roach = Drupal.roach || {};
  Drupal.roach.darkMode = Drupal.roach.darkMode || {};

  Drupal.behaviors.roachDarkMode = {
    attach: function (context, settings) {
      // Force init to fast change after page loads.
      Drupal.roach.darkMode.init();

      let toggles = context.querySelectorAll('.dark-mode-toggle');

      toggles.forEach(item => {
        if (item.processed) {
          return;
        }

        item.addEventListener('click', () => {
          Drupal.roach.darkMode.toggle();
        });
      });
    },
  };

  /**
   * Init script for dark mode.
   */
  Drupal.roach.darkMode.init = function () {
    if (!Drupal.roach.darkMode.isEnabled()) {
      return;
    }

    Drupal.roach.darkMode.enable();
  };

  /**
   * Gets status for dark mode.
   *
   * @return boolean
   *   TRUE if dark mode is active, FALSE otherwise.
   */
  Drupal.roach.darkMode.isEnabled = function () {
    return localStorage.getItem('roach-dark-mode') || false;
  };

  /**
   * Toggle dark mode.
   */
  Drupal.roach.darkMode.toggle = function () {
    if (document.documentElement.hasAttribute('data-theme')) {
      Drupal.roach.darkMode.disable();
    }
    else {
      Drupal.roach.darkMode.enable();
    }
  };

  /**
   * Activates dark mode.
   */
  Drupal.roach.darkMode.enable = function () {
    document.documentElement.setAttribute('data-theme', 'dark');
    localStorage.setItem('roach-dark-mode', true);
  };

  /**
   * Disable dark mode.
   */
  Drupal.roach.darkMode.disable = function () {
    document.documentElement.removeAttribute('data-theme');
    localStorage.removeItem('roach-dark-mode');
  };

})(Drupal);
