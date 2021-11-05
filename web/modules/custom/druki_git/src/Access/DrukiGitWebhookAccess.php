<?php

namespace Drupal\druki_git\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\State\StateInterface;
use Drupal\druki_git\Repository\GitSettingsInterface;

/**
 * Access check for webhook route.
 */
class DrukiGitWebhookAccess implements AccessInterface {

  /**
   * The git settings.
   */
  protected GitSettingsInterface $gitSettings;

  /**
   * The key/value state storage.
   */
  protected StateInterface $state;

  /**
   * DrukiGitWebhookAccess constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state system.
   * @param \Drupal\druki_git\Repository\GitSettingsInterface $git_settings
   *   The git settings.
   */
  public function __construct(StateInterface $state, GitSettingsInterface $git_settings) {
    $this->state = $state;
    $this->gitSettings = $git_settings;
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
    if ($key != $this->gitSettings->getWebhookAccessKey()) {
      return AccessResult::forbidden()->setCacheMaxAge(0);
    }
    elseif ($this->state->get('system.maintenance_mode')) {
      return AccessResult::forbidden()->setCacheMaxAge(0);
    }

    return AccessResult::allowed()->setCacheMaxAge(0);
  }

}
