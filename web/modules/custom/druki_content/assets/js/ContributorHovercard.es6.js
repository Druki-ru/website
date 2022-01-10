/**
 * @file
 * Provides functionality for contributor hovercards.
 */
(function (Drupal, Popper, once) {

  /**
   * The last instantiated hovercard element.
   *
   * Used to avoid showing multiple hovercards at the same time.
   */
  let hovercardElement = null;

  /**
   * The timout ID.
   */
  let timeout = null;

  /**
   * Fetches hovercard contents.
   */
  function fetchHovercard(element) {
    if (!element.dataset.hovercardAuthor && !element.dataset.hovercardUsername) {
      return;
    }

    let url = new URL('/api/contributor/hovercard', window.location);
    if (element.dataset.hovercardAuthor) {
      url.searchParams.set('author-id', element.dataset.hovercardAuthor);
    }
    if (element.dataset.hovercardUsername) {
      url.searchParams.set('username', element.dataset.hovercardUsername);
    }

    return fetch(url.toString())
      .then(result => result.json())
      .then(result => {
        return result.markup;
      });
  }

  /**
   * Display hovercard conetnts.
   */
  function showHovercard(referenceElement, contents) {
    let template = document.createElement('template');
    template.innerHTML = contents.trim();
    let tooltipElement = template.content.firstChild;
    document.body.append(tooltipElement);

    Popper.createPopper(referenceElement, tooltipElement, {
      modifiers: [
        {
          name: 'offset',
          options: {
            offset: [0, 16],
          }
        }
      ]
    });

    return tooltipElement;
  }

  /**
   * Delete hovercard element.
   */
  function deleteHovercard() {
    if (!hovercardElement) {
      return;
    }

    clearTimeout(timeout);
    hovercardElement.remove();
    hovercardElement = null;
  }

  /**
   * The event callback triggered when mouse is entering reference element.
   */
  async function onReferenceMousenter(event) {
    clearTimeout(timeout);
    let referenceElement = event.target;
    let hovercardContents = await fetchHovercard(referenceElement);
    if (!hovercardContents) {
      return;
    }

    if (hovercardElement) {
      deleteHovercard();
    }

    hovercardElement = showHovercard(referenceElement, hovercardContents);
    referenceElement.addEventListener('mouseleave', () => {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        if (!hovercardElement) {
          return;
        }
        let currentlyHoveredElement = [].slice.call(document.querySelectorAll(':hover')).pop();
        let isTargetingTooltip = currentlyHoveredElement === hovercardElement || hovercardElement.contains(currentlyHoveredElement);
        if (!isTargetingTooltip) {
          deleteHovercard();
        } else {
          hovercardElement.addEventListener('mouseleave', deleteHovercard);
        }
      }, 250);
    });
  }

  Drupal.behaviors.drukiContentContributorHovercard = {
    attach(context) {
      let elements = once('contributor-hovercard', '[data-druki-selector="contributor-hovercard"]', context);
      elements.forEach(element => {
        element.addEventListener('mouseenter', onReferenceMousenter);
      });
    }
  }

})(Drupal, Popper, once)
