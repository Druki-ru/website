<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\druki_content\Data\ContentAsideElement;
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
    $is_note = $element instanceof ContentNoteElement;
    $is_aside = $element instanceof ContentAsideElement;
    return $is_aside || $is_note;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ContentElementInterface $element, array $children_render_array = []): array {
    $aside_header = NULL;
    // @todo Remove when support for ContentNoteElement is gone.
    if ($element instanceof ContentAsideElement) {
      $aside_header = $element->getHeader();
    }
    return [
      '#theme' => 'druki_content_element_aside',
      '#aside_type' => $element->getType(),
      '#aside_header' => $aside_header,
      '#content' => $children_render_array,
    ];
  }

}
