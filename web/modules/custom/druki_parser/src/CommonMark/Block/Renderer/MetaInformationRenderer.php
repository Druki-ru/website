<?php

namespace Drupal\druki_parser\CommonMark\Block\Renderer;

use Drupal\Core\Serialization\Yaml;
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
    $content = [];
    dump($block->getStringContent());
    $yaml_array = Yaml::decode($block->getStringContent());

    foreach ($yaml_array as $key => $value) {
      $content[] = new HtmlElement('div', [
        'data-druki-key' => $key,
        'data-druki-value' => is_array($value) ? implode(', ', $value) : $value,
      ]);
    }

    return new HtmlElement('div', ['data-druki-meta' => ''], $content);
  }

}
