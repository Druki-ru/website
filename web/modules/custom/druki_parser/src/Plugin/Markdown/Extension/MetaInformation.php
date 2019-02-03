<?php

namespace Drupal\druki_parser\Plugin\Markdown\Extension;

use Drupal\druki_parser\CommonMark\Extension\MetaInformation as MetaInformationExtension;
use Drupal\markdown\Plugin\Markdown\Extension\CommonMarkExtension;
use League\CommonMark\Environment;
use League\CommonMark\EnvironmentAwareInterface;

/**
 * Class MetaInformation.
 *
 * @MarkdownExtension(
 *   id = "druki_parser_meta_information",
 *   parser = "thephpleague/commonmark",
 *   label = @Translation("Parser for meta information"),
 *   description = @Translation("Parse meta information for entity.")
 * )
 */
class MetaInformation extends CommonMarkExtension implements EnvironmentAwareInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultSettings() {
    return [
      'enabled' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'druki_parser_meta_information';
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(Environment $environment) {
    $environment->addExtension(new MetaInformationExtension());
  }

}
