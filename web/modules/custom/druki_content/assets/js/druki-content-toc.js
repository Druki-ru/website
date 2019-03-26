/**
 * @file
 * Druki Content TOC behaviors.
 */

(function(Drupal) {

  /**
   * The selector name for TOC element.
   */
  const TOC_SELECTOR = '.druki-content-toc';

  /**
   * The class name applied when it processed.
   */
  const TOC_SELECTOR_PROCESSED = TOC_SELECTOR + '--processed';

  /**
   * Link selector inside TOC.
   */
  const TOC_LINK_SELECTOR = TOC_SELECTOR + '__menu-item-link';

  /**
   * Class which added to active TOC link.
   */
  const TOC_LINK_ACTIVE_CLASS = 'druki-content-toc__menu-item-link--active';

  Drupal.behaviors.drukiContentTOC = {
    attach: function(context, settings) {
      let tocs = context.querySelectorAll(
        `${TOC_SELECTOR}:not(${TOC_SELECTOR_PROCESSED})`,
      );

      if (tocs.length) {
        tocs.forEach(element => {
          this.processTOC(context, element);
        });
      }
    },

    /**
     * Process TOC element.
     */
    processTOC: function(context, element) {
      let links = element.querySelectorAll(TOC_LINK_SELECTOR);
      this.attachEvents(context, links);
    },

    /**
     * Find heading for link and it's position.
     */
    processLinks: function(context, links) {
      let result = [];

      links.forEach(link => {
        let anchor = link.getAttribute('href');
        let anchorId = anchor.substring(1);
        let headingForAnchor = context.getElementById(anchorId);
        let headingBounding = headingForAnchor.getBoundingClientRect();

        result.push({
          link: link,
          top: headingBounding.top,
        });
      });

      return result;
    },

    /**
     * Attach all needed events.
     */
    attachEvents: function(context, links) {
      let scrollListener = event => {
        let linksWithPosition = this.processLinks(context, links);
        let closestLink = null;

        linksWithPosition.forEach(linkInfo => {
          linkInfo.link.classList.remove(TOC_LINK_ACTIVE_CLASS);

          if (linkInfo.top < 0) {
            closestLink = linkInfo.link;
          }
        });

        if (closestLink) {
          closestLink.classList.add(TOC_LINK_ACTIVE_CLASS);
        }
      };

      // We throttle event for performance improvements. We don't need to
      // execute this calculation on every scroll event. It's too redundant.
      // 16ms is for 60fps smooth updates.
      context.addEventListener('scroll', Drupal.throttle(scrollListener, 16));
    },
  };

})(Drupal);
