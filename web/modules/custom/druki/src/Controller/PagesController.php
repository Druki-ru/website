<?php

namespace Drupal\druki\Controller;

/**
 * Returns responses for pages.
 */
final class PagesController {

  /**
   * Builds the response.
   */
  public function downloadPage(): array {
    $build['content'] = [
      '#theme' => 'druki_download',
    ];

    return $build;
  }

  /**
   * Builds the response.
   */
  public function wikiPage(): array {
    $build['content'] = [
      '#theme' => 'druki_wiki',
    ];

    return $build;
  }

}
