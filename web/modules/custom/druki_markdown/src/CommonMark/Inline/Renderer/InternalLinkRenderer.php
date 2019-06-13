<?php

namespace Drupal\druki_markdown\CommonMark\Inline\Renderer;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

/**
 * Class InternalLinkRenderer
 *
 * @package Drupal\druki_markdown\CommonMark\Inline\Renderer
 *
 * @deprecated in flavor of https://gitlab.com/druki/website/issues/32
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
