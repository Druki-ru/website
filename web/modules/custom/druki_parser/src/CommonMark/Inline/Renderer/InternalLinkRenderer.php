<?php

namespace Drupal\druki_parser\CommonMark\Inline\Renderer;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

/**
 * Class InternalLinkRenderer
 *
 * @package Drupal\druki_parser\CommonMark\Inline\Renderer
 */
class InternalLinkRenderer implements InlineRendererInterface {

  /**
   * @param AbstractInline $inline
   * @param ElementRendererInterface $html_renderer
   *
   * @return HtmlElement|string
   */
  public function render(AbstractInline $inline, ElementRendererInterface $html_renderer): HtmlElement {
    $attributes = [
      'href' => '@druki_content:' . $inline->getContentId(),
    ];

    // Create element and render its children AST to a string.
    return new HtmlElement('a', $attributes, $html_renderer->renderInlines($inline->children()));
  }

}
