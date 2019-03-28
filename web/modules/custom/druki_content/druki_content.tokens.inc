<?php

/**
 * @file
 * File for custom token hooks.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function druki_content_token_info() {
  $druki_content['computed-alias'] = [
    'name' => t('Computed alias'),
    'description' => t('The computed alias depends on meta data.'),
  ];

  return [
    'tokens' => [
      'druki_content' => $druki_content,
    ],
  ];
}

/**
 * Implements hook_tokens().
 */
function druki_content_tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  if ($type == 'druki_content' && !empty($data['druki_content'])) {
    /** @var \Drupal\druki_content\Entity\DrukiContentInterface $druki_content */
    $druki_content = $data['druki_content'];

    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'computed-alias':
          $path_parts = [];

          if (
            $druki_content->hasField('forced_path')
            && !$druki_content->get('forced_path')->isEmpty()
          ) {
            $path_parts[] = $druki_content->get('forced_path')->value;
          }
          else {
            $path_parts[] = 'docs';

            if (
              $druki_content->hasField('core')
              && !$druki_content->get('core')->isEmpty()
            ) {
              $path_parts[] = $druki_content->get('core')->value;
            }

            $path_parts[] = $druki_content->get('external_id')->value;
          }

          $replacements[$original] = '/' . implode('/', $path_parts);
          break;
      }
    }
  }

  return $replacements;
}
