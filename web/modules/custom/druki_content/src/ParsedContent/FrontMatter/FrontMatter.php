<?php

namespace Drupal\druki_content\ParsedContent\FrontMatter;

/**
 * Provides value object for Front Matter storage.
 */
final class FrontMatter {

  /**
   * The meta values.
   *
   * @var array
   */
  private $values = [];

  /**
   * Adds new value to meta information.
   *
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatterValue $value
   *   The value.
   *
   * @return $this
   */
  public function add(FrontMatterValue $value): FrontMatter {
    $this->values[] = $value;

    return $this;
  }

  /**
   * Gets all values added to meta information.
   *
   * @return \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatterValue[]
   *   An array containing all meta values.
   */
  public function getValues(): array {
    return $this->values;
  }

  /**
   * Gets value for specific key.
   *
   * @param string $key
   *   The key to get.
   *
   * @return \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatterValue|null
   *   The meta value instance, NULL if not found.
   */
  public function get(string $key): ?FrontMatterValue {
    /** @var \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatterValue $value */
    foreach ($this->values as $value) {
      if ($value->getKey() == $key) {
        return $value;
      }
    }

    return NULL;
  }

  /**
   * Checks is meta information is in valid state.
   *
   * Valid meta information is that contains all minimum required values for
   * create content.
   *
   * @return bool
   *   TRUE is valid, FALSE if one of required values is missing.
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
   * Checks is meta information has value for provided key.
   *
   * @param string $key
   *   The key to search.
   *
   * @return bool
   *   The result of search, TRUE if found, FALSE otherwise.
   */
  public function has(string $key): bool {
    /** @var \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatterValue $value */
    foreach ($this->values as $value) {
      if ($value->getKey() == $key) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
