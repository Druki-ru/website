<?php

namespace Drupal\druki\Drupal;


/**
 * Provides interface for classes that store information about Drupal releases.
 */
interface DrupalReleasesInterface {

  /**
   * The cache tag used to update last stable release information.
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
