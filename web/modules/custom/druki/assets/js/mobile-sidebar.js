/**
 * @file
 * Druki Header branding navigation behaviors.
 */
(function (Drupal) {

  Drupal.behaviors.drukiMobileSidebar = {
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
        this.attachEvents(context, settings);
      });
    },

    attachEvents: function (context, settings) {
      const buttonElement = context.querySelector('.js-mobile-sidebar-button');
      const sidebarElement = context.querySelector('.js-mobile-sidebar');
      const overlayElement = context.querySelector('.js-mobile-sidebar-overlay');
      const closeElement = context.querySelector('.js-mobile-sidebar-close');

      const clickListener = () => {
        document.querySelector('body').classList.toggle('js-mobile-sidebar-active');
      };

      if (buttonElement && !buttonElement.processed && sidebarElement) {
        buttonElement.processed = true;
        buttonElement.addEventListener('click', clickListener);

        // Overlay is optional.
        if (overlayElement) {
          overlayElement.addEventListener('click', clickListener);
        }

        // Close button is optional but make sure user can close sidebar.
        if (closeElement) {
          closeElement.addEventListener('click', clickListener)
        }
      }
    }
  };

})(Drupal);
