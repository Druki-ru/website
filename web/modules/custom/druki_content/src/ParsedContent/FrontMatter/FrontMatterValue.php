<?php

namespace Drupal\druki_content\ParsedContent\FrontMatter;

/**
 * Provides value object for store single Front Matter value.
 */
final class FrontMatterValue {

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
   * FrontMatterValue constructor.
   *
   * @param string $key
   *   The value key.
   * @param mixed $value
   *   The value.
   */
  public function __construct(string $key, $value) {
    $this->key = $key;
    $this->setValue($value);
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
   * @return mixed
   *   The value.
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * Sets value.
   *
   * @param mixed $value
   *   The value.
   */
  protected function setValue($value): void {
    // @todo improve it. Maybe add type checking, or leave it.
    $this->value = $value;
  }

}
