<?php

namespace Drupal\druki_parser\CommonMark\Extension;

use Drupal\druki_parser\CommonMark\Block\Parser\MetaInformationParser;
use Drupal\druki_parser\CommonMark\Block\Parser\NoteParser;
use Drupal\druki_parser\CommonMark\Block\Renderer\MetaInformationRenderer;
use Drupal\druki_parser\CommonMark\Inline\Parser\CloseBracerParser;
use Drupal\druki_parser\CommonMark\Inline\Parser\OpenBracerParser;
use Drupal\druki_parser\CommonMark\Inline\Renderer\InternalLinkRenderer;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Class MetaInformationExtension
 *
 * @package Drupal\CommonMark
 */
class DrukiParserExtensions implements ExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function register(ConfigurableEnvironmentInterface $environment): void {
    $environment
      ->addBlockParser(new NoteParser(), 80)
      ->addBlockParser(new MetaInformationParser(), 30)
      ->addBlockRenderer(
        'Drupal\druki_parser\CommonMark\Block\Element\MetaInformationElement',
        new MetaInformationRenderer()
      )
      ->addInlineParser(new OpenBracerParser())
      ->addInlineParser(new CloseBracerParser())
      ->addInlineRenderer(
        'Drupal\druki_parser\CommonMark\Inline\Element\InternalLinkElement',
        new InternalLinkRenderer()
      );
  }

}
