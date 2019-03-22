<?php

/***
 * @file
 * Custom theme hook preprocess.
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_preprocess_HOOK() for druki-title.html.twig.
 */
function template_preprocess_druki_title(array &$variables): void {
  foreach ($variables['links'] as &$link) {
    $link_attributes = new Attribute();
    if (isset($link['attributes']) && !($link['attributes'] instanceof Attribute)) {
      if ($link['attributes']) {
        $link['attributes'] = new Attribute($link['attributes']);
      }
      else {
        // Create empty attributes.
        $link['attributes'] = clone $link_attributes;
      }
    }
  }
}
