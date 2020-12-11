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
  public function build(): array {
    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

}
