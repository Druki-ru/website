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
      const codeBlocks = [].slice.call(document.querySelectorAll('pre code'));

      if ('IntersectionObserver' in window) {
        let codeBlockObserver = new IntersectionObserver(function (entries, observer) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              let codeBlock = entry.target;
              Prism.highlightElement(codeBlock);
              codeBlockObserver.unobserve(codeBlock);
            }
          });
        });

        codeBlocks.forEach(function (codeBlock) {
          codeBlockObserver.observe(codeBlock);
        });
      }
    }
  };

})(Drupal);
