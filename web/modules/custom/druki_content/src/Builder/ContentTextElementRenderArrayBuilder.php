<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\Core\Render\Markup;
use Drupal\druki_content\Data\ContentElementInterface;
use Drupal\druki_content\Data\ContentTextElement;

/**
 * Provides render array builder for text element.
 */
final class ContentTextElementRenderArrayBuilder extends ContentElementRenderArrayBuilderBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ContentElementInterface $element): bool {
    return $element instanceof ContentTextElement;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ContentElementInterface $element, array $children_render_array = []): array {
    \assert($element instanceof ContentTextElement);
    return [
      '#theme' => 'druki_content_element_text',
      '#content' => Markup::create($element->getContent()),
    ];
  }

}
