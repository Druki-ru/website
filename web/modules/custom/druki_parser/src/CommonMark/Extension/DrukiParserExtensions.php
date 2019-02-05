<?php

namespace Drupal\druki_parser\CommonMark\Extension;

use Drupal\druki_parser\CommonMark\Block\Parser\MetaInformationParser;
use Drupal\druki_parser\CommonMark\Block\Renderer\MetaInformationRenderer;
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
  public function getBlockRenderers() {
    return [
      'Drupal\druki_parser\CommonMark\Block\Element\MetaInformationElement' => new MetaInformationRenderer(),
    ];
  }

}
