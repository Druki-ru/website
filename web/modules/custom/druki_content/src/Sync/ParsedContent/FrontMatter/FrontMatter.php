<?php

namespace Drupal\druki_content\Sync\ParsedContent\FrontMatter;

/**
 * Provides value object for Front Matter implementation.
 */
final class FrontMatter implements FrontMatterInterface {

  /**
   * The meta values.
   *
   * @var array
   */
  private $values = [];

  /**
   * {@inheritdoc}
   */
  public function add(FrontMatterValueInterface $value): FrontMatterInterface {
    $this->values[] = $value;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getValues(): array {
    return $this->values;
  }

  /**
   * {@inheritdoc}
   */
  public function get(string $key): ?FrontMatterValueInterface {
    /** @var \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValue $value */
    foreach ($this->values as $value) {
      if ($value->getKey() == $key) {
        return $value;
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function valid(): bool {
    $required_values = [
      'id',
      'title',
    ];

    foreach ($required_values as $required_value) {
      if (!$this->has($required_value)) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function has(string $key): bool {
    /** @var \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValue $value */
    foreach ($this->values as $value) {
      if ($value->getKey() == $key) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
