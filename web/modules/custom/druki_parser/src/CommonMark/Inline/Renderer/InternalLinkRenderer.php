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
   * @param ElementRendererInterface $htmlRenderer
   *
   * @return HtmlElement|string
   */
  public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer) {
    // TODO: Implement render() method.
  }

}
