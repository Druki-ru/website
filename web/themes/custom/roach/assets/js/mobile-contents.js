/**
 * @file
 * Provides mobile contents behaviors.
 *
 * @todo Maybe it's better to created RenderElement + send markup inside
 *   drupalSettings. This will also allows to load JS on specific pages.
 */

(function (Drupal) {

  Drupal.roach = Drupal.roach || {};
  Drupal.roach.mobileContents = Drupal.roach.mobileContents || {found: false};

  /**
   * Attach behavior.
   */
  Drupal.behaviors.roachMobileContents = {
    attach: function (context, settings) {
      Drupal.roach.mobileContents.init(context, settings);
    },
  };

  /**
   * Initialization for script.
   */
  Drupal.roach.mobileContents.init = function (context, settings) {
    if (Drupal.roach.mobileContents.found) {
      return;
    }

    const contentsElement = context.querySelector('.druki-content-toc');

    if (!contentsElement) {
      return;
    }

    Drupal.roach.mobileContents.found = true;
    const mobileTocElement = Drupal.roach.mobileContents.prepareElement(contentsElement);
    const contentElement = context.querySelector('.druki-content-druki-content-full');
    contentElement.prepend(mobileTocElement);
    Drupal.roach.mobileContents.attachClickEvents(mobileTocElement);
  };

  /**
   * Creates DOM element.
   */
  Drupal.roach.mobileContents.prepareElement = function (contentsElement) {
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
    content.append(contentsElement);
    header.append(toggle);
    wrapper.append(header);
    wrapper.append(content);

    return wrapper;
  };

  /**
   * Adds click event listeners to mobile TOC.
   */
  Drupal.roach.mobileContents.attachClickEvents = function (mobileTocElement) {
    const toggleElement = mobileTocElement.querySelector('.druki-mobile-toc__toggle');

    const listener = () => {
      mobileTocElement.classList.toggle('druki-mobile-toc--active');
    };

    mobileTocElement.removeEventListener('click', listener);
    mobileTocElement.addEventListener('click', listener);
  };

})(Drupal);
