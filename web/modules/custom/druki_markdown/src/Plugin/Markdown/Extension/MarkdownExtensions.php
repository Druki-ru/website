<?php

namespace Drupal\druki_markdown\Plugin\Markdown\Extension;

use Drupal\druki_markdown\CommonMark\Extension\DrukiParserExtensions;
use Drupal\markdown\Plugin\Markdown\Extension\CommonMarkExtension;
use League\CommonMark\EnvironmentAwareInterface;
use League\CommonMark\EnvironmentInterface;

/**
 * Class MarkdownExtensions.
 *
 * @MarkdownExtension(
 *   id = "druki_markdown_extensions",
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
    return 'druki_markdown_extensions';
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $environment->addExtension(new DrukiParserExtensions());
  }

}
