<?php

namespace Drupal\druki\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Druki routes.
 */
class DrukiController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function buildDownload() {

    $build['content'] = [
      '#theme' => 'druki_download',
    ];

    return $build;
  }

  /**
   * Builds the response.
   */
  public function buildDocs() {

    $build['content'] = [
      '#theme' => 'druki_docs',
    ];

    return $build;
  }

}
