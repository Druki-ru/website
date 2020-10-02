/**
 * @file
 * Remote video media optimized behaviors.
 */

(function (Drupal) {

  Drupal.behaviors.drukiMediaRemoteVideoOptimized = {
    attach: function (context, settings) {
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
        let elements = context.querySelectorAll('.druki-media-remote-video-optimized');

        Object.keys(elements).forEach(key => {
          let element = elements[key];
          element.processed = typeof element.processed === 'undefined' ? false : element.processed;

          if (!element.processed) {
            // Mark as processed. Replace for jquery.once.
            element.processed = true;

            let provider = element.getAttribute('data-video-provider');
            let previewElement = element.querySelector('.druki-media-remote-video-optimized__preview');
            let playElement = element.querySelector('.druki-media-remote-video-optimized__play');
            let loadingElement = element.querySelector('.druki-media-remote-video-optimized__loading');

            if (provider === 'YouTube') {
              this.handleYouTube(element, previewElement, playElement, loadingElement);
            }
          }
        });
      });
    },

    /**
     * Handler for YouTube provider.
     */
    handleYouTube: function (element, previewElement, playElement, loadingElement) {
      let videoId = element.getAttribute('data-video-id');
      let iframeUrl = new URL('https://www.youtube.com/embed/' + videoId);
      iframeUrl.searchParams.set('autoplay', 1);
      iframeUrl.searchParams.set('feature', 'oembed');

      previewElement.addEventListener('click', e => {
        let oembedIframe = document.createElement('iframe');
        oembedIframe.setAttribute('src', iframeUrl.href);
        oembedIframe.setAttribute('frameborder', 0);
        oembedIframe.classList.add('druki-media-remote-video-optimized__iframe');
        // Do not show iframe till it loads.
        oembedIframe.classList.add('druki-media-remote-video-optimized__iframe--hidden');

        // Add iframe on page.
        element.append(oembedIframe);
        // Add active indicator for loading element.
        loadingElement.classList.add('druki-media-remote-video-optimized__loading--active');
        // Remove play element.
        playElement.remove();

        // When iframe is fully loaded.
        oembedIframe.onload = () => {
          // Remove preview element from DOM.
          previewElement.remove();
          // Remove hidden class that iframe will visible to user.
          oembedIframe.classList.remove('druki-media-remote-video-optimized__iframe--hidden');
        }
      });
    },
  };

})(Drupal);
