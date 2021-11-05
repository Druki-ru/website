<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\druki_content\Data\ContentElementInterface;
use Drupal\druki_content\Data\ContentNoteElement;

/**
 * Provides render array builder for note element.
 */
final class ContentAsideElementRenderArrayBuilder extends ContentElementRenderArrayBuilderBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ContentElementInterface $element): bool {
    return $element instanceof ContentNoteElement;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ContentElementInterface $element, array $children_render_array = []): array {
    \assert($element instanceof ContentNoteElement);
    return [
      '#theme' => 'druki_content_element_aside',
      '#aside_type' => $element->getType(),
      '#content' => $children_render_array,
    ];
  }

}
