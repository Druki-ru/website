<?php

namespace Drupal\druki_content\ParsedContent;

use Drupal\druki_content\Entity\DrukiContentInterface;

/**
 * Provides base class for all parsed content item loaders.
 */
abstract class ParsedContentItemLoaderBase implements ParsedContentItemLoaderInterface {

  /**
   * The interface or class that this loader supports.
   *
   * @var string|array
   */
  protected $supportedInterfaceOrClass;

  /**
   * {@inheritdoc}
   */
  public function supportsLoading($data): bool {
    if (!is_object($data)) {
      return FALSE;
    }

    $supported = (array) $this->supportedInterfaceOrClass;

    return (bool) array_filter($supported, function ($name) use ($data) {
      return $data instanceof $name;
    });
  }

  /**
   * {@inheritdoc}
   */
  abstract public function process($data, DrukiContentInterface $content): void;

}
