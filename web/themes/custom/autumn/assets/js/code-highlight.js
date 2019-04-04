/**
 * @file
 * code-highlight.js behaviors.
 */

(function (Drupal) {

  Drupal.behaviors.codeHighlight = {
    attach: function (context, settings) {
      let elements = context.querySelectorAll('pre code');

      if (elements.length) {
        Object.keys(elements).forEach(item => {
          let element = elements[item];

          element.processed = typeof element.processed === 'undefined' ? false : element.processed;
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
