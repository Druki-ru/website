<?php

namespace Drupal\druki_page\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Druki routes.
 */
class DrukiPageController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function downloadPage() {

    $build['content'] = [
      '#theme' => 'druki_page_download',
    ];

    return $build;
  }

  /**
   * Builds the response.
   */
  public function wikiPage() {

    $build['content'] = [
      '#theme' => 'druki_page_wiki',
    ];

    return $build;
  }

}
