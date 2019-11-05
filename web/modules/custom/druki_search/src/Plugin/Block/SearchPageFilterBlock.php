<?php

namespace Drupal\druki_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a search page filter block.
 *
 * @Block(
 *   id = "druki_search_page_filter",
 *   admin_label = @Translation("Search page filter"),
 *   category = @Translation("Druki Search")
 * )
 */
class SearchPageFilterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#create_placeholder' => TRUE,
      '#lazy_builder' => [
        'form_builder:getForm',
        ['Drupal\druki_search\SearchPage\FilterForm'],
      ],
    ];
  }

}
