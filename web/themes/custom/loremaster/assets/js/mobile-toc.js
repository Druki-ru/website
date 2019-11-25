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
  Drupal.behaviors.loremasterMobileToc = {
    attach: function (context, settings) {
      const element = context.querySelector('.druki-content-toc');

      if (element && !element.processed) {
        element.processed = true;
        this.init(element, context);
      }
    },

    init: function (element, context) {
      const elementClone = this.prepareElement(element.cloneNode(true));
      const content = context.querySelector('.druki-content-full');
      content.prepend(elementClone);
      this.attachEvents(elementClone)
    },

    prepareElement: function (element) {
      const wrapper = document.createElement('div');
      wrapper.classList.add('druki-mobile-toc');

      const header = document.createElement('div');
      header.classList.add('druki-mobile-toc__header');

      const toggle = document.createElement('div');
      toggle.classList.add('druki-mobile-toc__toggle');
      toggle.innerText = Drupal.t('Contents');

      const content = document.createElement('div');
      content.classList.add('druki-mobile-toc__content');

      // Place each in another.
      content.append(element);
      header.append(toggle);
      wrapper.append(header);
      wrapper.append(content);

      return wrapper;
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
