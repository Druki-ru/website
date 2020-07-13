<?php

namespace Drupal\druki_markdown\CommonMark\Block\Renderer;

use Drupal\Core\Serialization\Yaml;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;

/**
 * Provides Front Matter renderer.
 */
class FrontMatterRenderer implements BlockRendererInterface {

  /**
   * {@inheritdoc}
   */
  public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = FALSE): HtmlElement {
    $content = [];
    // @phpstan-ignore-next-line
    $yaml_array = Yaml::decode($block->getStringContent());

    foreach ($yaml_array as $key => $value) {
      $content[$key] = $value;
    }

    // We use div instead of script, for safety and problems with DOMDocument.
    // If we create here script, it will be put on <head> while we crawl DOM,
    // and additionally all content parsed as well as DOMDocument.
    // @see https://github.com/symfony/symfony/issues/14542
    return new HtmlElement(
      'div',
      ['data-druki-element' => 'front-matter'],
      json_encode($content)
    );
  }

}
