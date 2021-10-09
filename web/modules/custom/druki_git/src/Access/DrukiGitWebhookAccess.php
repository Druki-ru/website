<?php

namespace Drupal\druki_git\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\State\StateInterface;

/**
 * Access check for webhook route.
 */
class DrukiGitWebhookAccess implements AccessInterface {

  /**
   * The state system.
   */
  protected StateInterface $state;

  /**
   * DrukiGitWebhookAccess constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state system.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * Checks access.
   *
   * @param string $key
   *   The webhook key.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(string $key): AccessResultInterface {
    if ($key != $this->state->get('druki_git.webhook_key')) {
      return AccessResult::forbidden()->setCacheMaxAge(0);
    }
    elseif ($this->state->get('system.maintenance_mode')) {
      return AccessResult::forbidden()->setCacheMaxAge(0);
    }

    return AccessResult::allowed()->setCacheMaxAge(0);
  }

}
