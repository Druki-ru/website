/**
 * @file
 * Provides common behaviors for whole page.
 */
(function (Drupal) {

  'use strict';

  /**
   * Process mobile functionality if it's found.
   */
  function initMobileMenu(pageElement) {
    let hamburgerElement = pageElement.querySelector('[data-mobile-hamburger]');
    if (!hamburgerElement) {
      return;
    }

    hamburgerElement.addEventListener('click', () => {
      pageElement.classList.toggle('page--mobile-dropdown');
    });
  }

  Drupal.behaviors.loremasterPage = {
    attach(context) {
      let pageElement = context.querySelector('[data-loremaster-selector="page"]');
      if (!pageElement || pageElement.processed) {
        return;
      }
      pageElement.processed;
      initMobileMenu(pageElement);
    }
  }

})(Drupal);
