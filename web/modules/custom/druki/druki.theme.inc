<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki\Drupal\DrupalReleases;
use Drupal\media\MediaInterface;
use Drupal\responsive_image\ResponsiveImageStyleInterface;

/**
 * Implements hook_preprocess_HOOK() for druki-drupal-versions.html.twig.
 */
function template_preprocess_druki_drupal_versions(array &$variables): void {
  $drupal_releases = \Drupal::service('druki.drupal_releases')->get();
  $variables['last_stable_release'] = $drupal_releases['last_stable_release'];
  $variables['last_minor_release'] = $drupal_releases['last_minor_release'];
  $variables['#cache'] = [
    'tags' => [DrupalReleases::CACHE_TAG],
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

/**
 * Prepares variables for druki wiki page templates.
 *
 * Default template: druki-wiki.html.twig.
 *
 * @param array $variables
 *   An associative array containing default theme hook variables.
 */
function template_preprocess_druki_wiki(array &$variables) {
  $variables['user_guide'] = [
    '#theme' => 'druki_official_user_guide',
  ];
}

/**
 * Implements hook_preprocess_HOOK() for druki-media-remote-video-optimized.html.twig.
 */
function template_preprocess_druki_media_remote_video_optimized(array &$variables): void {
  $variables['attributes']['data-video-provider'] = $variables['video_provider'];
  $variables['attributes']['data-video-id'] = $variables['video_id'];

  $variables['thumbnail'] = [
    '#type' => 'responsive_image',
    '#responsive_image_style_id' => $variables['thumbnail_style_id'],
    '#uri' => $variables['thumbnail_uri'],
    '#attributes' => [
      'alt' => $variables['thumbnail_alt'],
      'class' => ['druki-media-remote-video-optimized__thumbnail'],
    ],
  ];
}
