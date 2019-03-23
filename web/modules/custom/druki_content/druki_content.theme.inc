<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

use Drupal\Core\Render\Element;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Prepares variables for druki content templates.
 *
 * Default template: druki-content.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the druki content information
 *   and any fields attached to the entity.
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_druki_content(array &$variables): void {
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }

  $variables['druki_content'] = $variables['elements']['#druki_content'];
  $variables['view_mode'] = $variables['elements']['#view_mode'];
}

/**
 * Implements hook_preprocess_HOOK() for druki-content-toc.html.twig.
 */
function template_preprocess_druki_content_toc(array &$variables): void {
  /** @var \Drupal\druki_content\Entity\DrukiContentInterface $druki_content */
  $druki_content = $variables['druki_content'];
  $variables['links'] = [];
  $links = &$variables['links'];
  $headings = $druki_content->get('content')->filter(function ($item) {
    return $item->entity->bundle() == 'druki_heading';
  });

  if (!$headings->isEmpty()) {
    foreach ($headings as $item) {
      /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
      $paragraph = $item->entity;
      $links[] = _druki_content_toc_prepare_link($paragraph);
    }
  }
}

function _druki_content_toc_prepare_link(ParagraphInterface $paragraph): array {
  $title = $paragraph->get('druki_textfield_formatted')->value;
  $heading_level = $paragraph->get('druki_heading_level')->value;

  $anchor = Drupal::transliteration()->transliterate($title);
  $anchor = strtolower($anchor);
  $anchor = trim($anchor);
  // Replace all spaces with dash.
  $anchor = preg_replace("/[\s_]/", '-', $anchor);
  // Remove everything else. Only alphabet and dash is allowed.
  $anchor = preg_replace("/[^a-z-]/", '', $anchor);

  switch ($heading_level) {
    case 'h2':
    default:
      $heading_level_int = 2;
      break;

    case 'h3':
      $heading_level_int = 3;
      break;

    case 'h4':
      $heading_level_int = 4;
      break;

    case 'h5':
      $heading_level_int = 5;
      break;

    case 'h6':
      $heading_level_int = 6;
      break;
  }

  return [
    'text' => $title,
    'anchor' => $anchor,
    'level' => $heading_level_int,
  ];
}
