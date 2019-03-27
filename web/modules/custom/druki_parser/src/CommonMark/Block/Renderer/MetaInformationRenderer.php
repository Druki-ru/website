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
    $strings = $block->getStrings();
    $content = [];

    $yaml_string = implode($strings, PHP_EOL);
    $yaml_array = Yaml::decode($yaml_string);
    foreach ($yaml_array as $key => $value) {
      $content[] = new HtmlElement('div', [
        'data-druki-key' => $key,
        'data-druki-value' => is_array($value) ? implode(', ', $value) : $value,
      ]);
    }

    return new HtmlElement('div', ['data-druki-meta' => ''], $content);
  }

}
