<?php

namespace Drupal\druki\Drupal;

use Drupal\Core\State\StateInterface;

/**
 * Provides class that store information about Drupal releases.
 */
final class DrupalReleases {

  /**
   * The cache tag used to update last stable release information.
   */
  public const CACHE_TAG = 'druki_last_stable_release';

  /**
   * The state key with stored information about releases.
   */
  protected const STATE_KEY = 'druki.drupal_releases';

  /**
   * The key/value store.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a new DrupalReleases object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The key/value store.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * Gets current information about releases.
   *
   * @return array
   *   An array with:
   *   - expires: Timestamp when until which stored information is valid.
   *   - last_stable_release: The last stable release version.
   *   - last_minor_release: The last minor release version.
   */
  public function get(): array {
    return $this->state->get(self::STATE_KEY, $this->getDefaultValue());
  }

  /**
   * Sets current information about releases.
   *
   * @param array $releases_info
   *   An array with releases info.
   */
  public function set(array $releases_info): void {
    $this->state->set(self::STATE_KEY, $releases_info);
  }

  /**
   * Gets default value.
   *
   * @return array
   *   An array with default value.
   */
  protected function getDefaultValue(): array {
    return [
      'expires' => 0,
      'last_stable_release' => NULL,
      'last_minor_release' => NULL,
    ];
  }

}
