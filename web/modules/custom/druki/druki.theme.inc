<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\MediaInterface;
use Drupal\responsive_image\ResponsiveImageStyleInterface;

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

/**
 * Implements hook_preprocess_HOOK() for 'druki-official-user-guide.html.twig'.
 *
 * // @todo cache tags.
 */
function template_preprocess_druki_official_user_guide(array &$variables) {
  $user_guide_settings = \Drupal::config('druki.official_user_guide_settings');
  /** @var \Drupal\media\MediaStorage $media_storage */
  $media_storage = \Drupal::entityTypeManager()->getStorage('media');
  $media_view_builder = \Drupal::entityTypeManager()->getViewBuilder('media');
  /** @var \Drupal\file\FileStorageInterface $file_storage */
  $file_storage = \Drupal::entityTypeManager()->getStorage('file');
  /** @var \Drupal\Core\Entity\EntityStorageInterface $responsive_image_style_storage */
  $responsive_image_style_storage = \Drupal::entityTypeManager()->getStorage('responsive_image_style');
  $image_settings = $user_guide_settings->get('image');

  /** @var \Drupal\media\MediaInterface $image_media */
  $image_media = $media_storage->load($image_settings['media']);
  $image_style = $responsive_image_style_storage->load($image_settings['style']);
  $variables['image'] = NULL;
  // Check is media and style is really existed.
  if ($image_media instanceof MediaInterface && $image_style instanceof ResponsiveImageStyleInterface) {
    $fid = $image_media->getSource()->getSourceFieldValue($image_media);
    /** @var \Drupal\file\FileInterface $file */
    $file = $file_storage->load($fid);
    $variables['image'] = [
      '#type' => 'responsive_image',
      '#responsive_image_style_id' => $image_style->id(),
      '#uri' => $file->getFileUri(),
      '#attributes' => [
        'alt' => new TranslatableMarkup('Official user guide'),
      ],
    ];
  }
}
