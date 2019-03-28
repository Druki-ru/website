<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_preprocess_HOOK() for druki-category-navigation.html.twig.
 */
function template_preprocess_druki_category_navigation(array &$variables): void {
  $current_route_name = \Drupal::routeMatch()->getRouteName();

  foreach ($variables['links'] as &$link) {
    $link['attributes'] = new Attribute();
    $link['is_active'] = $current_route_name == $link['url']->getRouteName();
  }
}
