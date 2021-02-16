/**
 * @file
 * code-highlight.js behaviors.
 */

(function (Drupal) {

  /**
   * Highlight the code blocks.
   *
   * We highlight only visible code blocks on the page to increase performance
   * for mobile devices on pages where a lot of code needs to be highlighted.
   */
  Drupal.behaviors.loremasterCodeHighlight = {
    attach: function (context, settings) {
      if (!window.IntersectionObserver) {
        return;
      }

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
        const intersectionObserver = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              let codeBlock = entry.target;
              Prism.highlightElement(codeBlock);
              intersectionObserver.unobserve(codeBlock);
            }
          });
        });

        [].slice.call(document.querySelectorAll('pre code')).forEach(function (codeBlock) {
          if (codeBlock.processed) {
            return;
          }
          codeBlock.processed = true;
          intersectionObserver.observe(codeBlock);
        });
      });
    }
  };

})(Drupal);
