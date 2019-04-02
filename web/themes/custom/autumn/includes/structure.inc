<?php

/**
 * @file
 * Theme and preprocess functions for page, html, etc
 */

use Drupal\image\ImageStyleInterface;
use Drupal\media\MediaInterface;

/**
 * Implements hook_preprocess_HOOK() for page.html.twig.
 */
function autumn_preprocess_page(array &$variables) {
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
}

/**
 * Implements hook_preprocess_HOOK() for page--front.html.twig.
 */
function autumn_preprocess_page__front(array &$variables) {
  $variables['promo_image_url'] = NULL;

  $frontpage_settings = \Drupal::config('druki.frontpage_settings');
  /** @var \Drupal\media\MediaStorage $media_storage */
  $media_storage = \Drupal::entityTypeManager()->getStorage('media');
  /** @var \Drupal\Core\Entity\EntityStorageInterface $image_style_storage */
  $image_style_storage = \Drupal::entityTypeManager()->getStorage('image_style');
  $promo_settings = $frontpage_settings->get('promo');

  $promo_media = $media_storage->load($promo_settings['image']);
  $promo_style = $image_style_storage->load($promo_settings['style']);
  // Check is media and style is really existed.
  if ($promo_media instanceof MediaInterface && $promo_style instanceof ImageStyleInterface) {
    /** @var \Drupal\file\FileInterface $file */
    $file = $promo_media->get('field_media_image')->entity;
    $variables['promo_image_url'] = $promo_style->buildUrl($file->getFileUri());
  }
}
