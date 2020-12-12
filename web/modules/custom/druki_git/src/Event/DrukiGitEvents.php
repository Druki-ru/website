<?php

namespace Drupal\druki_git\Event;

/**
 * Contains all event for druki_git module.
 *
 * @package Drupal\druki_git\Event
 */
final class DrukiGitEvents {

  /**
   * The FINISH_PULL event occurs when "git pull" is finished successfully.
   */
  public const FINISH_PULL = 'druki_git.pull.finish';

}
