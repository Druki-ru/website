<?php

namespace Drupal\druki_markdown\CommonMark\Extension;

use Drupal\druki_markdown\CommonMark\Block\Parser\MetaInformationParser;
use Drupal\druki_markdown\CommonMark\Block\Parser\NoteParser;
use Drupal\druki_markdown\CommonMark\Block\Renderer\MetaInformationRenderer;
use Drupal\druki_markdown\CommonMark\Block\Renderer\NoteRenderer;
use Drupal\druki_markdown\CommonMark\DocumentProcessor\InternalLinkProcessor;
use Drupal\druki_markdown\CommonMark\Inline\Parser\CloseBracerParser;
use Drupal\druki_markdown\CommonMark\Inline\Parser\OpenBracerParser;
use Drupal\druki_markdown\CommonMark\Inline\Renderer\InternalLinkRenderer;
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
      ->addBlockRenderer(
        'Drupal\druki_markdown\CommonMark\Block\Element\NoteElement',
        new NoteRenderer()
      )
      ->addBlockParser(new MetaInformationParser(), 30)
      ->addBlockRenderer(
        'Drupal\druki_markdown\CommonMark\Block\Element\MetaInformationElement',
        new MetaInformationRenderer()
      )
      ->addInlineParser(new OpenBracerParser())
      ->addInlineParser(new CloseBracerParser())
      ->addInlineRenderer(
        'Drupal\druki_markdown\CommonMark\Inline\Element\InternalLinkElement',
        new InternalLinkRenderer()
      );
  }

}
