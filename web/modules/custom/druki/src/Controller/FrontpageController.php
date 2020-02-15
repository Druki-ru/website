<?php

namespace Drupal\druki\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Druki routes.
 */
class FrontpageController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
