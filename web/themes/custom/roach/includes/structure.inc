<?php

/**
 * @file
 * Theme and preprocess functions for page, html, etc
 */

use Drupal\image\ImageStyleInterface;
use Drupal\media\MediaInterface;

/**
 * Implements hook_preprocess_HOOK() for page.html.twig.
 *
 * @Example. You can safely remove this hook completely.
 */
function roach_preprocess_page(array &$variables) {
  /** @var \Drupal\Core\Template\Attribute $main_layout_attributes */
  $main_layout_attributes = &$variables['main_layout_attributes'];

  // Set default class.
  $main_layout_attributes->addClass('main-layout');

  // Handle sidebar modifiers.
  if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
    $main_layout_attributes->addClass('main-layout--two-sidebars');
  }
  elseif (!empty($variables['page']['sidebar_first'])) {
    $main_layout_attributes->addClass('main-layout--sidebar-first');
  }
  elseif (!empty($variables['page']['sidebar_second'])) {
    $main_layout_attributes->addClass('main-layout--sidebar-second');
  }
  else {
    $main_layout_attributes->addClass('main-layout--no-sidebars');
  }

  // Some optimizations.
  $preload_mdi = [
    '#tag' => 'link',
    '#attributes' => [
      'rel' => 'preload',
      'as' => 'font',
      'href' => '/themes/custom/svzar_theme/node_modules/mdi/fonts/materialdesignicons-webfont.woff2',
      'type' => 'font/woff2',
      'crossorigin' => 'anonymous',
    ],
    '#weight' => -100,
  ];

  $google_font_styles = [
    '#tag' => 'link',
    '#attributes' => [
      'rel' => 'preconnect',
      'href' => 'https://fonts.googleapis.com/',
    ],
    '#weight' => -99,
  ];

  $google_font_files = [
    '#tag' => 'link',
    '#attributes' => [
      'rel' => 'preconnect',
      'href' => 'https://fonts.gstatic.com/',
    ],
    '#weight' => -98,
  ];

  $gtm = [
    '#tag' => 'link',
    '#attributes' => [
      'rel' => 'preconnect',
      'href' => 'https://www.googletagmanager.com/',
    ],
    '#weight' => -97,
  ];

  $variables['#attached']['html_head'][] = [$preload_mdi, 'preload_mdi'];
  $variables['#attached']['html_head'][] = [$google_font_styles, 'google_font_styles'];
  $variables['#attached']['html_head'][] = [$google_font_files, 'google_font_files'];
  $variables['#attached']['html_head'][] = [$gtm, 'google_tag_manager'];
}

/**
 * Implements hook_preprocess_HOOK() for page--front.html.twig.
 */
function roach_preprocess_page__front(array &$variables): void {
  $variables['promo_image_url'] = NULL;
  $variables['why_drupal_video'] = NULL;

  $frontpage_settings = \Drupal::config('druki.frontpage_settings');
  /** @var \Drupal\media\MediaStorage $media_storage */
  $media_storage = \Drupal::entityTypeManager()->getStorage('media');
  $media_view_builder = \Drupal::entityTypeManager()->getViewBuilder('media');
  /** @var \Drupal\Core\Entity\EntityStorageInterface $image_style_storage */
  $image_style_storage = \Drupal::entityTypeManager()->getStorage('image_style');
  $promo_settings = $frontpage_settings->get('promo');
  $why_settings = $frontpage_settings->get('why');

  // Promo area.
  $promo_image = $media_storage->load($promo_settings['image']);
  $promo_style = $image_style_storage->load($promo_settings['style']);
  // Check is media and style is really existed.
  if ($promo_image instanceof MediaInterface && $promo_style instanceof ImageStyleInterface) {
    /** @var \Drupal\file\FileInterface $file */
    $file = $promo_image->get('field_media_image')->entity;
    $variables['promo_image_url'] = $promo_style->buildUrl($file->getFileUri());
  }

  // Why Drupal area.
  $why_video = $media_storage->load($why_settings['video']);
  // Check is video exists.
  if ($why_video instanceof MediaInterface) {
    $variables['why_drupal_video'] = $media_view_builder->view($why_video);
  }
}
