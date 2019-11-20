/**
 * @file
 * Druki Content TOC behaviors.
 */

(function(Drupal) {

  Drupal.behaviors.drukiContentTOC = {
    attach: function(context, settings) {
      let toc = context.querySelector('.druki-content-toc');

      if (toc && !toc.processed) {
        toc.processed = true;
        this.processTOC(context, toc);
      }
    },

    /**
     * Process TOC element.
     */
    processTOC: function(context, element) {
      let links = element.querySelectorAll('.druki-content-toc__link');
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
          linkInfo.link.classList.remove('druki-content-toc__link--active');

          // 24px is gap for activate link a bit earlier.
          if (linkInfo.top < 24) {
            closestLink = linkInfo.link;
          }
        });

        if (closestLink) {
          closestLink.classList.add('druki-content-toc__link--active');
        }
      };

      // We throttle event for performance improvements. We don't need to
      // execute this calculation on every scroll event. It's too redundant.
      // 16ms is for 60fps smooth updates.
      context.addEventListener('scroll', Drupal.throttle(scrollListener, 16));
    },
  };

})(Drupal);
