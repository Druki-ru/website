<?php

namespace Drupal\druki_parser\Plugin\Markdown\Extension;

use Drupal\druki_parser\CommonMark\Extension\DrukiParserExtensions;
use Drupal\markdown\Plugin\Markdown\Extension\CommonMarkExtension;
use League\CommonMark\Environment;
use League\CommonMark\EnvironmentAwareInterface;

/**
 * Class MarkdownExtensions.
 *
 * @MarkdownExtension(
 *   id = "druki_parser_markdown_extensions",
 *   parser = "thephpleague/commonmark",
 *   label = @Translation("Additional markdown extensions"),
 *   description = @Translation("Our custom markdown syntax support.")
 * )
 */
class MarkdownExtensions extends CommonMarkExtension implements EnvironmentAwareInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultSettings(): array {
    return [
      'enabled' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'druki_parser_markdown_extensions';
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(Environment $environment): void {
    $environment->addExtension(new DrukiParserExtensions());
  }

}
