<?php

namespace Drupal\druki_content\Sync\ParsedContent\FrontMatter;

/**
 * Provides value object for store single Front Matter value.
 */
interface FrontMatterValueInterface {

  /**
   * Gets the key.
   *
   * @return string
   *   The key.
   */
  public function getKey(): string;

  /**
   * Gets the value.
   *
   * @return mixed
   *   The value.
   */
  public function getValue();

}
