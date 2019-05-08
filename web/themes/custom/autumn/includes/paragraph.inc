<?php

/**
 * @file
 * Custom preprocess functions for paragraph entity.
 */

use Drupal\druki\Utility\Text;

/**
 * Implements hook_preprocess_HOOK() for paragraph--druki-heading.html.twig.
 */
function autumn_preprocess_paragraph__druki_heading(array &$variables): void {
  /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
  $paragraph = $variables['paragraph'];

  $title = strip_tags($paragraph->get('druki_textfield_formatted')->processed, '<code><strong><em>');

  $variables['heading_level'] = $paragraph->get('druki_heading_level')->value;
  $variables['title'] = $title;

  $variables['anchor'] = Text::anchor($title, 'druki_content_toc_theme');
}

/**
 * Implements hook_preprocess_HOOK() for paragraph--druki-code.html.twig.
 */
function autumn_preprocess_paragraph__druki_code(array &$variables): void {
  $variables['#attached']['library'][] = 'autumn/code_highlight';
}

/**
 * Implements hook_preprocess_HOOK() for paragraph--druki-note.html.twig.
 */
function autumn_preprocess_paragraph__druki_note(array &$variables): void {
  /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
  $paragraph = $variables['paragraph'];
  $druki_note_type = $paragraph->get('druki_note_type');
  $types = $druki_note_type
    ->getFieldDefinition()
    ->getFieldStorageDefinition()
    ->getSetting('allowed_values');

  $variables['note_type_label'] = $types[$druki_note_type->value];
}
