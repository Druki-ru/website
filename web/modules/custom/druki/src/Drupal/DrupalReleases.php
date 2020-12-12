<?php

namespace Drupal\druki\Drupal;

use Drupal\Core\State\StateInterface;

/**
 * Provides class that store information about Drupal releases.
 */
final class DrupalReleases implements DrupalReleasesInterface {

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
   * {@inheritdoc}
   */
  public function get(): array {
    return $this->state->get(self::STATE_KEY, $this->getDefaultValue());
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

  /**
   * {@inheritdoc}
   */
  public function set(array $releases_info): void {
    $this->state->set(self::STATE_KEY, $releases_info);
  }

}
