<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki\Drupal\DrupalReleases;
use Drupal\druki\Form\FrontpageSettings;
use Drupal\druki\Form\OfficialUserGuideSettingsForm;

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
 */
function template_preprocess_druki_official_user_guide(array &$variables): void {
  $variables['image'] = NULL;
  $media_id = \Drupal::state()->get(OfficialUserGuideSettingsForm::IMAGE_STORAGE_KEY);
  if (!$media_id) {
    return;
  }

  /** @var \Drupal\media\MediaStorage $media_storage */
  $media_storage = \Drupal::entityTypeManager()->getStorage('media');
  /** @var \Drupal\media\MediaInterface $image_media */
  $image_media = $media_storage->load($media_id);
  if (!$image_media) {
    return;
  }

  $user_guide_settings = \Drupal::config('druki.official_user_guide_settings');
  $image_style_id = $user_guide_settings->get('image_style');
  if (!$image_style_id) {
    return;
  }

  /** @var \Drupal\Core\Entity\EntityStorageInterface $responsive_image_style_storage */
  $responsive_image_style_storage = \Drupal::entityTypeManager()->getStorage('responsive_image_style');
  $image_style = $responsive_image_style_storage->load($image_style_id);
  if (!$image_style) {
    return;
  }

  $source_field = $image_media->getSource()->getConfiguration()['source_field'];
  /** @var \Drupal\file\FileInterface $file */
  $file = $image_media->get($source_field)->entity;
  $variables['image'] = [
    '#type' => 'responsive_image',
    '#responsive_image_style_id' => $image_style_id,
    '#uri' => $file->getFileUri(),
    '#attributes' => [
      'alt' => new TranslatableMarkup('Official user guide'),
    ],
  ];

  $cache = new CacheableMetadata();
  $cache->addCacheableDependency($user_guide_settings);
  $cache->addCacheableDependency($image_style);
  $cache->addCacheableDependency($image_media);
  $cache->applyTo($variables);
}

/**
 * Prepares variables for druki wiki page templates.
 *
 * Default template: druki-wiki.html.twig.
 *
 * @param array $variables
 *   An associative array containing default theme hook variables.
 */
function template_preprocess_druki_wiki(array &$variables): void {
  $variables['user_guide'] = [
    '#theme' => 'druki_official_user_guide',
  ];
}

/**
 * Prepares variables for druki frontpage download templates.
 *
 * Default template: druki-frontpage-download.html.twig.
 *
 * @param array $variables
 *   An associative array containing default theme hook variables.
 */
function template_preprocess_druki_frontpage_download(array &$variables): void {
  $variables['image'] = NULL;
  $download_media_id = \Drupal::state()->get(FrontpageSettings::DOWNLOAD_MEDIA_STORAGE_KEY);
  if (!$download_media_id) {
    return;
  }

  /** @var \Drupal\media\MediaStorage $media_storage */
  $media_storage = \Drupal::entityTypeManager()->getStorage('media');
  /** @var \Drupal\media\MediaInterface $download_media */
  $download_media = $media_storage->load($download_media_id);
  if (!$download_media) {
    return;
  }

  $frontpage_settings = \Drupal::config('druki.frontpage.settings');
  $download_media_image_style_id = $frontpage_settings->get('download.image_style');
  if (!$download_media_image_style_id) {
    return;
  }

  /** @var \Drupal\Core\Entity\EntityStorageInterface $responsive_image_style_storage */
  $responsive_image_style_storage = \Drupal::entityTypeManager()->getStorage('responsive_image_style');
  $download_image_style = $responsive_image_style_storage->load($download_media_image_style_id);
  if (!$download_image_style) {
    return;
  }

  $source_field = $download_media->getSource()->getConfiguration()['source_field'];
  /** @var \Drupal\file\FileInterface $file */
  $file = $download_media->get($source_field)->entity;
  $variables['image'] = [
    '#type' => 'responsive_image',
    '#responsive_image_style_id' => $download_media_image_style_id,
    '#uri' => $file->getFileUri(),
    '#attributes' => [
      'alt' => new TranslatableMarkup('Druplicon'),
      // This image is part of FCP, disable lazyload because it hurts LCP.
      // This trick improve LCP (Desktop mode) from 950ms (96/100) to 732ms
      // (99/100).
      'loading' => 'eager',
    ],
  ];

  $cache = new CacheableMetadata();
  $cache->addCacheableDependency($frontpage_settings);
  $cache->addCacheableDependency($download_image_style);
  $cache->addCacheableDependency($download_media);
  $cache->applyTo($variables);
}

/**
 * Prepares variables for photoswipe responsive image.
 *
 * PhotoSwipe module theme hooks expected to be used with FieldItem objects,
 * which makes it very hard to use in some cases and not usable at all if you
 * don't have field item for it. This theme hooks simplify their theme hooks and
 * makes it require only URI of an image. This allows to be used anywhere as
 * long as you have an image URI.
 *
 * Default template: druki-photoswipe-responsive-image.html.twig.
 *
 * @param array $variables
 *   An associative array containing default theme hook variables:
 *   - uri: The URI of image to be displayed.
 *   - alt: The image alternative description.
 *   - responsive_image_style_id: The Responsive Image Style ID.
 *   - photoswipe_image_style_id: The image style to be used for PhotoSwipe
 *     modal.
 */
function template_preprocess_druki_photoswipe_responsive_image(array &$variables): void {
  $variables['image'] = [];
  $image_style_storage = \Drupal::entityTypeManager()->getStorage('image_style');
  /** @var \Drupal\image\ImageStyleInterface $image_style */
  $image_style = $image_style_storage->load($variables['photoswipe_image_style_id']);
  if (!$image_style) {
    return;
  }
  /** @var \Drupal\Core\Image\ImageFactory $image_factory */
  $image_factory = \Drupal::service('image.factory');
  $image_file = $image_factory->get($variables['uri']);
  if (!$image_file->isValid()) {
    return;
  }

  $variables['image'] = [
    '#theme' => 'responsive_image',
    '#uri' => $variables['uri'],
    '#responsive_image_style_id' => $variables['responsive_image_style_id'],
    '#attributes' => [
      'alt' => $variables['alt'],
    ],
  ];
  $variables['photoswipe_image_url'] = file_url_transform_relative($image_style->buildUrl($variables['uri']));
  $variables['attributes']['class'][] = 'photoswipe';
  $variables['attributes']['data-overlay-title'] = $variables['alt'];
  $variables['attributes']['data-size'] = $image_file->getWidth() . 'x' . $image_file->getHeight();

  \Drupal::service('photoswipe.assets_manager')->attach($variables);

  $cache = new CacheableMetadata();
  $cache->addCacheableDependency($image_style);
  $cache->applyTo($variables);
}
