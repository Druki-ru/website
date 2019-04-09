/**
 * @file
 * Mega menu behaviors.
 */
(function(Drupal) {

  /**
   * Delay before menu shows up in ms.
   */
  const SHOW_DELAY = 250;

  /**
   * Delay after which menu will hide in ms.
   */
  const HIDE_DELAY = 250;

  let hideTimeout, showTimeout;

  Drupal.behaviors.autumnMegaMenu = {
    attach: function(context, settings) {
      // Select all elements that triggers mega-menu to show up.
      let megaMenuTriggers = context.querySelectorAll(
        '[data-mega-menu-id-selector]'
      );
      // Select all mega-menu elements.
      let megaMenuElements = context.querySelectorAll('.mega-menu');

      if (megaMenuTriggers.length && megaMenuElements.length) {
        megaMenuTriggers.forEach(element => {
          this.attachEvents(element, megaMenuElements, context);
        });
      }
    },

    attachEvents: function(triggerElement, megaMenuElements, context) {
      triggerElement.processed = typeof triggerElement.processed === 'undefined' ? false : triggerElement.processed;

      // Replaces core/jquery.once.
      if (triggerElement.processed) {
        return;
      }

      // Mark element as processed.
      triggerElement.processed = true;

      let megaMenuId = triggerElement.getAttribute('data-mega-menu-id-selector');

      let megaMenu = context.querySelector(
        `[data-mega-menu-id="${megaMenuId}"]`
      );

      triggerElement.addEventListener('mouseenter', {
        handleEvent: function(event) {
          clearTimeout(hideTimeout);

          showTimeout = setTimeout(() => {
            // Deactivate all previously activated and visible mega menus.
            megaMenuElements.forEach(element => {
              element.classList.remove('mega-menu--active');
            });

            // Activate main mega menu wrapper.
            megaMenu.classList.add('mega-menu--active');
          }, SHOW_DELAY);
        },
      });

      triggerElement.addEventListener('mouseleave', {
        handleEvent: function(event) {
          clearTimeout(showTimeout);
          hideTimeout = setTimeout(() => {
            megaMenu.classList.remove('mega-menu--active');
          }, HIDE_DELAY);
        },
      });
    },
  };

})(Drupal);
