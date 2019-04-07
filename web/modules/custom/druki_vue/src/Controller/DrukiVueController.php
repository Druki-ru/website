<?php

namespace Drupal\druki_vue\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Druki - Vue.js routes.
 */
class DrukiVueController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function searchPage() {

    $build['content'] = [
      '#markup' => '<div class="search-init"></div>',
    ];

    return $build;
  }

}
