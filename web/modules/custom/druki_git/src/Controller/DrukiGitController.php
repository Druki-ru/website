<?php

namespace Drupal\druki_git\Controller;

use Drupal\Core\Controller\ControllerBase;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Returns responses for Druki — git routes.
 */
class DrukiGitController extends ControllerBase {

  /**
   * Reacts on webhook route call.
   */
  public function webhook() {
    return new JsonResponse(['message' => 'Webhook triggered!']);
  }

}
