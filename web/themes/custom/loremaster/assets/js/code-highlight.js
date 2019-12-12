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
      if (!'IntersectionObserver' in window) {
        return;
      }

      const codeBlocks = [].slice.call(document.querySelectorAll('pre code'));

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
        if (codeBlock.processed) {
          return;
        }
        codeBlock.processed = true;
        codeBlockObserver.observe(codeBlock);
      });
    }
  };

})(Drupal);
