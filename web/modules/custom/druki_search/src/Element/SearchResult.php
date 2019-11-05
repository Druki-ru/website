<?php

namespace Drupal\druki_search\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides render element repsresents search result item.
 *
 * @RenderElement("druki_search_result")
 */
class SearchResult extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'druki_search_result',
      '#title' => NULL,
      '#url' => NULL,
      '#display_url' => NULL,
      '#supporting_text' => NULL,
    ];
  }

}
