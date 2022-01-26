<?php

namespace Drupal\druki\Repository;

/**
 * Provides an interface for Drupal core release info storage.
 */
interface DrupalCoreVersionInterface {

  /**
   * The cache tag used to update last stable release information.
   *
   * @todo Make DrupalCoreVersion cacheable dependency and move it here.
   */
  public const CACHE_TAG = 'druki_last_stable_release';

  /**
   * Gets current information about releases.
   *
   * @return array
   *   An array with:
   *   - expires: Timestamp when until which stored information is valid.
   *   - last_stable_release: The last stable release version.
   *   - last_minor_release: The last minor release version.
   */
  public function get(): array;

  /**
   * Sets current information about releases.
   *
   * @param array $releases_info
   *   An array with releases info.
   */
  public function set(array $releases_info): void;

}
