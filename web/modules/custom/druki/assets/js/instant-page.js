/**
 * @file
 * Provides wrapper for instant.page library.
 *
 * @see https://instant.page/
 */
(function (Drupal) {

  'use strict';

  /**
   * The list of patterns with blacklisted links.
   *
   * Some links don't need to be prefetch in any situation. F.e. /user/logout
   * prefetch will logout user without it noticing and intention. But when page
   * will be changed or refreshed he will be anonymous user.
   *
   * Also it's a good to to exclude pages which will redirect as their response.
   * This just not make sense to preload.
   *
   * This list contains JavaScript regexp patterns.
   */
  const blackList = [
    '\/user\/login',
    '\/user\/logout',
    '\/druki_content\/.*\/edit-remote',
    '\/druki_content\/.*\/history-remote',
  ];

  Drupal.behaviors.drukiInstantPage = {
    attach: function (context, settings) {
      this.blackListHandler(context);
    },

    /**
     * Handles black listed links in CPU intense free mode.
     */
    blackListHandler: function (context) {
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
        const intersectionObserver = new IntersectionObserver((entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              const linkElement = entry.target;
              intersectionObserver.unobserve(linkElement);
              this.processBlackListLink(linkElement);
            }
          })
        });

        context.querySelectorAll('a').forEach((linkElement) => {
          intersectionObserver.observe(linkElement);
        })
      });
    },

    /**
     * Process link and disable prefetch if it's blacklisted.
     */
    processBlackListLink: function (linkElement) {
      // Support only for internal links. External not prefetched at all.
      if (linkElement.origin != location.origin) {
        return;
      }

      // Build regexp.
      const regexp = '^(?:' + blackList.join('|') + ')$';

      console.log(linkElement.pathname, linkElement.pathname.match(regexp), regexp);

      if (linkElement.pathname.match(regexp)) {
        linkElement.dataset.noInstant = true;
      }
    }
  };

})(Drupal);
