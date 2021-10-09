<?php

namespace Drupal\druki_content\Sync\ParsedContent\FrontMatter;

/**
 * Provides default front matter value implementation.
 */
final class FrontMatterValue implements FrontMatterValueInterface {

  /**
   * The key.
   */
  private string $key;

  /**
   * The value.
   */
  private mixed $value;

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
   * {@inheritdoc}
   */
  public function getKey(): string {
    return $this->key;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(): mixed {
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
