<?php

namespace Drupal\druki\Aggregator;

/**
 * Provides an interface for Drupal core version aggregator.
 */
interface DrupalCoreVersionAggregatorInterface {

  /**
   * Gets last minor version info for Drupal core.
   *
   * @return array|null
   *   The version info.
   */
  public function getCoreLastMinorVersion(): ?array;

  /**
   * Gets project last stable release.
   *
   * @return array|null
   *   The last stable release version info, NULL if something wrong happens or
   *   stable release is missing.
   */
  public function getCoreLastStableVersion(): ?array;

}
