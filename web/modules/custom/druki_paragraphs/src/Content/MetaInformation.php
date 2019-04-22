<?php

namespace Drupal\druki_paragraphs\Content;

/**
 * Class MetaInformation.
 *
 * The meta information values.
 *
 * @package Drupal\druki_paragraphs\Content
 */
final class MetaInformation {

  /**
   * The meta values.
   *
   * @var array
   */
  protected $values = [];

  /**
   * Adds new value to meta information.
   *
   * @param \Drupal\druki_paragraphs\Content\MetaValue $value
   *   The value.
   *
   * @return \Drupal\druki_paragraphs\Content\MetaInformation
   *   The current instance.
   */
  public function add(MetaValue $value): MetaInformation {
    $this->values[] = $value;

    return $this;
  }

  /**
   * Gets all values added to meta information.
   *
   * @return \Drupal\druki_paragraphs\Content\MetaValue[]
   *   An array containing all meta values.
   */
  public function getValues(): array {
    return $this->values;
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
    /** @var \Drupal\druki_paragraphs\Content\MetaValue $value */
    foreach ($this->values as $value) {
      if ($value->getKey() == $key) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Gets value for specific key.
   *
   * @param string $key
   *   The key to get.
   *
   * @return \Drupal\druki_paragraphs\Content\MetaValue|null
   *   The meta value instance, NULL if not found.
   */
  public function get(string $key): ?MetaValue {
    /** @var \Drupal\druki_paragraphs\Content\MetaValue $value */
    foreach ($this->values as $value) {
      if ($value->getKey() == $key) {
        return $value;
      }
    }

    return NULL;
  }

}
