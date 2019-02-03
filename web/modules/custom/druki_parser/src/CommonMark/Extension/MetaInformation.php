<?php

namespace Drupal\druki_parser\CommonMark\Extension;

use League\CommonMark\Extension\Extension;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Class MetaInformationExtension
 *
 * @package Drupal\CommonMark
 */
class MetaInformation extends Extension implements ExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function getBlockParsers() {
    return [
      new \Drupal\druki_parser\CommonMark\Block\Parser\MetaInformationParser(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockRenderers() {
    return [
      'Drupal\druki_parser\CommonMark\Block\Element\MetaInformationElement' => new \Drupal\druki_parser\CommonMark\Block\Renderer\MetaInformationRenderer(),
    ];
  }

}
