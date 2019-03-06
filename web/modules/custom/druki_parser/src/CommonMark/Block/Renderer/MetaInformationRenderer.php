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
class MetaInformationRenderer implements BlockRendererInterface {

  /**
   * {@inheritdoc}
   */
  public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = FALSE): HtmlElement {
    $strings = $block->getStrings();
    $content = [];

    foreach ($strings as $string) {
      if (strlen($string)) {
        preg_match_all("/^([a-zA-Z-]+):\s(.*)$/", $string, $matches);
        if (!empty($matches)) {
          $original = $matches[0][0];
          $key = $matches[1][0];
          $value = $matches[2][0];

          $content[] = new HtmlElement('div', [
            'data-druki-key' => $key,
            'data-druki-value' => $value,
          ], $original);
        }
      }

    }

    return new HtmlElement('div', ['data-druki-meta' => ''], $content);
  }

}
