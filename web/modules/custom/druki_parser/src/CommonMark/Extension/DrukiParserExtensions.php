<?php

namespace Drupal\druki_parser\CommonMark\Extension;

use Drupal\druki_parser\CommonMark\Block\Parser\MetaInformationParser;
use Drupal\druki_parser\CommonMark\Block\Renderer\MetaInformationRenderer;
use Drupal\druki_parser\CommonMark\Inline\Parser\CloseBracerParser;
use Drupal\druki_parser\CommonMark\Inline\Parser\OpenBracerParser;
use Drupal\druki_parser\CommonMark\Inline\Renderer\InternalLinkRenderer;
use League\CommonMark\Extension\Extension;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Class MetaInformationExtension
 *
 * @package Drupal\CommonMark
 */
class DrukiParserExtensions extends Extension implements ExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function getBlockParsers() {
    return [
      new MetaInformationParser(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockRenderers(): array {
    return [
      'Drupal\druki_parser\CommonMark\Block\Element\MetaInformationElement' => new MetaInformationRenderer(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getInlineParsers(): array {
    return [
      new OpenBracerParser(),
      new CloseBracerParser(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getInlineRenderers(): array {
    return [
      'Drupal\druki_parser\CommonMark\Inline\Element\InternalLinkElement' => new InternalLinkRenderer(),
    ];
  }

}
