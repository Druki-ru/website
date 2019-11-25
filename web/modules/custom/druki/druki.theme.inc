<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Implements hook_preprocess_HOOK() for druki-drupal-versions.html.twig.
 */
function template_preprocess_druki_drupal_versions(array &$variables): void {
  $variables['bem_block'] = 'druki-drupal-versions';

  $drupal_releases = \Drupal::state()->get('druki.drupal_releases', [
    'expires' => 0,
    'last_stable_release' => NULL,
    'last_minor_release' => NULL,
  ]);

  $variables['last_stable_release'] = $drupal_releases['last_stable_release'];
  $variables['last_minor_release'] = $drupal_releases['last_minor_release'];
}

/**
 * Implements hook_preprocess_HOOK() for 'druki-card.html.twig'.
 */
function template_preprocess_druki_card(array &$variables): void {
  $variables['bem_block'] = 'druki-card';

  if (isset($variables['style'])) {
    $variables['bem_modifiers'][] = $variables['style'];
  }
}

/**
 * Implements hook_preprocess_HOOK() for 'druki-header-branding-navigation.html.twig'.
 */
function template_preprocess_druki_header_branding_navigation(array &$variables): void {
  $variables['site_logo'] = [
    '#theme' => 'image',
    '#uri' => theme_get_setting('logo.url'),
    '#alt' => new TranslatableMarkup('Home'),
    '#attributes' => [
      'class' => ['druki-header-branding-navigation__logo'],
    ],
  ];
}

/**
 * Implements hook_preprocess_HOOK() for 'druki-mobile-header.html.twig'.
 */
function template_preprocess_druki_mobile_header(array &$variables): void {
  $variables['site_logo'] = [
    '#theme' => 'image',
    '#uri' => theme_get_setting('logo.url'),
    '#alt' => new TranslatableMarkup('Home'),
    '#attributes' => [
      'class' => ['druki-mobile-header__logo'],
    ],
  ];
}
