<?php

/**
 * @file
 * File for custom token hooks.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function druki_content_token_info(): array {
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
function druki_content_tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): array {
  $replacements = [];

  if ($type == 'druki_content' && !empty($data['druki_content'])) {
    /** @var \Drupal\druki_content\Entity\DrukiContentInterface $druki_content */
    $druki_content = $data['druki_content'];

    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'computed-alias':
          $path_parts = [];
          // Force /wiki to be in path in every file.
          $path_parts[] = 'wiki';
          $path_parts[] = $druki_content->getSlug();
          $replacements[$original] = '/' . implode('/', $path_parts);
          break;
      }
    }
  }

  return $replacements;
}
