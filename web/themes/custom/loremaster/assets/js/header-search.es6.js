/**
 * @file
 * Provides behaviors for header search form.
 */
(function (Drupal) {

  Drupal.behaviors.loremasterHeaderSearch = {
    attach(context) {
      const formElement = context.querySelector('[data-loremaster-selector="header-search"]');
      if (!formElement || formElement.processed) {
        return;
      }
      formElement.processed = true;
      const activeClass = formElement.dataset.classActive || 'is-active';
      const toggleElement = formElement.querySelector('[data-toggle]');
      if (!toggleElement) {
        return;
      }
      const bodyElement = document.querySelector('body');
      toggleElement.addEventListener('click', () => {
        formElement.classList.toggle(activeClass);
        bodyElement.classList.toggle('is-search-expanded');
      });
    }
  }

})(Drupal);
