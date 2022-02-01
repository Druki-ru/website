<?php

declare(strict_types=1);

namespace Drupal\druki\Markdown\CommonMark\Block\Renderer;

use Drupal\druki\Markdown\CommonMark\Block\Element\AsideElement;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;

/**
 * Provides renderer for <Aside> element.
 */
final class AsideRenderer implements BlockRendererInterface {

  /**
   * {@inheritdoc}
   */
  public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, bool $inTightList = FALSE) {
    \assert($block instanceof AsideElement);
    $child_content = $htmlRenderer->renderBlocks($block->children());

    $aside_attributes = [
      // @see https://www.digitala11y.com/note-role/
      'role' => 'note',
      'data-type' => $block->getType(),
    ];

    if ($header = $block->getHeader()) {
      $aside_attributes['data-header'] = $header;
    }

    return new HtmlElement('aside', $aside_attributes, $child_content);
  }

}
