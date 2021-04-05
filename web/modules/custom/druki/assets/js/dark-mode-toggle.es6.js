/**
 * @file
 * Provides behaviours to dark mode switchers.
 */
(function (Drupal, DarkMode) {

  /**
   * Updates element state.
   *
   * @param element
   *   The element to work with.
   * @param color
   *   The current color scheme.
   * @param isSystem
   *   Indicated is current scheme from user system.
   */
  const updateElement = (element, color, isSystem) => {
    const value = isSystem ? 'auto' : color;
    const radioElement = element.querySelector(`[data-mode-option][value="${value}"]`);
    radioElement.checked = true;
  }

  Drupal.behaviors.darkModeToggle = {
    attach(context) {
      context.querySelectorAll('[data-druki-selector="dark-mode-toggle"]').forEach(toggleElement => {
        if (toggleElement.dataset.processed) {
          return;
        }
        toggleElement.dataset.processed = true;

        // Initial value.
        updateElement(toggleElement, DarkMode.getColorScheme(), DarkMode.isSchemeFromSystem());

        DarkMode.onUpdate((color, isSystem) => {
          updateElement(toggleElement, color, isSystem);
        });

        toggleElement.querySelectorAll('[data-mode-option]').forEach(optionElement => {
          optionElement.addEventListener('click', () => {
            DarkMode.setColorScheme(optionElement.value);
          });
        });
      });
    }
  }

})(Drupal, DarkMode);
