<?php

namespace Drupal\druki\Markdown\CommonMark\Extension;

use Drupal\druki\Markdown\CommonMark\Block\Parser\FrontMatterParser;
use Drupal\druki\Markdown\CommonMark\Block\Parser\NoteParser;
use Drupal\druki\Markdown\CommonMark\Block\Renderer\FrontMatterRenderer;
use Drupal\druki\Markdown\CommonMark\Block\Renderer\NoteRenderer;
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
        'Drupal\druki\Markdown\CommonMark\Block\Element\NoteElement',
        new NoteRenderer(),
      );
  }

}
