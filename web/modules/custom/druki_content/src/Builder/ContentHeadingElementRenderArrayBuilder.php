<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\druki_content\Data\ContentElementInterface;
use Drupal\druki_content\Data\ContentHeadingElement;

/**
 * Provides render array builder for heading element.
 */
final class ContentHeadingElementRenderArrayBuilder extends ContentElementRenderArrayBuilderBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ContentElementInterface $element): bool {
    return $element instanceof ContentHeadingElement;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ContentElementInterface $element, array $children_render_array = []): array {
    \assert($element instanceof ContentHeadingElement);
    return [
      '#theme' => 'druki_content_element_heading',
      '#level' => $element->getLevel(),
      '#content' => [
        '#type' => 'processed_text',
        '#text' => $element->getContent(),
        '#format' => 'basic_html',
      ],
    ];
  }

}
