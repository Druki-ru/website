<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

use Drupal\Core\Render\Element;

/**
 * Implements hook_preprocess_HOOK() for druki-drupal-versions.html.twig.
 */
function template_preprocess_druki_drupal_versions(array &$variables): void {
  $drupal_releases = \Drupal::state()->get('druki.drupal_releases', [
    'expires' => 0,
    'last_stable_release' => NULL,
    'last_minor_release' => NULL,
  ]);

  $variables['last_stable_release'] = $drupal_releases['last_stable_release'];
  $variables['last_minor_release'] = $drupal_releases['last_minor_release'];
}
