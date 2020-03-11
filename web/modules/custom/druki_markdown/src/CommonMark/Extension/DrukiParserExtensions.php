<?php

namespace Drupal\druki_markdown\CommonMark\Extension;

use Drupal\druki_markdown\CommonMark\Block\Parser\FrontMatterParser;
use Drupal\druki_markdown\CommonMark\Block\Parser\NoteParser;
use Drupal\druki_markdown\CommonMark\Block\Renderer\FrontMatterRenderer;
use Drupal\druki_markdown\CommonMark\Block\Renderer\NoteRenderer;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Provides class with custom Common Mark extensions.
 */
final class DrukiParserExtensions implements ExtensionInterface {

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
      ->addBlockParser(new FrontMatterParser(), 30)
      ->addBlockRenderer(
        'Drupal\druki_markdown\CommonMark\Block\Element\FrontMatterElement',
        new FrontMatterRenderer()
      );
  }

}
