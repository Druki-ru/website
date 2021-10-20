<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\druki_content\Data\ContentCodeElement;
use Drupal\druki_content\Data\ContentElementInterface;

/**
 * Provides render array builder for text element.
 */
final class ContentCodeElementRenderArrayBuilder extends ContentElementRenderArrayBuilderBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ContentElementInterface $element): bool {
    return $element instanceof ContentCodeElement;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ContentElementInterface $element, array $children_render_array = []): array {
    \assert($element instanceof ContentCodeElement);
    return [
      '#theme' => 'druki_content_element_code',
      '#content' => [
        '#type' => 'processed_text',
        '#text' => $element->getContent(),
        '#format' => 'basic_html',
      ],
    ];
  }

}
