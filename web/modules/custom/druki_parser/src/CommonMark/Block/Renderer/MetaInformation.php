<?php

namespace Drupal\druki_parser\CommonMark\Block\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;

/**
 * Class MetaInformationRenderer
 *
 * @package Drupal\druki_parser\CommonMark\Renderer
 */
class MetaInformation implements BlockRendererInterface {

  /**
   * {@inheritdoc}
   */
  public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = FALSE) {
    return new HtmlElement('div', ['data-druki-meta' => '']);
  }

}
