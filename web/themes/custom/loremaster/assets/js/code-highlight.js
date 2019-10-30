/**
 * @file
 * code-highlight.js behaviors.
 */

(function (Drupal) {

  Drupal.behaviors.loremasterCodeHighlight = {
    attach: function (context, settings) {
      let elements = context.querySelectorAll('pre code');

      if (elements.length) {
        Object.keys(elements).forEach(item => {
          let element = elements[item];
          if (!element.processed) {
            // Mark as processed. Replace for jquery.once.
            element.processed = true;

            let language = element.classList[0];
            // Add prism class.
            element.classList.add('code-highlight-processed');
          }
        });
      }
    }
  };

})(Drupal);
