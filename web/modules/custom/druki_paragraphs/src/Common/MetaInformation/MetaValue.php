<?php

namespace Drupal\druki_paragraphs\Common\MetaInformation;

/**
 * Class MetaValue.
 *
 * Contains value for MetaInformation.
 *
 * @package Drupal\druki_paragraphs\Common\MetaInformation
 */
final class MetaValue {

  /**
   * The key.
   *
   * @var string
   */
  private $key;

  /**
   * The value.
   *
   * @var string
   */
  private $value;

  /**
   * MetaValue constructor.
   *
   * @param string $key
   *   The value key.
   * @param string $value
   *   The value.
   */
  public function __construct(string $key, string $value) {
    $this->key = $key;
    $this->value = $value;
  }

  /**
   * Gets the key.
   *
   * @return string
   *   The key.
   */
  public function getKey(): string {
    return $this->key;
  }

  /**
   * Gets the value.
   *
   * @return string
   *   The value.
   */
  public function getValue(): string {
    return $this->value;
  }

}
