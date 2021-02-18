<?php

namespace Drupal\druki_content\Sync\ParsedContent\FrontMatter;

/**
 * Provides value object for Front Matter storage.
 */
interface FrontMatterInterface {

  /**
   * Adds new value to meta information.
   *
   * @param \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValueInterface $value
   *   The value.
   *
   * @return $this
   */
  public function add(FrontMatterValueInterface $value): FrontMatterInterface;

  /**
   * Gets all values added to meta information.
   *
   * @return \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValue[]
   *   An array containing all meta values.
   */
  public function getValues(): array;

  /**
   * Gets value for specific key.
   *
   * @param string $key
   *   The key to get.
   *
   * @return \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValueInterface|null
   *   The meta value instance, NULL if not found.
   */
  public function get(string $key): ?FrontMatterValueInterface;

  /**
   * Checks is meta information is in valid state.
   *
   * Valid meta information is that contains all minimum required values for
   * create content.
   *
   * @return bool
   *   TRUE is valid, FALSE if one of required values is missing.
   */
  public function valid(): bool;

  /**
   * Checks is meta information has value for provided key.
   *
   * @param string $key
   *   The key to search.
   *
   * @return bool
   *   The result of search, TRUE if found, FALSE otherwise.
   */
  public function has(string $key): bool;

}
