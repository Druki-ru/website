/**
 * @file
 * Provides mobile contents behaviors.
 *
 * @todo Maybe it's better to created RenderElement + send markup inside
 *   drupalSettings. This will also allows to load JS on specific pages.
 */

(function (Drupal) {

  /**
   * Attach behavior.
   */
  Drupal.behaviors.drukiContentMobileToc = {
    attach: function (context, settings) {
      const element = context.querySelector('.druki-mobile-toc');

      if (element && !element.processed) {
        element.processed = true;
        this.attachEvents(element, context);
      }
    },

    attachEvents: function (mobileToc) {
      const toggleElement = mobileToc.querySelector('.druki-mobile-toc__toggle');

      const listener = () => {
        mobileToc.classList.toggle('druki-mobile-toc--active');
      };

      toggleElement.removeEventListener('click', listener);
      toggleElement.addEventListener('click', listener);
    }
  };

})(Drupal);
