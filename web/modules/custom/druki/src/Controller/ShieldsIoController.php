<?php

declare(strict_types=1);

namespace Drupal\druki\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides endpoints for shields.io integration.
 *
 * @see https://shields.io/endpoint
 */
final class ShieldsIoController {

  /**
   * Provides endpoint for current Drupal Core version of the website.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response with schema.
   */
  public function drupalCore(): JsonResponse {
    return new JsonResponse([
      'schemaVersion' => 1,
      'label' => 'Drupal Core',
      'message' => \Drupal::VERSION,
      'color' => '#0c76ab',
      'namedLogo' => 'Drupal',
    ]);
  }

}
