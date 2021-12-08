<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

declare(strict_types=1);

use Drupal\Core\Render\Element;

/**
 * Prepares variables for druki author templates.
 *
 * Default template: druki-author.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the druki content information
 *   and any fields attached to the entity.
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_druki_author(array &$variables): void {
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }

  $variables['druki_author'] = $variables['elements']['#druki_author'];
  $variables['view_mode'] = $variables['elements']['#view_mode'];
}
